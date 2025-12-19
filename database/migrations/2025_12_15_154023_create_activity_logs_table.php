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
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                  ->constrained() // Membuat foreign key ke tabel users
                  ->onDelete('cascade'); // Jika user dihapus, lognya ikut dihapus
            $table->string('activity_type', 100); 
            $table->string('loggable_type')->nullable(); // Class Model (e.g., App\Models\Pendapatan)
            $table->unsignedBigInteger('loggable_id')->nullable(); // ID record yang terpengaruh (e.g., ID Pendapatan)
            $table->json('details')->nullable(); 
            $table->timestamps(); 
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};