<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pengumpulan extends Model
{
    protected $fillable = [
        'nasabah_id',
        'tanggal_pengumpulan',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_pengumpulan' => 'date',
        ];
    }

    public function nasabah(): BelongsTo
    {
        return $this->belongsTo(Nasabah::class);
    }

    public function detailPengumpulans(): HasMany
    {
        return $this->hasMany(DetailPengumpulan::class);
    }
}
