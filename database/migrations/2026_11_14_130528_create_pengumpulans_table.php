
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengumpulans', function (Blueprint $table) {
            $table->id();

            $table->foreignId('nasabah_id')
                ->constrained('nasabahs')
                ->cascadeOnDelete();

            $table->date('tanggal_pengumpulan');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengumpulans');
    }
};
