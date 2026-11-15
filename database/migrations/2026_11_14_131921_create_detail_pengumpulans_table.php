<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detail_pengumpulans', function (Blueprint $table) {
            $table->id();

            $table->foreignId('pengumpulan_id')
                ->constrained('pengumpulans')
                ->cascadeOnDelete();

            $table->foreignId('jenis_sampah_id')
                ->constrained('jenis_sampahs')
                ->restrictOnDelete();

            $table->decimal('berat', 10, 2);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_pengumpulans');
    }
};