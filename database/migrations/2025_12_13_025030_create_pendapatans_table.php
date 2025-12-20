<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pendapatans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('villa_id')->constrained('villas')->onDelete('cascade');
            
            // Relasi ke Category
            $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');
            
            // Kolom Dinamis untuk Room
            $table->date('check_in')->nullable();
            $table->date('check_out')->nullable();
          

            // Kolom Dinamis untuk Item Umum (Laundry, Breakfast, dll)
            $table->string('item_name')->nullable(); 
            $table->integer('qty')->nullable();
            $table->decimal('price_per_item', 15, 2)->nullable();

            // Kolom Utama
            $table->decimal('nominal', 15, 2); // Ini Total Akhir
            $table->date('tanggal'); 
            $table->string('metode_pembayaran'); 
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pendapatans');
    }
};