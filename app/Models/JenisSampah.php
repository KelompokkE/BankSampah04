<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JenisSampah extends Model
{
    protected $fillable = [
        'nama',
        'harga_per_kg',
        'aktif',
    ];

    protected function casts(): array
    {
        return [
            'harga_per_kg' => 'decimal:2',
            'aktif' => 'boolean',
        ];
    }

    // Satu jenis sampah dapat memiliki banyak catatan penimbangan
    public function penimbangans(): HasMany
    {
        return $this->hasMany(Penimbangan::class);
    }
}