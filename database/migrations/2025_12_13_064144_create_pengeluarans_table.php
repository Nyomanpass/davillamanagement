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
        Schema::create('pengeluarans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('villa_id')->constrained('villas')->onDelete('cascade');
            
            // Relasi ke Category (untuk grouping: Operational, Maintenance, Gaji, dll)
            $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');
            
            // Detail Pengeluaran
            $table->string('nama_pengeluaran'); // Contoh: "Beli Lampu Kamar 1" atau "Bayar Listrik Feb"
            $table->decimal('qty', 10, 2)->default(1); // Pakai decimal agar bisa 1.5 kg
            $table->string('satuan')->nullable(); // Pcs, Kg, Bulan, Lot
            $table->decimal('harga_satuan', 15, 2)->nullable();
            
            // Total Akhir
            $table->decimal('nominal', 15, 2); 
            $table->enum('jenis_beban', ['operasional', 'non_operasional'])->default('operasional');
            $table->date('tanggal');
            $table->string('metode_pembayaran'); // Cash, Transfer, Petty Cash
            $table->text('keterangan')->nullable(); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengeluarans');
    }
};
