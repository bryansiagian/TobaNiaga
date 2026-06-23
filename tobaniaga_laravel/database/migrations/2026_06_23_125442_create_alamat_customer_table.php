<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alamat_customer', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')
                  ->constrained('users')
                  ->cascadeOnDelete();
            $table->string('label')->comment('Rumah, Kantor, dll');
            $table->text('alamat_lengkap');
            $table->string('kecamatan');
            $table->string('desa');
            $table->string('kode_pos')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->boolean('is_default')->default(false);
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alamat_customer');
    }
};
