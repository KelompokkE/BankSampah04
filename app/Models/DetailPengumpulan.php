<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetailPengumpulan extends Model
{
    protected $fillable = [
        'pengumpulan_id',
        'jenis_sampah_id',
        'berat',
    ];

    protected function casts(): array
    {
        return [
            'berat' => 'decimal:2',
        ];
    }

    public function pengumpulan(): BelongsTo
    {
        return $this->belongsTo(Pengumpulan::class);
    }

    public function jenisSampah(): BelongsTo
    {
        return $this->belongsTo(JenisSampah::class);
    }
}
