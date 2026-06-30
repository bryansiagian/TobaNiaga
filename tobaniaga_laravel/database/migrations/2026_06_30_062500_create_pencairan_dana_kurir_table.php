<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pencairan_dana_kurir', function (Blueprint $table) {
            $table->id();
            $table->string('no_pencairan')->unique();
            $table->foreignId('courier_id')
                  ->constrained('users')
                  ->cascadeOnDelete();
            $table->foreignId('rekening_bank_kurir_id')
                  ->nullable()
                  ->constrained('rekening_bank_kurir')
                  ->restrictOnDelete();
            $table->float('jumlah');
            $table->enum('status', ['diajukan', 'diproses', 'selesai', 'ditolak'])
                  ->default('diajukan');
            $table->text('catatan_admin')->nullable();
            $table->string('bukti_transfer')->nullable();
            $table->foreignId('diproses_oleh')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();
            $table->timestamp('diproses_at')->nullable();
            $table->timestamp('selesai_at')->nullable();
            $table->timestamps();
        });

        Schema::create('pencairan_dana_kurir_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pencairan_dana_kurir_id')
                  ->constrained('pencairan_dana_kurir')
                  ->cascadeOnDelete();
            $table->foreignId('pengiriman_id')
                  ->constrained('pengiriman')
                  ->cascadeOnDelete();
            $table->float('jumlah');
            $table->unique(['pengiriman_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pencairan_dana_kurir_detail');
        Schema::dropIfExists('pencairan_dana_kurir');
    }
};
