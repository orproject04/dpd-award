<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Kontribusi extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'kontribusi';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $guarded = [];

    public function pendaftar(): BelongsTo
    {
        return $this->belongsTo(Pendaftar::class, 'pendaftar_id');
    }
}
