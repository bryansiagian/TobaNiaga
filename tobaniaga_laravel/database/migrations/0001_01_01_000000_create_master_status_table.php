<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Status User
        Schema::create('status_user', function (Blueprint $table) {
            $table->id();
            $table->string('kode')->unique();
            $table->string('label');
        });

        // Status Verifikasi UMKM
        Schema::create('status_verifikasi_umkm', function (Blueprint $table) {
            $table->id();
            $table->string('kode')->unique();
            $table->string('label');
        });

        // Status UMKM
        Schema::create('status_umkm', function (Blueprint $table) {
            $table->id();
            $table->string('kode')->unique();
            $table->string('label');
        });

        // Status Produk
        Schema::create('status_produk', function (Blueprint $table) {
            $table->id();
            $table->string('kode')->unique();
            $table->string('label');
        });

        // Status Promo
        Schema::create('status_promo', function (Blueprint $table) {
            $table->id();
            $table->string('kode')->unique();
            $table->string('label');
        });

        // Status Pesanan
        Schema::create('status_pesanan', function (Blueprint $table) {
            $table->id();
            $table->string('kode')->unique();
            $table->string('label');
            $table->unsignedTinyInteger('urutan')->comment('untuk tracking timeline di UI');
        });

        // Status Pembayaran
        Schema::create('status_pembayaran', function (Blueprint $table) {
            $table->id();
            $table->string('kode')->unique();
            $table->string('label');
        });

        // Status Pengiriman
        Schema::create('status_pengiriman', function (Blueprint $table) {
            $table->id();
            $table->string('kode')->unique();
            $table->string('label');
            $table->unsignedTinyInteger('urutan');
        });

        Schema::create('status_verifikasi_dokumen', function (Blueprint $table) {
            $table->id();
            $table->string('kode');
            $table->string('label');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('status_pengiriman');
        Schema::dropIfExists('status_pembayaran');
        Schema::dropIfExists('status_pesanan');
        Schema::dropIfExists('status_promo');
        Schema::dropIfExists('status_produk');
        Schema::dropIfExists('status_umkm');
        Schema::dropIfExists('status_verifikasi_umkm');
        Schema::dropIfExists('status_verifikasi_dokumen');
        Schema::dropIfExists('status_user');
    }
};
