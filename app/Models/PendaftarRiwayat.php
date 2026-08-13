<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class PendaftarRiwayat extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'pendaftar_id',
        'status',
        'keterangan',
        'created_at',
    ];
}
