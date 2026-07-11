<?php

namespace Modules\Pendaftar\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravolt\Suitable\AutoFilter;
use Laravolt\Suitable\AutoSearch;
use Laravolt\Suitable\AutoSort;
use Models\Kontribusi;
use Models\penghargaan;

class Pendaftar extends Model
{
    use AutoFilter, AutoSearch, AutoSort, HasFactory;

    protected $table = 'pendaftar';

    protected $guarded = [];

    /** @var array<string> */
    protected $searchableColumns = ['nomor_registrasi', 'nama'];

    protected static function newFactory()
    {
        return PendaftarFactory::new();
    }

    public function kontribusi()
    {
        return $this->hasMany(Kontribusi::class);
    }

    public function penghargaan()
    {
        return $this->hasMany(penghargaan::class);
    }

    public function getFotoAttribute(): string
    {
        if (! $this->foto) {
            return asset('assets/images/default.png');
        }

        return asset('storage/pendaftar/'.$this->foto);
    }
}
