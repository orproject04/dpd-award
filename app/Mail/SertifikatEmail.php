<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\Pendaftar as AppPendaftar;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Modules\Pendaftar\Models\Pendaftar as ModulePendaftar;

class SertifikatEmail extends Mailable
{
    use Queueable, SerializesModels;

    public AppPendaftar|ModulePendaftar $pendaftar;
    public string $stageLabel;
    public string $certTitle;
    public string $pdfPath;
    public string $downloadName;

    /**
     * Create a new message instance.
     */
    public function __construct(
        AppPendaftar|ModulePendaftar $pendaftar,
        string $stageLabel,
        string $certTitle,
        string $pdfPath,
        string $downloadName
    ) {
        $this->pendaftar = $pendaftar;
        $this->stageLabel = $stageLabel;
        $this->certTitle = $certTitle;
        $this->pdfPath = $pdfPath;
        $this->downloadName = $downloadName;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->certTitle . ' - DPDRI Awards 2026 (' . $this->pendaftar->nomor_registrasi . ')',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.sertifikat',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        if (!is_file($this->pdfPath)) {
            return [];
        }

        return [
            Attachment::fromPath($this->pdfPath)
                ->as($this->downloadName)
                ->withMime('application/pdf'),
        ];
    }
}

