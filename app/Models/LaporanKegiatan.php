<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LaporanKegiatan extends Model
{
    protected $table = 'laporan_kegiatans';

    protected $fillable = [
        'bulan',
        'tahun',
        'minggu',
        'tanggal',
        'deskripsi',
        'foto',
    ];

    protected function casts(): array
    {
        return [
            'bulan' => 'integer',
            'tahun' => 'integer',
            'minggu' => 'integer',
            'tanggal' => 'date',
        ];
    }
}
