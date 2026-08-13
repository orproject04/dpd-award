<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pendaftar extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'pendaftar';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $guarded = [];

    public function kontribusi(): HasMany
    {
        return $this->hasMany(Kontribusi::class, 'pendaftar_id');
    }

    public function penghargaan(): HasMany
    {
        return $this->hasMany(Penghargaan::class, 'pendaftar_id');
    }

    public static function getProvinsiMap(): array
    {
        return [
            'Sub Wilayah Barat I' => [
                'Provinsi Aceh', 'Provinsi Sumatera Utara', 'Provinsi Sumatera Barat',
                'Provinsi Riau', 'Provinsi Jambi', 'Provinsi Sumatera Selatan',
                'Provinsi Bengkulu', 'Provinsi Kepulauan Bangka Belitung',
                'Provinsi Kepulauan Riau', 'Provinsi Lampung'
            ],
            'Sub Wilayah Barat II' => [
                'Provinsi Daerah Khusus Jakarta', 'Provinsi Jawa Barat', 'Provinsi Jawa Tengah',
                'Provinsi Daerah Istimewa Yogyakarta', 'Provinsi Jawa Timur', 'Provinsi Banten',
                'Provinsi Bali', 'Provinsi Nusa Tenggara Barat', 'Provinsi Nusa Tenggara Timur'
            ],
            'Sub Wilayah Timur I' => [
                'Provinsi Kalimantan Barat', 'Provinsi Kalimantan Tengah', 'Provinsi Kalimantan Selatan',
                'Provinsi Kalimantan Timur', 'Provinsi Kalimantan Utara', 'Provinsi Sulawesi Selatan',
                'Provinsi Sulawesi Tengah', 'Provinsi Sulawesi Barat', 'Provinsi Gorontalo'
            ],
            'Sub Wilayah Timur II' => [
                'Provinsi Sulawesi Utara', 'Provinsi Sulawesi Tenggara', 'Provinsi Maluku',
                'Provinsi Maluku Utara', 'Provinsi Papua', 'Provinsi Papua Barat',
                'Provinsi Papua Selatan', 'Provinsi Papua Tengah', 'Provinsi Papua Pegunungan',
                'Provinsi Papua Barat Daya'
            ]
        ];
    }

    public static function getProvinsiList(): array
    {
        $list = ['' => 'Pilih Provinsi'];
        foreach (self::getProvinsiMap() as $wilayah => $provinsis) {
            foreach ($provinsis as $provinsi) {
                $list[$provinsi] = $provinsi;
            }
        }
        return $list;
    }

    public function getProvinsiWithWilayahAttribute(): string
    {
        if (empty($this->provinsi)) {
            return '-';
        }

        foreach (self::getProvinsiMap() as $wilayah => $provinsis) {
            if (in_array($this->provinsi, $provinsis)) {
                return "{$this->provinsi} ({$wilayah})";
            }
        }

        return $this->provinsi;
    }

    public function getFotoAttribute(): string
    {
        $foto = $this->getRawOriginal('foto');
        if (empty($foto)) {
            return '';
        }

        return storage_path('app/private/' . $foto);
    }

    public static function getCurrentTimelineStage(): string
    {
        $setting = config('laravolt.ui.timeline_saat_ini');
        if (!empty($setting)) {
            return $setting;
        }

        $now = \Carbon\Carbon::now();

        $stageConfigs = [
            'Lolos ke Tahap Final' => config('laravolt.ui.timeline_malam_penganugerahan'),
            'Lolos ke Tahap Wawancara' => config('laravolt.ui.timeline_wawancara'),
            'Lolos ke Tahap 3 Besar' => config('laravolt.ui.timeline_penilaian_tahap_3'),
            'Lolos ke Tahap 10 Besar' => config('laravolt.ui.timeline_penilaian_tahap_2'),
            'Lolos ke Tahap 50 Besar' => config('laravolt.ui.timeline_penilaian_tahap_1'),
            'Lolos Verifikasi Berkas' => config('laravolt.ui.timeline_verifikasi_identifikasi'),
            'Diajukan' => config('laravolt.ui.timeline_periode_pendaftaran'),
        ];

        $monthMap = [
            'januari' => 1,
            'jan' => 1,
            'februari' => 2,
            'feb' => 2,
            'maret' => 3,
            'mar' => 3,
            'april' => 4,
            'apr' => 4,
            'mei' => 5,
            'juni' => 6,
            'jun' => 6,
            'juli' => 7,
            'jul' => 7,
            'agustus' => 8,
            'agu' => 8,
            'ags' => 8,
            'september' => 9,
            'sep' => 9,
            'oktober' => 10,
            'okt' => 10,
            'november' => 11,
            'nov' => 11,
            'desember' => 12,
            'des' => 12,
        ];

        foreach ($stageConfigs as $stageName => $dateStr) {
            if (empty($dateStr)) continue;

            $dateStrLower = strtolower($dateStr);
            foreach ($monthMap as $mName => $mNum) {
                if (str_contains($dateStrLower, $mName)) {
                    if (preg_match('/20\d{2}/', $dateStrLower, $yMatches)) {
                        $year = (int)$yMatches[0];
                        if (preg_match('/\b([1-3]?[0-9])\b/', $dateStrLower, $dMatches)) {
                            $day = (int)$dMatches[1];
                            try {
                                $stageDate = \Carbon\Carbon::create($year, $mNum, $day, 0, 0, 0);
                                if ($now->greaterThanOrEqualTo($stageDate)) {
                                    return $stageName;
                                }
                            } catch (\Throwable $e) {
                                // ignore parse errors
                            }
                        }
                    }
                    break;
                }
            }
        }

        return 'Diajukan';
    }

    protected static function booted(): void
    {
        static::saving(function ($pendaftar) {
            if (empty($pendaftar->status_before)) {
                $pendaftar->status_before = $pendaftar->status ?? 'Diajukan';
            }
        });
    }

    public static function getDisplayStatus($actualStatus = null, ?string $statusBefore = null): string
    {
        if ($actualStatus instanceof self || $actualStatus instanceof \Modules\Pendaftar\Models\Pendaftar) {
            $statusBefore = $actualStatus->status_before;
            $actualStatus = $actualStatus->status;
        }

        if (empty($actualStatus)) {
            $actualStatus = 'Diajukan';
        }

        if (empty($statusBefore)) {
            $statusBefore = $actualStatus;
        }

        $stages = [
            'Diajukan' => 0,
            'Lolos Verifikasi Berkas' => 1,
            'Lolos ke Tahap 50 Besar' => 2,
            'Lolos ke Tahap 10 Besar' => 3,
            'Lolos ke Tahap 3 Besar' => 4,
            'Lolos ke Tahap Wawancara' => 5,
            'Lolos ke Tahap Final' => 6,
        ];

        $currentStage = static::getCurrentTimelineStage();
        $currentRank = $stages[$currentStage] ?? 0;
        $beforeRank = $stages[$statusBefore] ?? 0;

        if (isset($stages[$statusBefore]) && $currentRank <= $beforeRank && $actualStatus !== $statusBefore) {
            return $statusBefore;
        }

        if ($actualStatus === 'Tidak Lolos') {
            return 'Tidak Lolos';
        }

        if (!isset($stages[$actualStatus])) {
            return $actualStatus;
        }

        $actualRank = $stages[$actualStatus];

        if ($actualRank > $currentRank) {
            return $currentStage;
        }

        return $actualStatus;
    }

    public function riwayats()
    {
        return $this->hasMany(PendaftarRiwayat::class, 'pendaftar_id')->orderBy('created_at', 'desc');
    }
}
