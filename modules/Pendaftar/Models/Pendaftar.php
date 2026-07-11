<?php

namespace Modules\Pendaftar\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravolt\Suitable\AutoFilter;
use Laravolt\Suitable\AutoSearch;
use Laravolt\Suitable\AutoSort;
use App\Models\Kontribusi;
use App\Models\Penghargaan;

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
