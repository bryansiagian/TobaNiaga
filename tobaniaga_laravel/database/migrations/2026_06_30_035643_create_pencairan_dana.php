<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pencairan_dana', function (Blueprint $table) {
            $table->id();
            $table->string('no_pencairan')->unique();   // PCR-XXXXXXXX
            $table->foreignId('umkm_id')
                  ->constrained('umkm')
                  ->cascadeOnDelete();
            $table->foreignId('rekening_bank_id')
                  ->constrained('rekening_bank')
                  ->restrictOnDelete();
            $table->float('jumlah');                     // total nominal diajukan
            $table->enum('status', ['diajukan', 'diproses', 'selesai', 'ditolak'])
                  ->default('diajukan');
            $table->text('catatan_admin')->nullable();    // alasan tolak / catatan transfer
            $table->string('bukti_transfer')->nullable(); // path file bukti (opsional)
            $table->foreignId('diproses_oleh')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();
            $table->timestamp('diproses_at')->nullable();
            $table->timestamp('selesai_at')->nullable();
            $table->timestamps();
        });

        Schema::create('pencairan_dana_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pencairan_dana_id')
                  ->constrained('pencairan_dana')
                  ->cascadeOnDelete();
            $table->foreignId('pesanan_id')
                  ->constrained('pesanan')
                  ->cascadeOnDelete();
            $table->float('jumlah'); // snapshot total_harga pesanan saat diajukan
            $table->unique(['pesanan_id']); // satu pesanan hanya boleh 1x masuk pencairan aktif
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pencairan_dana_detail');
        Schema::dropIfExists('pencairan_dana');
    }
};
