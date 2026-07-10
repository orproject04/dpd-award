<?php

namespace Modules\Pendaftar\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravolt\Suitable\AutoFilter;
use Laravolt\Suitable\AutoSearch;
use Laravolt\Suitable\AutoSort;

class Pendaftar extends Model
{
    use AutoFilter, AutoSearch, AutoSort, HasFactory;

    protected $table = 'pendaftar';

    protected $guarded = [];

    /** @var array<string> */
    protected $searchableColumns = ['nomor_registrasi', 'kategori', 'nama', 'tempat_lahir', 'tanggal_lahir', 'jenis_kelamin', 'pendidikan', 'alamat', 'nomor_wa', 'email', 'ktp', 'foto'];

    protected static function newFactory()
    {
        return PendaftarFactory::new();
    }
}
