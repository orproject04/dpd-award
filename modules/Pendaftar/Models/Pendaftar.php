<?php

namespace Modules\Pendaftar\Models;

use App\Models\Kontribusi;
use App\Models\Penghargaan;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Laravolt\Suitable\AutoFilter;
use Laravolt\Suitable\AutoSearch;
use Laravolt\Suitable\AutoSort;

class Pendaftar extends Model
{
    use AutoFilter, AutoSearch, AutoSort, HasFactory, HasUuids;

    protected $table = 'pendaftar';

    protected $guarded = [];

    /** @var array<string> */
    protected $searchableColumns = ['nomor_registrasi', 'nama', 'email'];

    protected $keyType = 'string';

    public $incrementing = false;

    protected static function newFactory()
    {
        return PendaftarFactory::new();
    }

    protected static function booted()
    {
        static::saving(function ($pendaftar) {
            if (empty($pendaftar->status_before)) {
                $pendaftar->status_before = $pendaftar->status ?? 'Diajukan';
            }
        });

        static::deleting(function ($pendaftar) {
            $ktp = $pendaftar->getRawOriginal('ktp');
            $foto = $pendaftar->getRawOriginal('foto');
            $dir = null;

            if (!empty($ktp)) {
                $dir = dirname(str_replace('\\', '/', $ktp));
            } elseif (!empty($foto)) {
                $dir = dirname(str_replace('\\', '/', $foto));
            }

            if (!empty($dir) && $dir !== '.' && $dir !== '/' && $dir !== 'pendaftar' && str_starts_with($dir, 'pendaftar/')) {
                Storage::disk('local')->deleteDirectory($dir);
            }
        });
    }

    public function kontribusi()
    {
        return $this->hasMany(Kontribusi::class);
    }

    public function penghargaan()
    {
        return $this->hasMany(Penghargaan::class);
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
        return \App\Models\Pendaftar::getCurrentTimelineStage();
    }

    public static function getDisplayStatus($actualStatus = null, ?string $statusBefore = null): string
    {
        return \App\Models\Pendaftar::getDisplayStatus($actualStatus, $statusBefore);
    }

    public function getProvinsiWithWilayahAttribute(): string
    {
        if (empty($this->provinsi)) {
            return '-';
        }

        foreach (\App\Models\Pendaftar::getProvinsiMap() as $wilayah => $provinsis) {
            if (in_array($this->provinsi, $provinsis)) {
                return "{$this->provinsi} ({$wilayah})";
            }
        }

        return $this->provinsi;
    }

    public function getWilayahAttribute(): string
    {
        if (empty($this->provinsi)) {
            return '-';
        }

        foreach (\App\Models\Pendaftar::getProvinsiMap() as $wilayah => $provinsis) {
            if (in_array($this->provinsi, $provinsis)) {
                return $wilayah;
            }
        }

        return '-';
    }

    public function riwayats()
    {
        return $this->hasMany(\App\Models\PendaftarRiwayat::class, 'pendaftar_id')->orderBy('created_at', 'desc');
    }

    public function kertasKerja()
    {
        return $this->hasMany(\App\Models\PendaftarKertasKerja::class, 'pendaftar_id');
    }

    public function getDetailedStatus(): string
    {
        $statusBefore = $this->status_before;
        $actualStatus = $this->status;

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

        $currentStage = \App\Models\Pendaftar::getCurrentTimelineStage();
        $currentRank = $stages[$currentStage] ?? 0;
        $beforeRank = $stages[$statusBefore] ?? 0;

        if (isset($stages[$statusBefore]) && $currentRank <= $beforeRank && $actualStatus !== $statusBefore) {
            return $statusBefore;
        }

        if ($actualStatus === 'Tidak Lolos') {
            if (!empty($statusBefore) && $statusBefore !== 'Tidak Lolos' && isset($stages[$statusBefore])) {
                $nextStages = [
                    'Diajukan' => 'Lolos Verifikasi Berkas',
                    'Lolos Verifikasi Berkas' => 'Lolos ke Tahap 50 Besar',
                    'Lolos ke Tahap 50 Besar' => 'Lolos ke Tahap 10 Besar',
                    'Lolos ke Tahap 10 Besar' => 'Lolos ke Tahap 3 Besar',
                    'Lolos ke Tahap 3 Besar' => 'Lolos ke Tahap Wawancara',
                    'Lolos ke Tahap Wawancara' => 'Lolos ke Tahap Final'
                ];
                
                if (isset($nextStages[$statusBefore])) {
                    $nextStageName = str_replace(['Lolos ke Tahap ', 'Lolos '], '', $nextStages[$statusBefore]);
                    return "Tidak Lolos Tahap " . $nextStageName;
                }
            }
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
}
