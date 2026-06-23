<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kategori_umkm', function (Blueprint $table) {
            $table->id();
            $table->string('nama')->unique()->comment('Kuliner, Kerajinan, Tekstil, Pertanian, dll');
            $table->text('deskripsi')->nullable();
        });

        Schema::create('umkm', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_id')
                  ->constrained('users')
                  ->restrictOnDelete()
                  ->comment('user dengan role sales');
            $table->foreignId('kategori_id')
                  ->constrained('kategori_umkm')
                  ->restrictOnDelete();
            $table->string('nama_umkm');
            $table->string('slug')->unique();
            $table->text('deskripsi');
            $table->text('alamat');
            $table->string('kecamatan');
            $table->string('desa');
            $table->decimal('latitude', 10, 7)->nullable()->comment('untuk integrasi OSM/Maps');
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('no_hp_wa')->nullable()->comment('untuk tombol chat WA');
            $table->string('foto_profil')->nullable();
            $table->string('foto_banner')->nullable();
            $table->foreignId('status_verifikasi_id')
                  ->constrained('status_verifikasi_umkm')
                  ->restrictOnDelete();
            $table->foreignId('status_id')
                  ->constrained('status_umkm')
                  ->restrictOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('umkm');
        Schema::dropIfExists('kategori_umkm');
    }
};
