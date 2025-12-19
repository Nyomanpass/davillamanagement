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
        Schema::create('villa_fee_histories', function (Blueprint $table) {
            $table->id();
            // Menghubungkan ke tabel villas
            $table->foreignId('villa_id')->constrained()->onDelete('cascade');
            
            $table->unsignedTinyInteger('fee_manajemen')->default(0);
            $table->unsignedTinyInteger('service_karyawan')->default(0);
            $table->date('mulai_berlaku'); // Tanggal mulai berlakunya fee ini
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('villa_fee_histories');
    }
};
