<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Penimbangan extends Model
{
    protected $fillable = [
        'tanggal',
        'jenis_sampah_id',
        'berat',
        'harga_per_kg',
        'total',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'berat' => 'decimal:2',
            'harga_per_kg' => 'decimal:2',
            'total' => 'decimal:2',
        ];
    }

    // Setiap penimbangan memiliki satu jenis sampah
    public function jenisSampah(): BelongsTo
    {
        return $this->belongsTo(JenisSampah::class);
    }
}
