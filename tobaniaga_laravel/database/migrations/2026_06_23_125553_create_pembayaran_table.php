<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pembayaran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pesanan_id')
                  ->constrained('pesanan')
                  ->cascadeOnDelete();
            $table->string('midtrans_order_id')->unique();
            $table->string('midtrans_trans_id')->nullable();
            $table->string('snap_token')->nullable();
            $table->string('metode')->nullable()
                  ->comment('qris, va_bca, gopay, dll — diisi setelah bayar dari callback Midtrans');
            $table->float('jumlah');
            $table->foreignId('status_id')
                  ->constrained('status_pembayaran')
                  ->restrictOnDelete();
            $table->text('raw_response')->nullable()
                  ->comment('simpan payload callback midtrans');
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembayaran');
    }
};
