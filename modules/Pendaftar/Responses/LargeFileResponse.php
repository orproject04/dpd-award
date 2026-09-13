<?php

namespace Modules\Pendaftar\Responses;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class LargeFileResponse extends BinaryFileResponse
{
    /**
     * Factory to build a ready-to-send streaming file response.
     */
    public static function make(
        string $filePath,
        Request $request,
        bool $isDownload = false,
        ?string $downloadFilename = null
    ): self {
        $filename = $downloadFilename ?: basename($filePath);
        $response = new self($filePath);

        // Determine mime type accurately
        $mime = self::determineMimeType($filePath);
        $response->headers->set('Content-Type', $mime);
        $response->headers->set('Accept-Ranges', 'bytes');
        $response->headers->set('Cache-Control', 'private, max-age=86400, must-revalidate');

        // Set Content-Disposition
        $disposition = $isDownload
            ? ResponseHeaderBag::DISPOSITION_ATTACHMENT
            : ResponseHeaderBag::DISPOSITION_INLINE;
        $response->setContentDisposition($disposition, $filename);

        // Prepare Range headers & status codes based on request
        $response->prepare($request);

        return $response;
    }

    /**
     * Mime type detection with robust fallback.
     */
    public static function determineMimeType(string $filePath): string
    {
        $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        $map = [
            'pdf' => 'application/pdf',
            'png' => 'image/png',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'gif' => 'image/gif',
            'webp' => 'image/webp',
            'svg' => 'image/svg+xml',
            'mp4' => 'video/mp4',
            'webm' => 'video/webm',
            'ogg' => 'video/ogg',
            'mov' => 'video/quicktime',
            'mp3' => 'audio/mpeg',
            'wav' => 'audio/wav',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'doc' => 'application/msword',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'xls' => 'application/vnd.ms-excel',
            'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'ppt' => 'application/vnd.ms-powerpoint',
            'zip' => 'application/zip',
            'rar' => 'application/x-rar-compressed',
            '7z' => 'application/x-7z-compressed',
            'txt' => 'text/plain',
        ];

        if (isset($map[$ext])) {
            return $map[$ext];
        }

        try {
            $detected = File::mimeType($filePath);
            if ($detected && $detected !== 'text/plain') {
                return $detected;
            }
        } catch (\Throwable) {
        }

        return 'application/octet-stream';
    }

    /**
     * Override sendContent to stream chunk-by-chunk without memory exhaustion.
     */
    public function sendContent(): static
    {
        try {
            if (!$this->isSuccessful()) {
                return parent::sendContent();
            }

            if (0 === $this->maxlen) {
                return $this;
            }

            // Clean any active output buffers to prevent buffering into RAM
            while (ob_get_level() > 0) {
                @ob_end_clean();
            }

            // Remove execution and memory limits for large file transfers
            @ini_set('memory_limit', '512M');
            @set_time_limit(0);

            $out = fopen('php://output', 'wb');
            $file = fopen($this->file->getPathname(), 'rb');

            if ($file === false || $out === false) {
                return $this;
            }

            ignore_user_abort(true);

            if (0 !== $this->offset) {
                fseek($file, $this->offset);
            }

            $chunkSize = 1048576; // 1MB chunk size
            $remaining = $this->maxlen;

            while ($remaining > 0 && !feof($file)) {
                $readSize = ($remaining > $chunkSize || $remaining < 0) ? $chunkSize : $remaining;
                $data = fread($file, $readSize);
                if ($data === false || $data === '') {
                    break;
                }

                $written = fwrite($out, $data);
                if ($written === false || connection_aborted()) {
                    break;
                }

                if ($remaining > 0) {
                    $remaining -= $written;
                }

                @flush();
            }

            fclose($file);
            fclose($out);
        } finally {
            if ($this->deleteFileAfterSend && is_file($this->file->getPathname())) {
                @unlink($this->file->getPathname());
            }
        }

        return $this;
    }
}

