<?php

namespace Modules\Pendaftar\Models;

use App\Models\Kontribusi;
use App\Models\Penghargaan;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Laravolt\Suitable\AutoFilter;
use Laravolt\Suitable\AutoSearch;
use Laravolt\Suitable\AutoSort;

class Pendaftar extends Model
{
    use AutoFilter, AutoSearch, AutoSort, HasFactory;

    protected $table = 'pendaftar';

    protected $guarded = [];

    /** @var array<string> */
    protected $searchableColumns = ['nomor_registrasi', 'nama'];

    protected $keyType = 'string';

    public $incrementing = false;

    protected static function newFactory()
    {
        return PendaftarFactory::new();
    }

    protected static function booted()
    {
        static::deleting(function ($pendaftar) {
            $ktp = $pendaftar->getRawOriginal('ktp');
            $foto = $pendaftar->getRawOriginal('foto');
            $dir = null;

            if (! empty($ktp)) {
                $dir = dirname(str_replace('\\', '/', $ktp));
            } elseif (! empty($foto)) {
                $dir = dirname(str_replace('\\', '/', $foto));
            }

            if (! empty($dir) && $dir !== '.' && $dir !== '/' && $dir !== 'pendaftar' && str_starts_with($dir, 'pendaftar/')) {
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
}
