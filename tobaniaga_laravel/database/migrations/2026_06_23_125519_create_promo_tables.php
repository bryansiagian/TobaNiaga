<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('promo', function (Blueprint $table) {
            $table->id();
            $table->foreignId('umkm_id')
                  ->nullable()
                  ->constrained('umkm')
                  ->cascadeOnDelete()
                  ->comment('null = promo platform, diisi = promo milik 1 UMKM');
            $table->string('nama_promo');
            $table->string('tipe')->comment('persen, nominal');
            $table->float('nilai');
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->foreignId('status_id')
                  ->constrained('status_promo')
                  ->restrictOnDelete();
        });

        Schema::create('promo_target', function (Blueprint $table) {
            $table->id();
            $table->foreignId('promo_id')
                  ->constrained('promo')
                  ->cascadeOnDelete();
            $table->foreignId('produk_id')
                  ->nullable()
                  ->constrained('produk')
                  ->cascadeOnDelete()
                  ->comment('null = berlaku untuk semua produk umkm terkait');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('promo_target');
        Schema::dropIfExists('promo');
    }
};
