<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('keranjang', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')
                  ->constrained('users')
                  ->cascadeOnDelete();
            $table->foreignId('produk_id')
                  ->constrained('produk')
                  ->cascadeOnDelete();
            $table->unsignedInteger('jumlah')->default(1);
            $table->timestamp('created_at')->useCurrent();

            $table->unique(['customer_id', 'produk_id']);
        });

        Schema::create('wishlist', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')
                  ->constrained('users')
                  ->cascadeOnDelete();
            $table->foreignId('produk_id')
                  ->constrained('produk')
                  ->cascadeOnDelete();
            $table->timestamp('created_at')->useCurrent();

            $table->unique(['customer_id', 'produk_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wishlist');
        Schema::dropIfExists('keranjang');
    }
};
