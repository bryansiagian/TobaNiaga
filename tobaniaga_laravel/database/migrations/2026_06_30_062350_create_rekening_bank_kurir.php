<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rekening_bank_kurir', function (Blueprint $table) {
            $table->id();
            $table->foreignId('courier_id')
                  ->constrained('users')
                  ->cascadeOnDelete();
            $table->string('nama_bank', 50);
            $table->string('nama_pemilik', 100);
            $table->string('no_rekening', 50);
            $table->boolean('is_utama')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rekening_bank_kurir');
    }
};
