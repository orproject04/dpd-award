<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KategoriAspek extends Model
{
    use HasFactory;

    protected $table = 'kategori_aspeks';

    protected $fillable = [
        'kategori',
        'aspek',
        'dimensi',
        'bobot',
    ];

    protected $casts = [
        'bobot' => 'integer',
    ];

    public static function defaultKategories(): array
    {
        return [
            'Bidang Pendidikan',
            'Bidang Kesehatan',
            'Bidang Ketahanan Pangan',
            'Bidang Seni dan Budaya',
        ];
    }
}
