<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Pendaftar\Models\Pendaftar;

class PendaftarKertasKerja extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'pendaftar_kertas_kerja';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $guarded = [];

    protected $casts = [
        'data_dukung' => 'array',
        'bobot' => 'integer',
        'nilai' => 'integer',
        'total' => 'float',
    ];

    public function pendaftar(): BelongsTo
    {
        return $this->belongsTo(Pendaftar::class, 'pendaftar_id');
    }

    public function kategoriAspek(): BelongsTo
    {
        return $this->belongsTo(KategoriAspek::class, 'kategori_aspek_id');
    }
}
