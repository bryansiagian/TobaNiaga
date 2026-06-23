<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('metode_pengiriman', function (Blueprint $table) {
            $table->id();
            $table->string('kode')->unique()->comment('ambil_ditempat, kurir');
            $table->string('label')->comment('Ambil di Tempat, Kurir TobaNiaga');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('metode_pengiriman');
    }
};
