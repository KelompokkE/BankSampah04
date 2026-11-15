<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Nasabah extends Model
{
    protected $fillable = [
        'kode_nasabah',
        'nama',
        'alamat',
        'rt',
        'no_telepon',
    ];

    public function pengumpulans(): HasMany
    {
        return $this->hasMany(Pengumpulan::class);
    }
}
