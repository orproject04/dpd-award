<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Berita extends Model
{
    use \Illuminate\Database\Eloquent\Concerns\HasUuids;

    protected $fillable = [
        'sumber',
        'tanggal',
        'judul',
        'kutipan',
        'gambar',
        'tautan',
        'status_aktif',
    ];

    protected static function booted()
    {
        static::saved(function ($berita) {
            \Illuminate\Support\Facades\Artisan::call('responsecache:clear');
        });

        static::deleted(function ($berita) {
            \Illuminate\Support\Facades\Artisan::call('responsecache:clear');
        });
    }
}
