<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Pendaftar;
use Illuminate\Support\Facades\URL;
use Modules\Pendaftar\Models\Pendaftar as ModulePendaftar;

/**
 * Mengelola pencocokan dan penyajian sertifikat PDF peserta berdasarkan tahapan seleksi.
 */
class SertifikatService
{
    /**
     * Pemetaan nama status ke peringkat tahapan (selaras dengan Pendaftar::getStatusRank).
     *
     * @var array<string, int>
     */
    protected const STAGE_RANKS = [
        'Diajukan' => 0,
        'Lolos Verifikasi Berkas' => 1,
        'Lolos ke Tahap 50 Besar' => 2,
        'Lolos ke Tahap 10 Besar' => 3,
        'Lolos ke Tahap 3 Besar' => 4,
        'Lolos ke Tahap Wawancara' => 5,
        'Lolos ke Tahap Final' => 6,
    ];

    /**
     * Metadata kanonik setiap tahap sertifikat, diurutkan dari tahap tertinggi ke terendah.
     *
     * @var array<int, array{key: string, label: string, title: string, download_prefix: string}>
     */
    protected const STAGE_META = [
        6 => [
            'key' => 'final',
            'label' => 'Tahap Final',
            'title' => 'Sertifikat Tahap Final',
            'download_prefix' => 'Sertifikat_Tahap_Final',
        ],
        5 => [
            'key' => 'wawancara',
            'label' => 'Verifikasi Lapangan & Penjurian 3 Besar',
            'title' => 'Sertifikat Verifikasi Lapangan & Penjurian 3 Besar',
            'download_prefix' => 'Sertifikat_Verifikasi_Lapangan',
        ],
        4 => [
            'key' => '3_besar',
            'label' => 'Tahap 3 Besar',
            'title' => 'Sertifikat 3 Besar',
            'download_prefix' => 'Sertifikat_3_Besar',
        ],
        3 => [
            'key' => '10_besar',
            'label' => 'Tahap 10 Besar',
            'title' => 'Sertifikat 10 Besar',
            'download_prefix' => 'Sertifikat_10_Besar',
        ],
        2 => [
            'key' => '50_besar',
            'label' => 'Tahap 50 Besar',
            'title' => 'Sertifikat 50 Besar',
            'download_prefix' => 'Sertifikat_50_Besar',
        ],
        1 => [
            'key' => 'verifikasi_berkas',
            'label' => 'Peserta',
            'title' => 'Sertifikat Peserta',
            'download_prefix' => 'Sertifikat_Peserta',
        ],
    ];

    /**
     * Mengonversi nilai status dropdown menjadi peringkat tahap (0..6, atau -1 bila tidak dikenal).
     */
    public static function resolveStageRankFromStatus(?string $status): int
    {
        $clean = trim((string) $status);

        return self::STAGE_RANKS[$clean] ?? -1;
    }

    /**
     * Mengonversi nilai status dropdown menjadi stage_key kanonik sertifikat (jika merupakan tahap lolos).
     */
    public static function mapStageKeyFromStatus(?string $status): ?string
    {
        $rank = self::resolveStageRankFromStatus($status);

        if ($rank < 1 || !isset(self::STAGE_META[$rank])) {
            return null;
        }

        return self::STAGE_META[$rank]['key'];
    }

    protected string $certRootPath;

    public function __construct(?string $certRootPath = null)
    {
        $this->certRootPath = $certRootPath ?? storage_path('app/private/pendaftar/sertifikat');
    }

    /**
     * Mengembalikan daftar sertifikat PDF yang tersedia dan berhak diakses oleh pendaftar.
     *
     * @return array<int, array{
     *     stage_key: string,
     *     stage_label: string,
     *     stage_rank: int,
     *     title: string,
     *     filename: string,
     *     size_formatted: string,
     *     view_url: string,
     *     download_url: string
     * }>
     */
    public function getAvailableCertificates(Pendaftar|ModulePendaftar $pendaftar, int $ttlHours = 2): array
    {
        $regId = trim((string) $pendaftar->nomor_registrasi);
        if ($regId === '') {
            return [];
        }

        $maxRank = $this->resolveMaxEligibleRank($pendaftar);
        if ($maxRank < 1) {
            return [];
        }

        $realRoot = $this->getRealCertRoot();
        if ($realRoot === null) {
            return [];
        }

        $dirsByRank = $this->mapStageDirectories($realRoot);
        $certificates = [];

        foreach (self::STAGE_META as $rank => $meta) {
            if ($rank > $maxRank || empty($dirsByRank[$rank])) {
                continue;
            }

            $pdfPath = $this->findPdfForRegIdInDirs($dirsByRank[$rank], $regId, $realRoot);
            if ($pdfPath === null) {
                continue;
            }

            $bytes = @filesize($pdfPath) ?: 0;
            $certificates[] = [
                'stage_key' => $meta['key'],
                'stage_label' => $meta['label'],
                'stage_rank' => $rank,
                'title' => $meta['title'],
                'filename' => $this->buildDownloadFilename($meta['download_prefix'], $regId),
                'size_formatted' => $this->formatBytes($bytes),
                'view_url' => URL::temporarySignedRoute('track.certificate', now()->addHours($ttlHours), [
                    'reg_id' => $regId,
                    'tahap' => $meta['key'],
                ], absolute: false),
                'download_url' => URL::temporarySignedRoute('track.certificate', now()->addHours($ttlHours), [
                    'reg_id' => $regId,
                    'tahap' => $meta['key'],
                    'download' => '1',
                ], absolute: false),
            ];
        }

        return $certificates;
    }

    /**
     * Mengambil pemetaan direktori tahap sekali jalan agar pemindaian massal lebih efisien.
     *
     * @return array{root: string, dirs: array<int, array<int, string>>}|null
     */
    public function getStageDirectoryIndex(): ?array
    {
        $realRoot = $this->getRealCertRoot();
        if ($realRoot === null) {
            return null;
        }

        return [
            'root' => $realRoot,
            'dirs' => $this->mapStageDirectories($realRoot),
        ];
    }

    /**
     * Mengembalikan daftar ID pendaftar yang memiliki file sertifikat PDF fisik valid
     * sesuai filter status yang aktif.
     *
     * @return array<int, string>
     */
    public function getPendaftarIdsWithCertificate(?string $statusFilter = null): array
    {
        $index = $this->getStageDirectoryIndex();
        if ($index === null) {
            return [];
        }

        $stemsByRank = $this->collectPdfStemsByRank($index);
        if (empty($stemsByRank)) {
            return [];
        }

        $cleanFilter = trim((string) $statusFilter);
        if ($cleanFilter !== '' && $cleanFilter !== 'Tidak Lolos') {
            $filterRank = self::STAGE_RANKS[$cleanFilter] ?? null;
            if ($filterRank === null || $filterRank < 1 || empty($stemsByRank[$filterRank])) {
                return [];
            }

            $rows = Pendaftar::query()
                ->where('status', $cleanFilter)
                ->get(['id', 'nomor_registrasi', 'status', 'status_before']);

            $matchedIds = [];
            foreach ($rows as $row) {
                $stem = strtolower(trim((string) $row->nomor_registrasi));
                if ($stem !== '' && isset($stemsByRank[$filterRank][$stem])) {
                    $matchedIds[] = $row->id;
                }
            }

            return $matchedIds;
        }

        $query = Pendaftar::query()->with('riwayats');
        if ($cleanFilter === 'Tidak Lolos') {
            $query->where('status', 'Tidak Lolos');
        } else {
            $query->where(function ($q) {
                $q->where('status', 'like', 'Lolos%')
                    ->orWhere('status', 'Tidak Lolos');
            });
        }

        $rows = $query->get(['id', 'nomor_registrasi', 'status', 'status_before']);
        $matchedIds = [];

        foreach ($rows as $row) {
            $stem = strtolower(trim((string) $row->nomor_registrasi));
            if ($stem === '') {
                continue;
            }

            $maxRank = $this->resolveAdminMaxStageRank($row);
            if ($maxRank < 1) {
                continue;
            }

            for ($r = $maxRank; $r >= 1; $r--) {
                if (isset($stemsByRank[$r][$stem])) {
                    $matchedIds[] = $row->id;
                    break;
                }
            }
        }

        return $matchedIds;
    }

    /**
     * Memindai seluruh stem nama file PDF per peringkat untuk pencocokan cepat O(1).
     *
     * @param array{root: string, dirs: array<int, array<int, string>>} $index
     * @return array<int, array<string, true>>
     */
    protected function collectPdfStemsByRank(array $index): array
    {
        $realRoot = $index['root'];
        $stemsByRank = [];

        foreach ($index['dirs'] as $rank => $dirs) {
            foreach ($dirs as $dir) {
                foreach ($this->scanPdfFilesInDir($dir, $realRoot) as $filePath) {
                    $stem = strtolower((string) pathinfo($filePath, PATHINFO_FILENAME));
                    if ($stem !== '') {
                        $stemsByRank[$rank][$stem] = true;
                    }
                }
            }
        }

        return $stemsByRank;
    }

    /**
     * Mengumpulkan path file PDF valid pada direktori tahap (langsung maupun 1 subfolder kategori).
     *
     * @return array<int, string>
     */
    protected function scanPdfFilesInDir(string $dir, string $realRoot): array
    {
        $entries = @scandir($dir);
        if (!is_array($entries)) {
            return [];
        }

        $results = [];
        foreach ($entries as $entry) {
            if ($entry === '.' || $entry === '..' || str_starts_with($entry, '.')) {
                continue;
            }

            $entryPath = $dir . DIRECTORY_SEPARATOR . $entry;
            if (is_file($entryPath)) {
                if (strtolower((string) pathinfo($entry, PATHINFO_EXTENSION)) === 'pdf') {
                    $verified = $this->verifyPdfInsideRoot($entryPath, $realRoot);
                    if ($verified !== null) {
                        $results[] = $verified;
                    }
                }
            } elseif (is_dir($entryPath)) {
                $subEntries = @scandir($entryPath);
                if (!is_array($subEntries)) {
                    continue;
                }
                foreach ($subEntries as $subEntry) {
                    if ($subEntry === '.' || $subEntry === '..' || str_starts_with($subEntry, '.')) {
                        continue;
                    }
                    if (strtolower((string) pathinfo($subEntry, PATHINFO_EXTENSION)) === 'pdf') {
                        $verified = $this->verifyPdfInsideRoot($entryPath . DIRECTORY_SEPARATOR . $subEntry, $realRoot);
                        if ($verified !== null) {
                            $results[] = $verified;
                        }
                    }
                }
            }
        }

        return $results;
    }

    /**
     * Menentukan sertifikat terbaik yang akan dikirim admin via email sesuai filter dropdown Status.
     * Ketika $statusFilter menunjuk tahap lolos tertentu, hanya tahap tersebut yang dicocokkan;
     * ketika "Semua Status" atau "Tidak Lolos", dipilih sertifikat tahap tertinggi yang lulus dan tersedia.
     *
     * @param array{root: string, dirs: array<int, array<int, string>>}|null $preloadedIndex
     * @return array{
     *     path: string,
     *     download_name: string,
     *     stage_key: string,
     *     stage_label: string,
     *     stage_rank: int,
     *     title: string
     * }|null
     */
    public function resolveAdminCertificateForSend(
        Pendaftar|ModulePendaftar $pendaftar,
        ?string $statusFilter = null,
        ?array $preloadedIndex = null
    ): ?array {
        $regId = trim((string) $pendaftar->nomor_registrasi);
        if ($regId === '') {
            return null;
        }

        $maxAdminRank = $this->resolveAdminMaxStageRank($pendaftar);
        if ($maxAdminRank < 1) {
            return null;
        }

        $index = $preloadedIndex ?? $this->getStageDirectoryIndex();
        if ($index === null) {
            return null;
        }

        $realRoot = $index['root'];
        $dirsByRank = $index['dirs'];
        $cleanFilter = trim((string) $statusFilter);

        if ($cleanFilter !== '' && $cleanFilter !== 'Tidak Lolos') {
            $filterRank = self::STAGE_RANKS[$cleanFilter] ?? null;
            if ($filterRank === null || $filterRank < 1 || $filterRank > $maxAdminRank || empty($dirsByRank[$filterRank])) {
                return null;
            }

            $pdfPath = $this->findPdfForRegIdInDirs($dirsByRank[$filterRank], $regId, $realRoot);
            if ($pdfPath === null) {
                return null;
            }

            $meta = self::STAGE_META[$filterRank];

            return $this->buildAdminCertificatePayload($pdfPath, $meta, $filterRank, $regId);
        }

        foreach (self::STAGE_META as $rank => $meta) {
            if ($rank > $maxAdminRank || empty($dirsByRank[$rank])) {
                continue;
            }

            $pdfPath = $this->findPdfForRegIdInDirs($dirsByRank[$rank], $regId, $realRoot);
            if ($pdfPath !== null) {
                return $this->buildAdminCertificatePayload($pdfPath, $meta, $rank, $regId);
            }
        }

        return null;
    }

    /**
     * Menyelesaikan file fisik sertifikat untuk satu tahap tertentu dengan verifikasi kelayakan peserta.
     *
     * @return array{path: string, download_name: string, meta: array{key: string, label: string, title: string, download_prefix: string}}|null
     */
    public function resolveCertificateFileForStage(
        Pendaftar|ModulePendaftar $pendaftar,
        string $stageKey,
        bool $respectTimeline = true
    ): ?array {
        $regId = trim((string) $pendaftar->nomor_registrasi);
        if ($regId === '') {
            return null;
        }

        $targetRank = $this->findRankByStageKey($stageKey);
        if ($targetRank === null) {
            return null;
        }

        $maxRank = $respectTimeline
            ? $this->resolveMaxEligibleRank($pendaftar)
            : $this->resolveAdminMaxStageRank($pendaftar);
        if ($targetRank > $maxRank) {
            return null;
        }

        $realRoot = $this->getRealCertRoot();
        if ($realRoot === null) {
            return null;
        }

        $dirsByRank = $this->mapStageDirectories($realRoot);
        if (empty($dirsByRank[$targetRank])) {
            return null;
        }

        $pdfPath = $this->findPdfForRegIdInDirs($dirsByRank[$targetRank], $regId, $realRoot);
        if ($pdfPath === null) {
            return null;
        }

        $meta = self::STAGE_META[$targetRank];

        return [
            'path' => $pdfPath,
            'download_name' => $this->buildDownloadFilename($meta['download_prefix'], $regId),
            'meta' => $meta,
        ];
    }

    /**
     * Menentukan peringkat tahap tertinggi yang berhak dilihat sertifikatnya oleh peserta
     * dengan tetap menghormati timeline pengumuman publik saat ini.
     */
    public function resolveMaxEligibleRank(Pendaftar|ModulePendaftar $pendaftar): int
    {
        $currentStage = Pendaftar::getCurrentTimelineStage();
        $currentRank = self::STAGE_RANKS[$currentStage] ?? 0;

        $displayStatus = Pendaftar::getDisplayStatus($pendaftar);
        if (isset(self::STAGE_RANKS[$displayStatus])) {
            return min(self::STAGE_RANKS[$displayStatus], $currentRank);
        }

        return min($this->resolveRejectedPreviousRank($pendaftar), $currentRank);
    }

    /**
     * Menentukan peringkat tahap tertinggi yang benar-benar telah dilalui peserta di database
     * untuk kebutuhan administrasi tanpa dibatasi jadwal pengumuman timeline publik.
     */
    public function resolveAdminMaxStageRank(Pendaftar|ModulePendaftar $pendaftar): int
    {
        $actualStatus = $pendaftar->status ?: 'Diajukan';
        if (isset(self::STAGE_RANKS[$actualStatus])) {
            return self::STAGE_RANKS[$actualStatus];
        }

        return $this->resolveRejectedPreviousRank($pendaftar);
    }

    protected function resolveRejectedPreviousRank(Pendaftar|ModulePendaftar $pendaftar): int
    {
        $actualStatus = $pendaftar->status ?: 'Diajukan';
        if ($actualStatus !== 'Tidak Lolos') {
            return 0;
        }

        $statusBefore = $pendaftar->status_before;
        if (!empty($statusBefore) && isset(self::STAGE_RANKS[$statusBefore])) {
            return self::STAGE_RANKS[$statusBefore];
        }

        // Fallback ke riwayat bila status_before kosong atau bernilai 'Tidak Lolos'
        $maxRiwayatRank = 0;
        foreach ($pendaftar->riwayats as $riwayat) {
            if (isset(self::STAGE_RANKS[$riwayat->status])) {
                $maxRiwayatRank = max($maxRiwayatRank, self::STAGE_RANKS[$riwayat->status]);
            }
        }

        return $maxRiwayatRank;
    }

    /**
     * @param array{key: string, label: string, title: string, download_prefix: string} $meta
     * @return array{path: string, download_name: string, stage_key: string, stage_label: string, stage_rank: int, title: string}
     */
    protected function buildAdminCertificatePayload(string $pdfPath, array $meta, int $rank, string $regId): array
    {
        return [
            'path' => $pdfPath,
            'download_name' => $this->buildDownloadFilename($meta['download_prefix'], $regId),
            'stage_key' => $meta['key'],
            'stage_label' => $meta['label'],
            'stage_rank' => $rank,
            'title' => $meta['title'],
        ];
    }

    /**
     * Mengenali peringkat tahap dari nama folder di dalam direktori sertifikat.
     */
    public function classifyStageFolder(string $folderName): ?int
    {
        $norm = strtolower(trim($folderName));
        $norm = preg_replace('/[^a-z0-9]+/', '_', $norm) ?? $norm;
        $norm = trim($norm, '_');

        if ($norm === '') {
            return null;
        }

        // Tahap 5 & 6 diperiksa lebih awal agar nama seperti "penjurian_3_besar" tidak salah masuk ke "3_besar"
        if (preg_match('/(^|_)(final|pemenang|anugerah|malam_penganugerahan)($|_)/', $norm)) {
            return 6;
        }
        if (preg_match('/(^|_)(wawancara|verifikasi_lapangan|penjurian_3_besar)($|_)/', $norm)) {
            return 5;
        }
        if (preg_match('/(^|_)(lolos_)?(tahap_)?50(_?besar)?($|_)/', $norm)) {
            return 2;
        }
        if (preg_match('/(^|_)(lolos_)?(tahap_)?10(_?besar)?($|_)/', $norm)) {
            return 3;
        }
        if (preg_match('/(^|_)(lolos_)?(tahap_)?3(_?besar)?($|_)/', $norm)) {
            return 4;
        }
        if (preg_match('/(^|_)(lolos_)?(tahap_)?(verifikasi_berkas|lolos_berkas|verifikasi|berkas)($|_)/', $norm)) {
            return 1;
        }

        return null;
    }

    protected function getRealCertRoot(): ?string
    {
        if (!is_dir($this->certRootPath)) {
            return null;
        }

        $real = realpath($this->certRootPath);

        return ($real !== false && is_dir($real)) ? $real : null;
    }

    /**
     * Memetakan direktori tahap yang ada di bawah root sertifikat ke peringkatnya masing-masing.
     *
     * @return array<int, array<int, string>>
     */
    protected function mapStageDirectories(string $realRoot): array
    {
        $entries = @scandir($realRoot);
        if (!is_array($entries)) {
            return [];
        }

        $dirsByRank = [];
        foreach ($entries as $entry) {
            if ($entry === '.' || $entry === '..' || str_starts_with($entry, '.')) {
                continue;
            }

            $fullPath = $realRoot . DIRECTORY_SEPARATOR . $entry;
            if (!is_dir($fullPath)) {
                continue;
            }

            $realSub = realpath($fullPath);
            if ($realSub === false || !str_starts_with($realSub, $realRoot . DIRECTORY_SEPARATOR)) {
                continue;
            }

            $rank = $this->classifyStageFolder($entry);
            if ($rank !== null) {
                $dirsByRank[$rank][] = $realSub;
            }
        }

        return $dirsByRank;
    }

    /**
     * Mencari file PDF bernama <nomor_registrasi>.pdf secara aman pada daftar direktori tahap.
     * Mendukung pencarian langsung maupun satu level subfolder kategori.
     *
     * @param array<int, string> $dirs
     */
    protected function findPdfForRegIdInDirs(array $dirs, string $regId, string $realRoot): ?string
    {
        $needleStem = strtolower($regId);

        foreach ($dirs as $dir) {
            $directCandidate = $dir . DIRECTORY_SEPARATOR . $regId . '.pdf';
            $verified = $this->verifyPdfInsideRoot($directCandidate, $realRoot);
            if ($verified !== null) {
                return $verified;
            }

            foreach ($this->scanPdfFilesInDir($dir, $realRoot) as $pdfPath) {
                if (strtolower((string) pathinfo($pdfPath, PATHINFO_FILENAME)) === $needleStem) {
                    return $pdfPath;
                }
            }
        }

        return null;
    }

    protected function verifyPdfInsideRoot(string $candidatePath, string $realRoot): ?string
    {
        if (!is_file($candidatePath)) {
            return null;
        }

        $realFile = realpath($candidatePath);
        if ($realFile === false || !is_file($realFile)) {
            return null;
        }

        if (!str_starts_with($realFile, $realRoot . DIRECTORY_SEPARATOR)) {
            return null;
        }

        return $realFile;
    }

    protected function findRankByStageKey(string $stageKey): ?int
    {
        foreach (self::STAGE_META as $rank => $meta) {
            if ($meta['key'] === $stageKey) {
                return $rank;
            }
        }

        return null;
    }

    protected function buildDownloadFilename(string $prefix, string $regId): string
    {
        $safeRegId = preg_replace('/[^A-Za-z0-9_\-]/', '_', $regId) ?: 'Peserta';

        return $prefix . '_' . $safeRegId . '.pdf';
    }

    protected function formatBytes(int $bytes): string
    {
        if ($bytes <= 0) {
            return '0 B';
        }

        $units = ['B', 'KB', 'MB', 'GB'];
        $power = min((int) floor(log($bytes, 1024)), count($units) - 1);

        return round($bytes / (1024 ** $power), 1) . ' ' . $units[$power];
    }
}

