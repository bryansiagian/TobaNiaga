<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('promo_umkm', function (Blueprint $table) {
            $table->id();
            $table->foreignId('promo_id')
                  ->constrained('promo')
                  ->cascadeOnDelete();
            $table->foreignId('umkm_id')
                  ->constrained('umkm')
                  ->cascadeOnDelete();
            $table->unique(['promo_id', 'umkm_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('promo_umkm');
    }
};
