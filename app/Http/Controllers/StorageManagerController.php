<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\Permission;
use App\Models\Pendaftar;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Modules\Pendaftar\Responses\LargeFileResponse;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use ZipArchive;

class StorageManagerController extends Controller
{
    /**
     * Ekstensi berkas berbahaya yang diblokir dari unggahan / rename / ekstraksi.
     *
     * @var array<int, string>
     */
    protected const BLOCKED_EXTENSIONS = [
        'php', 'phtml', 'phar', 'php3', 'php4', 'php5', 'php7', 'php8', 'phps',
        'htaccess', 'htpasswd', 'sh', 'bash', 'zsh', 'exe', 'bat', 'cmd', 'com',
        'msi', 'vbs', 'vbe', 'ps1', 'psm1', 'cgi', 'pl', 'py', 'rb', 'jsp',
        'asp', 'aspx', 'dll', 'so', 'scr', 'pif',
    ];

    /**
     * Berkas metadata OS yang dilewati secara aman saat unggah folder atau ekstrak ZIP.
     *
     * @var array<int, string>
     */
    protected const IGNORED_SYSTEM_FILES = [
        '.DS_Store',
        'Thumbs.db',
        'desktop.ini',
    ];

    /**
     * Memastikan pengguna memiliki permission STORAGE_MANAGE atau wildcard (*).
     */
    protected function authorizeAccess(): void
    {
        $user = auth()->user();
        if (!$user || !($user->hasPermission('*') || $user->hasPermission(Permission::STORAGE_MANAGE))) {
            abort(403, 'Anda tidak memiliki akses ke menu Manajemen Storage.');
        }
    }

    /**
     * Mendapatkan path fisik absolut dari direktori root storage/app/private/pendaftar.
     */
    protected function getRealRoot(): string
    {
        $root = storage_path('app/private/pendaftar');
        if (!is_dir($root)) {
            @mkdir($root, 0755, true);
        }

        $realRoot = realpath($root);
        if ($realRoot === false || !is_dir($realRoot)) {
            abort(500, 'Direktori storage pendaftar tidak dapat diakses.');
        }

        return $realRoot;
    }

    /**
     * Menormalisasi jalur relatif dan menolak percobaan directory traversal.
     */
    protected function normalizeRelativePath(?string $path): string
    {
        $path = str_replace("\0", '', (string) $path);
        $path = str_replace('\\', '/', $path);
        $path = trim($path, '/');

        if ($path === '') {
            return '';
        }

        $segments = explode('/', $path);
        foreach ($segments as $segment) {
            if ($segment === '' || $segment === '.' || $segment === '..' || str_starts_with($segment, '.')) {
                abort(403, 'Jalur direktori atau nama berkas tidak diizinkan.');
            }
            if (preg_match('/[<>:"|?*\x00-\x1f]/', $segment)) {
                abort(403, 'Karakter tidak valid pada jalur berkas.');
            }
        }

        return implode('/', $segments);
    }

    /**
     * Memvalidasi dan menyelesaikan path relatif ke realpath absolut yang dijamin berada di dalam root.
     *
     * @return array{0: string, 1: string} [cleanRelativePath, realAbsolutePath]
     */
    protected function resolveExistingPath(?string $relativePath, bool $allowRoot = false): array
    {
        $realRoot = $this->getRealRoot();
        $cleanPath = $this->normalizeRelativePath($relativePath);

        if ($cleanPath === '') {
            if (!$allowRoot) {
                abort(403, 'Operasi ini tidak dapat dilakukan pada direktori utama.');
            }

            return ['', $realRoot];
        }

        $candidate = $realRoot . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $cleanPath);

        if (!file_exists($candidate) && !is_link($candidate)) {
            abort(404, 'Berkas atau folder tidak ditemukan.');
        }

        $realPath = realpath($candidate);
        if ($realPath === false || (!str_starts_with($realPath, $realRoot . DIRECTORY_SEPARATOR) && $realPath !== $realRoot)) {
            abort(403, 'Akses ditolak: jalur berada di luar direktori storage pendaftar.');
        }

        return [$cleanPath, $realPath];
    }

    /**
     * Memvalidasi nama berkas atau folder tunggal (tanpa separator path).
     */
    protected function validateSingleSegmentName(string $name, bool $isFile = false): string
    {
        $name = trim(str_replace("\0", '', $name));

        if ($name === '' || $name === '.' || $name === '..' || str_starts_with($name, '.')) {
            throw ValidationException::withMessages([
                'name' => 'Nama berkas atau folder tidak valid.',
            ]);
        }

        if (preg_match('/[\/\\\\<>:"|?*\x00-\x1f]/', $name)) {
            throw ValidationException::withMessages([
                'name' => 'Nama tidak boleh mengandung karakter / \\ < > : " | ? *',
            ]);
        }

        $baseUpper = strtoupper(pathinfo($name, PATHINFO_FILENAME));
        $reserved = [
            'CON', 'PRN', 'AUX', 'NUL',
            'COM1', 'COM2', 'COM3', 'COM4', 'COM5', 'COM6', 'COM7', 'COM8', 'COM9',
            'LPT1', 'LPT2', 'LPT3', 'LPT4', 'LPT5', 'LPT6', 'LPT7', 'LPT8', 'LPT9',
        ];
        if (in_array($baseUpper, $reserved, true)) {
            throw ValidationException::withMessages([
                'name' => 'Nama tersebut merupakan nama sistem yang dicadangkan.',
            ]);
        }

        if ($isFile && $this->isBlockedExtension($name)) {
            throw ValidationException::withMessages([
                'name' => 'Jenis/ekstensi berkas "' . pathinfo($name, PATHINFO_EXTENSION) . '" tidak diizinkan demi keamanan.',
            ]);
        }

        return $name;
    }

    protected function isBlockedExtension(string $filename): bool
    {
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        return $ext !== '' && in_array($ext, self::BLOCKED_EXTENSIONS, true);
    }

    /**
     * Halaman utama File & Folder Manager untuk storage/app/private/pendaftar.
     */
    public function index(Request $request)
    {
        $this->authorizeAccess();

        $realRoot = $this->getRealRoot();
        [$currentPath, $realCurrentDir] = $this->resolveExistingPath($request->query('path', ''), true);

        if (!is_dir($realCurrentDir)) {
            abort(404, 'Jalur yang diminta bukan merupakan direktori.');
        }

        $search = trim((string) $request->query('search', ''));
        $scope = $request->query('scope', 'current') === 'global' ? 'global' : 'current';
        $sort = in_array($request->query('sort'), ['name', 'size', 'modified', 'type'], true)
            ? (string) $request->query('sort')
            : 'name';
        $dir = $request->query('dir') === 'desc' ? 'desc' : 'asc';

        // Breadcrumbs
        $breadcrumbs = [
            ['name' => 'pendaftar', 'path' => ''],
        ];
        if ($currentPath !== '') {
            $acc = [];
            foreach (explode('/', $currentPath) as $seg) {
                $acc[] = $seg;
                $breadcrumbs[] = [
                    'name' => $seg,
                    'path' => implode('/', $acc),
                ];
            }
        }

        $parentPath = null;
        if ($currentPath !== '') {
            $parentSegments = explode('/', $currentPath);
            array_pop($parentSegments);
            $parentPath = implode('/', $parentSegments);
        }

        // Scan entries (current folder or recursive search)
        if ($search !== '' && $scope === 'global') {
            $items = $this->searchRecursive($realCurrentDir, $realRoot, $search);
        } else {
            $items = $this->scanCurrentDirectory($realCurrentDir, $realRoot, $search);
        }

        // Apply sorting (directories always stay grouped on top unless explicitly sorting by size/type)
        $items = $this->sortItems($items, $sort, $dir);

        // Statistics of visible listing
        $stats = [
            'total_directories' => 0,
            'total_files' => 0,
            'total_size' => 0,
            'formatted_total_size' => '0 B',
        ];
        foreach ($items as $item) {
            if ($item['type'] === 'directory') {
                $stats['total_directories']++;
            } else {
                $stats['total_files']++;
                $stats['total_size'] += $item['size'];
            }
        }
        $stats['formatted_total_size'] = $this->formatBytes($stats['total_size']);

        $freeSpace = @disk_free_space($realRoot);
        $diskFreeFormatted = $freeSpace !== false ? $this->formatBytes((float) $freeSpace) : '-';

        // Lookup Pendaftar details if visible folder names correspond to nomor_registrasi
        $pendaftarMap = $this->lookupPendaftarLabels($items);

        // Collect all folder paths inside root for sidebar quick tree and Move modal
        $allFolders = $this->collectAllDirectoryPaths($realRoot, $realRoot);

        // Server max single upload hint
        $maxUploadFormatted = $this->formatBytes((float) $this->getMaxUploadBytes());

        return view('storage_manager.index', compact(
            'currentPath',
            'parentPath',
            'breadcrumbs',
            'items',
            'stats',
            'search',
            'scope',
            'sort',
            'dir',
            'diskFreeFormatted',
            'maxUploadFormatted',
            'pendaftarMap',
            'allFolders'
        ));
    }

    /**
     * Mengalirkan file (pratinjau inline atau unduhan attachment) menggunakan LargeFileResponse.
     */
    public function serveFile(Request $request): BinaryFileResponse
    {
        $this->authorizeAccess();

        $path = $request->query('path');
        if (empty($path)) {
            abort(404);
        }

        [, $realPath] = $this->resolveExistingPath((string) $path, false);

        if (!is_file($realPath)) {
            abort(404, 'Berkas tidak ditemukan.');
        }

        $isDownload = $request->has('download') || $request->boolean('download');
        $downloadName = $request->query('name');

        return LargeFileResponse::make(
            $realPath,
            $request,
            $isDownload,
            is_string($downloadName) ? $downloadName : null
        );
    }

    /**
     * Mengunduh seluruh isi folder sebagai arsip ZIP.
     */
    public function downloadZip(Request $request): BinaryFileResponse|RedirectResponse
    {
        $this->authorizeAccess();

        $realRoot = $this->getRealRoot();
        [$cleanPath, $realDir] = $this->resolveExistingPath((string) $request->query('path', ''), true);

        if (!is_dir($realDir)) {
            abort(404, 'Folder tidak ditemukan.');
        }

        $files = [];
        $emptyDirs = [];
        $this->collectArchiveEntries($realDir, $realDir, $realRoot, $files, $emptyDirs);

        if (empty($files) && empty($emptyDirs)) {
            return back()->with('error', 'Folder kosong, tidak ada berkas atau subfolder untuk diunduh.');
        }

        $baseName = $cleanPath === '' ? 'pendaftar_root' : basename($cleanPath);
        $safeBaseName = preg_replace('/[^A-Za-z0-9_\-]/', '_', $baseName) ?: 'storage';
        $zipFileName = 'Storage_' . $safeBaseName . '_' . date('Ymd_His') . '.zip';
        $zipPath = storage_path('app/private/temp_sm_' . Str::random(16) . '.zip');

        $zip = new ZipArchive();
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            abort(500, 'Gagal membuat berkas ZIP.');
        }

        foreach ($emptyDirs as $emptyRel) {
            $zip->addEmptyDir($emptyRel);
        }
        foreach ($files as $relPath => $absPath) {
            if (is_file($absPath)) {
                $zip->addFile($absPath, $relPath);
            }
        }
        $zip->close();

        return response()->download($zipPath, $zipFileName)->deleteFileAfterSend(true);
    }

    /**
     * Mengunduh beberapa item terpilih sekaligus sebagai arsip ZIP.
     */
    public function bulkDownload(Request $request): BinaryFileResponse|RedirectResponse
    {
        $this->authorizeAccess();

        $rawItems = $request->query('items', []);
        if (!is_array($rawItems) || empty($rawItems)) {
            return back()->with('error', 'Pilih minimal satu berkas atau folder untuk diunduh.');
        }

        $realRoot = $this->getRealRoot();
        $resolvedItems = [];
        $parentDirs = [];
        foreach ($rawItems as $rawItem) {
            [$cleanItem, $realItem] = $this->resolveExistingPath((string) $rawItem, false);
            $resolvedItems[] = [$cleanItem, $realItem];
            $parentDirs[dirname($cleanItem)] = true;
        }
        $useFullRelative = count($parentDirs) > 1;

        $files = [];
        $emptyDirs = [];

        foreach ($resolvedItems as [$cleanItem, $realItem]) {
            $entryBase = $useFullRelative ? $cleanItem : basename($cleanItem);

            if (is_dir($realItem)) {
                $emptyDirs[] = $entryBase;
                $this->collectArchiveEntries($realItem, $realItem, $realRoot, $files, $emptyDirs, $entryBase . '/');
            } elseif (is_file($realItem)) {
                $files[$entryBase] = $realItem;
            }
        }

        if (empty($files) && empty($emptyDirs)) {
            return back()->with('error', 'Tidak ada berkas valid yang dapat diunduh.');
        }

        $zipFileName = 'Storage_Selected_' . date('Ymd_His') . '.zip';
        $zipPath = storage_path('app/private/temp_sm_' . Str::random(16) . '.zip');

        $zip = new ZipArchive();
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            abort(500, 'Gagal membuat berkas ZIP.');
        }

        foreach (array_unique($emptyDirs) as $emptyRel) {
            $zip->addEmptyDir($emptyRel);
        }
        foreach ($files as $relPath => $absPath) {
            if (is_file($absPath)) {
                $zip->addFile($absPath, $relPath);
            }
        }
        $zip->close();

        return response()->download($zipPath, $zipFileName)->deleteFileAfterSend(true);
    }

    /**
     * Membuat folder baru (mendukung subfolder bertingkat dipisahkan garis miring '/').
     */
    public function createFolder(Request $request): JsonResponse|RedirectResponse
    {
        $this->authorizeAccess();

        $validated = $request->validate([
            'parent_path' => 'nullable|string|max:500',
            'folder_name' => 'required|string|max:255',
        ]);

        $realRoot = $this->getRealRoot();
        [$cleanParent, $realParent] = $this->resolveExistingPath($validated['parent_path'] ?? '', true);

        if (!is_dir($realParent)) {
            return $this->errorResponse($request, 'Direktori induk tidak valid.');
        }

        $rawInput = str_replace('\\', '/', trim($validated['folder_name']));
        $rawInput = trim($rawInput, '/');
        $segments = array_values(array_filter(explode('/', $rawInput), fn($s) => $s !== ''));

        if (empty($segments)) {
            return $this->errorResponse($request, 'Nama folder tidak boleh kosong.');
        }

        $cleanSegments = [];
        $checkPath = $realParent;
        foreach ($segments as $segment) {
            $validSegment = $this->validateSingleSegmentName($segment, false);
            $cleanSegments[] = $validSegment;
            $checkPath .= DIRECTORY_SEPARATOR . $validSegment;
            if (file_exists($checkPath) && !is_dir($checkPath)) {
                return $this->errorResponse($request, "Tidak dapat membuat folder karena sudah ada berkas bernama \"{$validSegment}\".");
            }
        }

        $targetPath = $realParent . DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $cleanSegments);
        if (file_exists($targetPath)) {
            return $this->errorResponse($request, 'Folder dengan nama tersebut sudah ada.');
        }

        if (!@mkdir($targetPath, 0755, true) && !is_dir($targetPath)) {
            return $this->errorResponse($request, 'Gagal membuat folder pada disk. Periksa izin direktori server.');
        }

        $realCreated = realpath($targetPath);
        if ($realCreated === false || !str_starts_with($realCreated, $realRoot . DIRECTORY_SEPARATOR)) {
            @rmdir($targetPath);
            abort(403, 'Jalur folder yang dibuat berada di luar direktori storage.');
        }

        $displayName = implode('/', $cleanSegments);

        return $this->successResponse(
            $request,
            "Folder \"{$displayName}\" berhasil dibuat.",
            $cleanParent
        );
    }

    /**
     * Mengunggah satu atau beberapa berkas (termasuk unggah folder dengan relative_paths).
     */
    public function upload(Request $request): JsonResponse|RedirectResponse
    {
        $this->authorizeAccess();

        $request->validate([
            'target_path' => 'nullable|string|max:500',
            'files' => 'required|array|min:1',
            'files.*' => 'file',
            'relative_paths' => 'nullable|array',
            'relative_paths.*' => 'nullable|string|max:500',
            'overwrite' => 'nullable|boolean',
        ]);

        $realRoot = $this->getRealRoot();
        [$cleanTarget, $realTargetDir] = $this->resolveExistingPath((string) $request->input('target_path', ''), true);

        if (!is_dir($realTargetDir)) {
            return $this->errorResponse($request, 'Direktori tujuan tidak valid.');
        }

        $overwrite = $request->boolean('overwrite', false);
        $uploadedFiles = $request->file('files', []);
        $relativePaths = $request->input('relative_paths', []);

        // Pre-validate all files before moving any, so dangerous extensions fail cleanly
        $prepared = [];
        foreach ($uploadedFiles as $idx => $uploadedFile) {
            if (!$uploadedFile || !$uploadedFile->isValid()) {
                return $this->errorResponse($request, 'Salah satu berkas gagal diunggah ke server (mungkin melebihi batas ukuran upload).');
            }

            $relHint = isset($relativePaths[$idx]) ? trim(str_replace('\\', '/', (string) $relativePaths[$idx]), '/') : '';

            if ($relHint !== '' && str_contains($relHint, '/')) {
                $parts = explode('/', $relHint);
                $rawFilename = (string) array_pop($parts);
                if (in_array($rawFilename, self::IGNORED_SYSTEM_FILES, true)) {
                    continue;
                }

                $validFilename = $this->validateSingleSegmentName($rawFilename, true);
                $validSubdirs = [];
                foreach ($parts as $dirSeg) {
                    if ($dirSeg === '') {
                        continue;
                    }
                    $validSubdirs[] = $this->validateSingleSegmentName($dirSeg, false);
                }

                $destDir = $realTargetDir . (!empty($validSubdirs) ? DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $validSubdirs) : '');
            } else {
                $rawFilename = $uploadedFile->getClientOriginalName();
                if (in_array($rawFilename, self::IGNORED_SYSTEM_FILES, true)) {
                    continue;
                }
                $validFilename = $this->validateSingleSegmentName($rawFilename, true);
                $destDir = $realTargetDir;
            }

            $prepared[] = [
                'file' => $uploadedFile,
                'dest_dir' => $destDir,
                'filename' => $validFilename,
            ];
        }

        if (empty($prepared)) {
            return $this->errorResponse($request, 'Tidak ada berkas yang valid untuk diunggah.');
        }

        $savedNames = [];
        foreach ($prepared as $item) {
            $destDir = $item['dest_dir'];
            if (!is_dir($destDir) && !@mkdir($destDir, 0755, true) && !is_dir($destDir)) {
                return $this->errorResponse($request, 'Gagal membuat subdirektori tujuan saat mengunggah.');
            }

            $realDestDir = realpath($destDir);
            if ($realDestDir === false || (!str_starts_with($realDestDir, $realRoot . DIRECTORY_SEPARATOR) && $realDestDir !== $realRoot)) {
                abort(403, 'Direktori unggahan berada di luar batas yang diizinkan.');
            }

            $finalName = $item['filename'];
            $destFullPath = $realDestDir . DIRECTORY_SEPARATOR . $finalName;

            if (!$overwrite && file_exists($destFullPath)) {
                $finalName = $this->makeUniqueFilename($realDestDir, $finalName);
                $destFullPath = $realDestDir . DIRECTORY_SEPARATOR . $finalName;
            } elseif ($overwrite && is_dir($destFullPath)) {
                return $this->errorResponse($request, "Tidak dapat menimpa folder dengan berkas bernama \"{$finalName}\".");
            }

            $item['file']->move($realDestDir, $finalName);
            $savedNames[] = $finalName;
        }

        $count = count($savedNames);
        $message = $count === 1
            ? "Berkas \"{$savedNames[0]}\" berhasil diunggah."
            : "{$count} berkas berhasil diunggah.";

        return $this->successResponse($request, $message, $cleanTarget, ['uploaded' => $savedNames]);
    }

    /**
     * Mengubah nama berkas atau folder.
     */
    public function rename(Request $request): JsonResponse|RedirectResponse
    {
        $this->authorizeAccess();

        $validated = $request->validate([
            'item_path' => 'required|string|max:500',
            'new_name' => 'required|string|max:255',
        ]);

        [$cleanItem, $realItem] = $this->resolveExistingPath($validated['item_path'], false);
        $isFile = is_file($realItem);
        $validNewName = $this->validateSingleSegmentName($validated['new_name'], $isFile);

        $parentDir = dirname($realItem);
        $parentClean = str_contains($cleanItem, '/')
            ? substr($cleanItem, 0, (int) strrpos($cleanItem, '/'))
            : '';

        $oldName = basename($realItem);
        if ($oldName === $validNewName) {
            return $this->successResponse($request, 'Nama tidak berubah.', $parentClean);
        }

        $destPath = $parentDir . DIRECTORY_SEPARATOR . $validNewName;
        if (strtolower($oldName) !== strtolower($validNewName) && file_exists($destPath)) {
            return $this->errorResponse($request, "Sudah ada berkas atau folder bernama \"{$validNewName}\".");
        }

        if (!@rename($realItem, $destPath)) {
            return $this->errorResponse($request, 'Gagal mengganti nama item.');
        }

        return $this->successResponse(
            $request,
            "\"{$oldName}\" berhasil diubah namanya menjadi \"{$validNewName}\".",
            $parentClean
        );
    }

    /**
     * Memindahkan satu atau beberapa berkas/folder ke direktori tujuan di dalam storage pendaftar.
     */
    public function move(Request $request): JsonResponse|RedirectResponse
    {
        $this->authorizeAccess();

        $validated = $request->validate([
            'item_path' => 'required_without:items|nullable|string|max:500',
            'items' => 'required_without:item_path|nullable|array|min:1',
            'items.*' => 'string|max:500',
            'destination_path' => 'nullable|string|max:500',
            'current_path' => 'nullable|string|max:500',
            'overwrite' => 'nullable|boolean',
        ]);

        [$cleanDest, $realDestDir] = $this->resolveExistingPath($validated['destination_path'] ?? '', true);
        if (!is_dir($realDestDir)) {
            return $this->errorResponse($request, 'Folder tujuan harus berupa direktori yang valid.');
        }

        $overwrite = $request->boolean('overwrite', false);
        $rawItems = !empty($validated['items']) ? $validated['items'] : [$validated['item_path']];

        $movedCount = 0;
        $redirectPath = $this->normalizeRelativePath($validated['current_path'] ?? $cleanDest);

        foreach ($rawItems as $rawItem) {
            [, $realItem] = $this->resolveExistingPath((string) $rawItem, false);

            // Prevent moving a folder into itself or any of its descendants
            if (is_dir($realItem) && ($realDestDir === $realItem || str_starts_with($realDestDir, $realItem . DIRECTORY_SEPARATOR))) {
                return $this->errorResponse($request, 'Tidak dapat memindahkan folder ke dalam dirinya sendiri atau subfoldernya.');
            }

            if (dirname($realItem) === $realDestDir) {
                continue;
            }

            $baseName = basename($realItem);
            $targetPath = $realDestDir . DIRECTORY_SEPARATOR . $baseName;

            if (file_exists($targetPath)) {
                if (!$overwrite || is_dir($realItem) || is_dir($targetPath)) {
                    return $this->errorResponse($request, "Di folder tujuan sudah terdapat item bernama \"{$baseName}\".");
                }
                @unlink($targetPath);
            }

            if (!@rename($realItem, $targetPath)) {
                return $this->errorResponse($request, "Gagal memindahkan \"{$baseName}\".");
            }

            $movedCount++;
        }

        if ($movedCount === 0) {
            return $this->successResponse($request, 'Item sudah berada di folder tujuan.', $redirectPath);
        }

        $destLabel = $cleanDest === '' ? 'pendaftar (Root)' : $cleanDest;
        $message = $movedCount === 1
            ? "Item berhasil dipindahkan ke \"{$destLabel}\"."
            : "{$movedCount} item berhasil dipindahkan ke \"{$destLabel}\".";

        return $this->successResponse($request, $message, $redirectPath);
    }

    /**
     * Mengekstrak file arsip ZIP dengan perlindungan ZipSlip dan filter ekstensi berbahaya.
     */
    public function extractZip(Request $request): JsonResponse|RedirectResponse
    {
        $this->authorizeAccess();

        $validated = $request->validate([
            'item_path' => 'required|string|max:500',
            'create_subfolder' => 'nullable|boolean',
        ]);

        $realRoot = $this->getRealRoot();
        [$cleanZip, $realZip] = $this->resolveExistingPath($validated['item_path'], false);

        if (!is_file($realZip) || strtolower(pathinfo($realZip, PATHINFO_EXTENSION)) !== 'zip') {
            return $this->errorResponse($request, 'Berkas yang dipilih bukan arsip ZIP yang valid.');
        }

        $parentDir = dirname($realZip);
        $parentClean = str_contains($cleanZip, '/')
            ? substr($cleanZip, 0, (int) strrpos($cleanZip, '/'))
            : '';

        $createSubfolder = $request->boolean('create_subfolder', true);
        if ($createSubfolder) {
            $subName = $this->validateSingleSegmentName(pathinfo($realZip, PATHINFO_FILENAME), false);
            $extractBase = $parentDir . DIRECTORY_SEPARATOR . $subName;
            if (file_exists($extractBase) && !is_dir($extractBase)) {
                return $this->errorResponse($request, "Sudah ada berkas bernama \"{$subName}\".");
            }
            if (!is_dir($extractBase)) {
                @mkdir($extractBase, 0755, true);
            }
        } else {
            $extractBase = $parentDir;
        }

        $realExtractBase = realpath($extractBase);
        if ($realExtractBase === false || (!str_starts_with($realExtractBase, $realRoot . DIRECTORY_SEPARATOR) && $realExtractBase !== $realRoot)) {
            abort(403, 'Direktori ekstraksi berada di luar batas yang diizinkan.');
        }

        $zip = new ZipArchive();
        if ($zip->open($realZip) !== true) {
            return $this->errorResponse($request, 'Gagal membuka arsip ZIP. Berkas mungkin rusak.');
        }

        $extractedFiles = 0;
        $skippedUnsafe = 0;

        for ($i = 0; $i < $zip->numFiles; $i++) {
            $entryName = $zip->getNameIndex($i);
            if ($entryName === false) {
                continue;
            }

            $normalized = str_replace('\\', '/', $entryName);
            $isDirectoryEntry = str_ends_with($normalized, '/');
            $trimmed = trim($normalized, '/');

            if ($trimmed === '' || str_starts_with($trimmed, '__MACOSX')) {
                continue;
            }

            // ZipSlip protection: reject drive letters, null bytes, empty segments, '.' or '..'
            if (preg_match('/^[A-Za-z]:/', $trimmed) || str_contains($trimmed, "\0")) {
                $skippedUnsafe++;
                continue;
            }

            $segments = explode('/', $trimmed);
            $unsafeSegment = false;
            foreach ($segments as $segIdx => $seg) {
                $isLeafFile = !$isDirectoryEntry && $segIdx === count($segments) - 1;
                try {
                    $this->validateSingleSegmentName($seg, $isLeafFile);
                } catch (ValidationException) {
                    $unsafeSegment = true;
                    break;
                }
            }

            if ($unsafeSegment) {
                $skippedUnsafe++;
                continue;
            }

            $targetEntryPath = $realExtractBase . DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $segments);

            if ($isDirectoryEntry) {
                if (!file_exists($targetEntryPath)) {
                    @mkdir($targetEntryPath, 0755, true);
                }
                continue;
            }

            if (is_dir($targetEntryPath)) {
                $skippedUnsafe++;
                continue;
            }

            $entryDir = dirname($targetEntryPath);
            if (!is_dir($entryDir)) {
                @mkdir($entryDir, 0755, true);
            }

            $realEntryDir = realpath($entryDir);
            if ($realEntryDir === false || (!str_starts_with($realEntryDir, $realExtractBase . DIRECTORY_SEPARATOR) && $realEntryDir !== $realExtractBase)) {
                $skippedUnsafe++;
                continue;
            }

            $stream = $zip->getStream($entryName);
            if ($stream === false) {
                continue;
            }

            $out = @fopen($targetEntryPath, 'wb');
            if ($out !== false) {
                stream_copy_to_stream($stream, $out);
                fclose($out);
                $extractedFiles++;
            }
            fclose($stream);
        }

        $zip->close();

        $msg = "Berhasil mengekstrak {$extractedFiles} berkas dari arsip ZIP.";
        if ($skippedUnsafe > 0) {
            $msg .= " ({$skippedUnsafe} entri sistem/berbahaya dilewati).";
        }

        return $this->successResponse($request, $msg, $parentClean);
    }

    /**
     * Menghapus satu berkas atau folder (beserta seluruh isinya).
     */
    public function destroy(Request $request): JsonResponse|RedirectResponse
    {
        $this->authorizeAccess();

        $validated = $request->validate([
            'item_path' => 'required|string|max:500',
            'current_path' => 'nullable|string|max:500',
        ]);

        $realRoot = $this->getRealRoot();
        [$cleanItem, $realItem] = $this->resolveExistingPath($validated['item_path'], false);

        $parentClean = str_contains($cleanItem, '/')
            ? substr($cleanItem, 0, (int) strrpos($cleanItem, '/'))
            : '';
        $redirectPath = $this->normalizeRelativePath($validated['current_path'] ?? $parentClean);

        $itemName = basename($realItem);
        $rawCandidate = $realRoot . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $cleanItem);

        if (is_link($rawCandidate)) {
            @unlink($rawCandidate);
        } elseif (is_dir($realItem)) {
            $this->deleteDirectoryRecursive($realItem, $realRoot);
        } elseif (is_file($realItem)) {
            @unlink($realItem);
        }

        return $this->successResponse(
            $request,
            "\"{$itemName}\" berhasil dihapus.",
            $redirectPath
        );
    }

    /**
     * Menghapus beberapa berkas/folder terpilih sekaligus.
     */
    public function bulkDelete(Request $request): JsonResponse|RedirectResponse
    {
        $this->authorizeAccess();

        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*' => 'string|max:500',
            'current_path' => 'nullable|string|max:500',
        ]);

        $realRoot = $this->getRealRoot();
        $redirectPath = $this->normalizeRelativePath($validated['current_path'] ?? '');
        $deletedCount = 0;

        foreach ($validated['items'] as $rawItem) {
            $cleanItem = $this->normalizeRelativePath($rawItem);
            if ($cleanItem === '') {
                abort(403, 'Operasi ini tidak dapat dilakukan pada direktori utama.');
            }

            $rawCandidate = $realRoot . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $cleanItem);
            if (!file_exists($rawCandidate) && !is_link($rawCandidate)) {
                continue;
            }

            $realItem = realpath($rawCandidate);
            if ($realItem === false || !str_starts_with($realItem, $realRoot . DIRECTORY_SEPARATOR)) {
                abort(403, 'Akses ditolak: jalur berada di luar direktori storage pendaftar.');
            }

            if (is_link($rawCandidate)) {
                @unlink($rawCandidate);
            } elseif (is_dir($realItem)) {
                $this->deleteDirectoryRecursive($realItem, $realRoot);
            } elseif (is_file($realItem)) {
                @unlink($realItem);
            }
            $deletedCount++;
        }

        return $this->successResponse(
            $request,
            "{$deletedCount} item berhasil dihapus.",
            $redirectPath
        );
    }

    /**
     * Memindai isi langsung sebuah direktori.
     *
     * @return array<int, array<string, mixed>>
     */
    protected function scanCurrentDirectory(string $dir, string $realRoot, string $search = ''): array
    {
        $entries = @scandir($dir);
        if ($entries === false) {
            return [];
        }

        $result = [];
        $needle = strtolower($search);

        foreach ($entries as $entry) {
            if ($entry === '.' || $entry === '..' || str_starts_with($entry, '.')) {
                continue;
            }

            if ($needle !== '' && !str_contains(strtolower($entry), $needle)) {
                continue;
            }

            $fullPath = $dir . DIRECTORY_SEPARATOR . $entry;
            $realEntry = realpath($fullPath);
            if ($realEntry === false || !str_starts_with($realEntry, $realRoot . DIRECTORY_SEPARATOR)) {
                continue;
            }

            $result[] = $this->buildItemEntry($realEntry, $realRoot, false);
        }

        return $result;
    }

    /**
     * Pencarian rekursif pada seluruh subfolder di bawah $startDir.
     *
     * @return array<int, array<string, mixed>>
     */
    protected function searchRecursive(string $startDir, string $realRoot, string $search): array
    {
        $results = [];
        $needle = strtolower($search);
        $this->walkForSearch($startDir, $realRoot, $needle, $results, 0);

        return $results;
    }

    /**
     * @param array<int, array<string, mixed>> $results
     */
    protected function walkForSearch(string $dir, string $realRoot, string $needle, array &$results, int $depth): void
    {
        if ($depth > 15 || count($results) >= 300) {
            return;
        }

        $entries = @scandir($dir);
        if ($entries === false) {
            return;
        }

        foreach ($entries as $entry) {
            if ($entry === '.' || $entry === '..' || str_starts_with($entry, '.')) {
                continue;
            }

            $fullPath = $dir . DIRECTORY_SEPARATOR . $entry;
            if (is_link($fullPath)) {
                continue;
            }

            $realEntry = realpath($fullPath);
            if ($realEntry === false || !str_starts_with($realEntry, $realRoot . DIRECTORY_SEPARATOR)) {
                continue;
            }

            if (str_contains(strtolower($entry), $needle)) {
                $results[] = $this->buildItemEntry($realEntry, $realRoot, true);
                if (count($results) >= 300) {
                    return;
                }
            }

            if (is_dir($realEntry)) {
                $this->walkForSearch($realEntry, $realRoot, $needle, $results, $depth + 1);
            }
        }
    }

    protected function makeRelativePath(string $fullPath, string $basePath): string
    {
        $normFull = str_replace('\\', '/', $fullPath);
        $normBase = rtrim(str_replace('\\', '/', $basePath), '/');

        if (str_starts_with($normFull, $normBase . '/')) {
            return ltrim(substr($normFull, strlen($normBase) + 1), '/');
        }

        return ltrim(str_replace($normBase, '', $normFull), '/');
    }

    /**
     * Membangun array metadata untuk satu folder atau file.
     *
     * @return array<string, mixed>
     */
    protected function buildItemEntry(string $realEntry, string $realRoot, bool $includeParentRel): array
    {
        $name = basename($realEntry);
        $relativePath = $this->makeRelativePath($realEntry, $realRoot);
        $modifiedTs = @filemtime($realEntry) ?: time();
        $parentRel = str_contains($relativePath, '/')
            ? substr($relativePath, 0, (int) strrpos($relativePath, '/'))
            : '';

        if (is_dir($realEntry)) {
            $children = @scandir($realEntry);
            $itemsCount = 0;
            if ($children !== false) {
                foreach ($children as $ch) {
                    if ($ch !== '.' && $ch !== '..' && !str_starts_with($ch, '.')) {
                        $itemsCount++;
                    }
                }
            }

            return [
                'name' => $name,
                'type' => 'directory',
                'extension' => '',
                'category' => 'folder',
                'size' => 0,
                'formatted_size' => $itemsCount . ' item',
                'items_count' => $itemsCount,
                'modified_ts' => $modifiedTs,
                'modified' => date('d M Y, H:i', $modifiedTs),
                'relative_path' => $relativePath,
                'parent_relative_path' => $includeParentRel ? $parentRel : null,
            ];
        }

        $size = @filesize($realEntry) ?: 0;
        $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
        $category = $this->determineFileCategory($ext);

        return [
            'name' => $name,
            'type' => 'file',
            'extension' => $ext,
            'category' => $category,
            'size' => $size,
            'formatted_size' => $this->formatBytes((float) $size),
            'modified_ts' => $modifiedTs,
            'modified' => date('d M Y, H:i', $modifiedTs),
            'relative_path' => $relativePath,
            'parent_relative_path' => $includeParentRel ? $parentRel : null,
            'url' => route('storage-manager.file', ['path' => $relativePath]),
            'download_url' => route('storage-manager.file', ['path' => $relativePath, 'download' => 1]),
        ];
    }

    /**
     * Mengurutkan item: folder tetap di atas berkas, lalu berdasarkan kolom pilihan.
     *
     * @param array<int, array<string, mixed>> $items
     * @return array<int, array<string, mixed>>
     */
    protected function sortItems(array $items, string $sort, string $dir): array
    {
        $mult = $dir === 'desc' ? -1 : 1;

        usort($items, function (array $a, array $b) use ($sort, $mult): int {
            if ($a['type'] !== $b['type']) {
                return $a['type'] === 'directory' ? -1 : 1;
            }

            $cmp = match ($sort) {
                'size' => $a['type'] === 'directory'
                    ? ($a['items_count'] <=> $b['items_count'])
                    : ($a['size'] <=> $b['size']),
                'modified' => $a['modified_ts'] <=> $b['modified_ts'],
                'type' => strcasecmp($a['extension'] ?: 'folder', $b['extension'] ?: 'folder'),
                default => strnatcasecmp($a['name'], $b['name']),
            };

            if ($cmp === 0) {
                return strnatcasecmp($a['name'], $b['name']);
            }

            return $cmp * $mult;
        });

        return $items;
    }

    /**
     * Mengumpulkan daftar folder di dalam root untuk navigasi cepat dan modal Pindahkan.
     *
     * @return array<int, array{path: string, name: string, depth: int}>
     */
    protected function collectAllDirectoryPaths(string $dir, string $realRoot, int $depth = 0): array
    {
        if ($depth > 6) {
            return [];
        }

        $entries = @scandir($dir);
        if ($entries === false) {
            return [];
        }

        $subdirs = [];
        foreach ($entries as $entry) {
            if ($entry === '.' || $entry === '..' || str_starts_with($entry, '.')) {
                continue;
            }
            $full = $dir . DIRECTORY_SEPARATOR . $entry;
            if (!is_link($full) && is_dir($full)) {
                $realFull = realpath($full);
                if ($realFull !== false && str_starts_with($realFull, $realRoot . DIRECTORY_SEPARATOR)) {
                    $rel = $this->makeRelativePath($realFull, $realRoot);
                    $subdirs[] = ['real' => $realFull, 'rel' => $rel, 'name' => $entry];
                }
            }
        }

        usort($subdirs, fn($a, $b) => strnatcasecmp($a['name'], $b['name']));

        $list = [];
        foreach ($subdirs as $sub) {
            $list[] = [
                'path' => $sub['rel'],
                'name' => $sub['name'],
                'depth' => $depth + 1,
            ];
            foreach ($this->collectAllDirectoryPaths($sub['real'], $realRoot, $depth + 1) as $child) {
                $list[] = $child;
            }
        }

        return $list;
    }

    /**
     * Mengambil nama pendaftar jika nama folder cocok dengan nomor_registrasi.
     *
     * @param array<int, array<string, mixed>> $items
     * @return array<string, array{id: string, nama: string}>
     */
    protected function lookupPendaftarLabels(array $items): array
    {
        $dirNames = [];
        foreach ($items as $item) {
            if ($item['type'] === 'directory') {
                $dirNames[] = $item['name'];
            }
        }

        if (empty($dirNames)) {
            return [];
        }

        try {
            return Pendaftar::whereIn('nomor_registrasi', array_unique($dirNames))
                ->get(['id', 'nomor_registrasi', 'nama'])
                ->mapWithKeys(fn($p) => [
                    $p->nomor_registrasi => [
                        'id' => $p->id,
                        'nama' => $p->nama,
                    ],
                ])
                ->all();
        } catch (\Throwable) {
            return [];
        }
    }

    /**
     * @param array<string, string> $files
     * @param array<int, string> $emptyDirs
     */
    protected function collectArchiveEntries(
        string $currentDir,
        string $rootBaseDir,
        string $realRoot,
        array &$files,
        array &$emptyDirs,
        string $prefix = ''
    ): void {
        $entries = @scandir($currentDir);
        if ($entries === false) {
            return;
        }

        $hasNonHiddenChildren = false;

        foreach ($entries as $entry) {
            if ($entry === '.' || $entry === '..' || str_starts_with($entry, '.')) {
                continue;
            }

            $fullPath = $currentDir . DIRECTORY_SEPARATOR . $entry;
            if (is_link($fullPath)) {
                continue;
            }

            $realEntry = realpath($fullPath);
            if ($realEntry === false || !str_starts_with($realEntry, $realRoot . DIRECTORY_SEPARATOR)) {
                continue;
            }

            $hasNonHiddenChildren = true;
            $rel = $prefix . $this->makeRelativePath($realEntry, $rootBaseDir);

            if (is_dir($realEntry)) {
                $this->collectArchiveEntries($realEntry, $rootBaseDir, $realRoot, $files, $emptyDirs, $prefix);
            } elseif (is_file($realEntry)) {
                $files[$rel] = $realEntry;
            }
        }

        if (!$hasNonHiddenChildren && $currentDir !== $rootBaseDir) {
            $relDir = $prefix . $this->makeRelativePath($currentDir, $rootBaseDir);
            if ($relDir !== '') {
                $emptyDirs[] = $relDir;
            }
        }
    }

    protected function deleteDirectoryRecursive(string $dir, string $realRoot): void
    {
        $realDir = realpath($dir);
        if ($realDir === false || $realDir === $realRoot || !str_starts_with($realDir, $realRoot . DIRECTORY_SEPARATOR)) {
            abort(403, 'Akses hapus direktori ditolak.');
        }

        $entries = @scandir($realDir);
        if ($entries !== false) {
            foreach ($entries as $entry) {
                if ($entry === '.' || $entry === '..') {
                    continue;
                }

                $full = $realDir . DIRECTORY_SEPARATOR . $entry;
                if (is_link($full)) {
                    @unlink($full);
                } elseif (is_dir($full)) {
                    $this->deleteDirectoryRecursive($full, $realRoot);
                } else {
                    @unlink($full);
                }
            }
        }

        @rmdir($realDir);
    }

    protected function makeUniqueFilename(string $dir, string $filename): string
    {
        $nameOnly = pathinfo($filename, PATHINFO_FILENAME);
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        $suffix = $ext !== '' ? '.' . $ext : '';

        $counter = 1;
        do {
            $candidate = "{$nameOnly} ({$counter}){$suffix}";
            $counter++;
        } while (file_exists($dir . DIRECTORY_SEPARATOR . $candidate));

        return $candidate;
    }

    protected function determineFileCategory(string $ext): string
    {
        return match ($ext) {
            'jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'bmp' => 'image',
            'pdf' => 'pdf',
            'doc', 'docx', 'odt', 'rtf' => 'word',
            'ppt', 'pptx', 'pps', 'ppsx', 'odp' => 'ppt',
            'xls', 'xlsx', 'csv', 'ods' => 'excel',
            'mp4', 'webm', 'ogg', 'mov', 'mkv', 'avi', 'wmv' => 'video',
            'mp3', 'wav', 'm4a', 'aac', 'flac' => 'audio',
            'zip', 'rar', '7z', 'tar', 'gz' => 'archive',
            'txt', 'log', 'md', 'json', 'xml' => 'text',
            default => 'other',
        };
    }

    protected function formatBytes(int|float $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes > 0 ? log($bytes) : 0) / log(1024));
        $pow = min((int) $pow, count($units) - 1);
        $bytes /= pow(1024, $pow);

        return round($bytes, $precision) . ' ' . $units[$pow];
    }

    protected function getMaxUploadBytes(): int
    {
        $upload = $this->parseIniSize((string) ini_get('upload_max_filesize'));
        $post = $this->parseIniSize((string) ini_get('post_max_size'));

        if ($upload > 0 && $post > 0) {
            return min($upload, $post);
        }

        return max($upload, $post, 0);
    }

    protected function parseIniSize(string $size): int
    {
        $size = trim($size);
        if ($size === '' || $size === '-1' || $size === '0') {
            return 0;
        }

        $unit = strtolower(substr($size, -1));
        $val = (int) $size;

        return match ($unit) {
            'g' => $val * 1024 * 1024 * 1024,
            'm' => $val * 1024 * 1024,
            'k' => $val * 1024,
            default => $val,
        };
    }

    /**
     * @param array<string, mixed> $extra
     */
    protected function successResponse(Request $request, string $message, string $redirectPath = '', array $extra = []): JsonResponse|RedirectResponse
    {
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(array_merge([
                'success' => true,
                'message' => $message,
            ], $extra));
        }

        return redirect()
            ->route('storage-manager.index', $redirectPath !== '' ? ['path' => $redirectPath] : [])
            ->with('success', $message);
    }

    protected function errorResponse(Request $request, string $message, int $status = 422): JsonResponse|RedirectResponse
    {
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => false,
                'message' => $message,
            ], $status);
        }

        return back()->with('error', $message);
    }
}
