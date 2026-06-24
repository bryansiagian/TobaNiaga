<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ulasan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pesanan_id')
                  ->constrained('pesanan')
                  ->cascadeOnDelete();
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->cascadeOnDelete();
            $table->foreignId('umkm_id')
                  ->constrained('umkm')
                  ->cascadeOnDelete();
            $table->foreignId('produk_id')
                  ->nullable()
                  ->constrained('produk')
                  ->nullOnDelete();
            $table->unsignedTinyInteger('rating')
                  ->comment('1-5');
            $table->text('komentar')->nullable();
            $table->json('foto')->nullable()->comment('array path foto ulasan, maks beberapa foto');
            $table->boolean('is_anonim')->default(false);
            $table->timestamps();

            // Satu user hanya bisa beri 1 ulasan untuk produk yang sama dalam 1 pesanan
            $table->unique(['pesanan_id', 'user_id', 'produk_id'], 'ulasan_unik_per_pesanan_produk');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ulasan');
    }
};
