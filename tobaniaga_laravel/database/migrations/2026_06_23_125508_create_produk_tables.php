<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kategori_produk', function (Blueprint $table) {
            $table->id();
            $table->string('nama')->comment('Makanan Ringan, Anyaman, Kain Ulos, dll');
        });

        Schema::create('produk', function (Blueprint $table) {
            $table->id();
            $table->foreignId('umkm_id')
                  ->constrained('umkm')
                  ->cascadeOnDelete();
            $table->foreignId('kategori_id')
                  ->constrained('kategori_produk')
                  ->restrictOnDelete();
            $table->string('nama_produk');
            $table->string('slug');
            $table->text('deskripsi');
            $table->float('harga');
            $table->unsignedInteger('stok')->default(0);
            $table->string('satuan')->comment('pcs, kg, porsi, dll');
            $table->foreignId('status_id')
                  ->constrained('status_produk')
                  ->restrictOnDelete();
            $table->timestamps();

            // Index untuk performa filter di halaman customer.products.index
            $table->index('harga');
            $table->index('stok');
        });

        Schema::create('produk_foto', function (Blueprint $table) {
            $table->id();
            $table->foreignId('produk_id')
                  ->constrained('produk')
                  ->cascadeOnDelete();
            $table->string('url_foto');
            $table->unsignedTinyInteger('urutan')->default(0);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('produk_foto');
        Schema::dropIfExists('produk');
        Schema::dropIfExists('kategori_produk');
    }
};
