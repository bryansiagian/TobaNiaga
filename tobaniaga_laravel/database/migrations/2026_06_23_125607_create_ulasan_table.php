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
            $table->foreignId('customer_id')
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
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ulasan');
    }
};
