<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Hapus tabel lama dan buat ulang yang lengkap
        Schema::dropIfExists('promo_target');
        Schema::dropIfExists('promo');

        Schema::create('promo', function (Blueprint $table) {
            $table->id();
            $table->foreignId('umkm_id')
                  ->nullable()
                  ->constrained('umkm')
                  ->cascadeOnDelete()
                  ->comment('null = promo platform oleh admin');
            $table->string('kode')->unique()->comment('Kode voucher yang diinput customer');
            $table->string('nama_promo');
            $table->text('deskripsi')->nullable();
            $table->enum('tipe', ['persen', 'nominal'])->comment('Tipe diskon');
            $table->float('nilai')->comment('Nilai diskon: % atau nominal Rp');
            $table->float('min_belanja')->default(0)->comment('Minimum total belanja');
            $table->float('maks_diskon')->nullable()->comment('Maksimum potongan untuk tipe persen');
            $table->unsignedInteger('kuota')->nullable()->comment('null = tidak terbatas');
            $table->unsignedInteger('terpakai')->default(0);
            $table->date('berlaku_mulai');
            $table->date('berlaku_sampai');
            $table->boolean('is_aktif')->default(true);
            $table->timestamps();
        });

        // Pivot promo ke produk tertentu (opsional, null = semua produk UMKM)
        Schema::create('promo_produk', function (Blueprint $table) {
            $table->id();
            $table->foreignId('promo_id')
                  ->constrained('promo')
                  ->cascadeOnDelete();
            $table->foreignId('produk_id')
                  ->constrained('produk')
                  ->cascadeOnDelete();
            $table->unique(['promo_id', 'produk_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('promo_produk');
        Schema::dropIfExists('promo');
    }
};
