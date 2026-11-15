<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('laporan_kegiatans', function (Blueprint $table) {
            $table->id();

            // Periode laporan
            $table->unsignedTinyInteger('bulan');
            $table->unsignedSmallInteger('tahun');
            $table->unsignedTinyInteger('minggu');

            // Informasi kegiatan
            $table->date('tanggal');
            $table->string('deskripsi', 500);

            // Dokumentasi foto (opsional)
            $table->string('foto')->nullable();

            $table->timestamps();

            // Satu laporan untuk setiap minggu dalam satu bulan
            $table->unique(
                ['bulan', 'tahun', 'minggu'],
                'laporan_periode_minggu_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laporan_kegiatans');
    }
};
