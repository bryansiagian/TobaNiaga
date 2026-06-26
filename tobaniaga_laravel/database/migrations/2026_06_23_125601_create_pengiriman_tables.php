<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengiriman', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pesanan_id')
                  ->unique()
                  ->constrained('pesanan')
                  ->cascadeOnDelete();
            $table->foreignId('courier_id')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete()
                  ->comment('diisi setelah kurir ditugaskan');
            $table->foreignId('status_id')
                  ->constrained('status_pengiriman')
                  ->restrictOnDelete();
            $table->timestamp('waktu_pickup')->nullable();
            $table->timestamp('waktu_selesai')->nullable();
            $table->text('catatan_kurir')->nullable();
            $table->string('nama_penerima')->nullable();
            $table->string('relasi_penerima')->nullable(); // "Diri sendiri", "Istri", dll
            $table->string('foto_bukti')->nullable();
            $table->timestamps();
        });

        Schema::create('pengiriman_log', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengiriman_id')
                  ->constrained('pengiriman')
                  ->cascadeOnDelete();
            $table->foreignId('status_id')
                  ->constrained('status_pengiriman')
                  ->restrictOnDelete();
            $table->text('catatan')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengiriman_log');
        Schema::dropIfExists('pengiriman');
    }
};
