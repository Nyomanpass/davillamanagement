<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('villas', function (Blueprint $table) {
            $table->id();
            $table->string('nama_villa')->unique();
            $table->text('alamat_villa');
            $table->decimal('fee_manajemen', 4, 1)->default(0); 
            $table->decimal('service_karyawan', 4, 1)->default(0);
            $table->unsignedSmallInteger('jumlah_kamar')->default(1);
            $table->string('image_logo')->nullable(); // Path logo
            $table->json('image_gallery')->nullable(); // Path gallery (disimpan sebagai JSON)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('villas');
    }
};
