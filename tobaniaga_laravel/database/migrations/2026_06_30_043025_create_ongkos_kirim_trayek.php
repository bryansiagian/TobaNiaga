<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ongkos_kirim_trayek', function (Blueprint $table) {
            $table->id();
            $table->string('lokasi_asal');    // kecamatan
            $table->string('lokasi_tujuan');  // kecamatan
            $table->float('ongkos');
            $table->boolean('is_aktif')->default(true);
            $table->timestamps();

            $table->unique(['lokasi_asal', 'lokasi_tujuan']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ongkos_kirim_trayek');
    }
};
