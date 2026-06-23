<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pesanan', function (Blueprint $table) {
            $table->id();
            $table->string('no_pesanan')->unique();
            $table->foreignId('customer_id')
                  ->constrained('users')
                  ->restrictOnDelete();
            $table->foreignId('umkm_id')
                  ->constrained('umkm')
                  ->restrictOnDelete();
            $table->foreignId('alamat_id')
                  ->nullable()
                  ->constrained('alamat_customer')
                  ->nullOnDelete()
                  ->comment('null jika ambil di tempat');
            $table->foreignId('metode_pengiriman_id')
                  ->constrained('metode_pengiriman')
                  ->restrictOnDelete();
            $table->float('ongkos_kirim')->default(0);
            $table->float('total_harga');
            $table->foreignId('status_id')
                  ->constrained('status_pesanan')
                  ->restrictOnDelete();
            $table->text('catatan_customer')->nullable();
            $table->timestamps();
        });

        Schema::create('pesanan_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pesanan_id')
                  ->constrained('pesanan')
                  ->cascadeOnDelete();
            $table->foreignId('produk_id')
                  ->constrained('produk')
                  ->restrictOnDelete();
            $table->string('nama_produk_snapshot')
                  ->comment('snapshot nama saat transaksi');
            $table->float('harga_satuan_snapshot');
            $table->unsignedInteger('jumlah');
            $table->float('subtotal');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pesanan_detail');
        Schema::dropIfExists('pesanan');
    }
};
