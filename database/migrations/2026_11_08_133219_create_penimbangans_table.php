<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penimbangans', function (Blueprint $table) {
            $table->id();

            // Tanggal pelaksanaan penimbangan
            $table->date('tanggal');

            // Jenis sampah yang ditimbang
            $table->foreignId('jenis_sampah_id')
                ->constrained('jenis_sampahs')
                ->restrictOnDelete();

            // Berat sampah dalam kilogram
            $table->decimal('berat', 12, 2);

            // Menyimpan harga saat penimbangan dilakukan
            $table->decimal('harga_per_kg', 12, 2);

            // Total otomatis dari berat x harga
            $table->decimal('total', 14, 2);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penimbangans');
    }
};
