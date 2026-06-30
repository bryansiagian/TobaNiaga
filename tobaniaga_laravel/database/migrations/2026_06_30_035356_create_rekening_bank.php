<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rekening_bank', function (Blueprint $table) {
            $table->id();
            $table->foreignId('umkm_id')
                  ->constrained('umkm')
                  ->cascadeOnDelete();
            $table->string('nama_bank');           // BCA, BNI, Mandiri, dll
            $table->string('nama_pemilik');         // Nama di buku rekening
            $table->string('no_rekening');
            $table->boolean('is_utama')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rekening_bank');
    }
};
