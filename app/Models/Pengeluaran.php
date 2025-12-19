<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\LogsActivity; 
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pengeluaran extends Model
{
    use HasFactory;
    use LogsActivity; 

    protected $table = 'pengeluarans';

    protected $fillable = [
        'villa_id',
        'category_id',      // GANTI: dari jenis_pengeluaran ke category_id
        'nama_pengeluaran', // TAMBAHAN: Detail barang/jasa
        'qty',              // TAMBAHAN: Jumlah
        'satuan',           // TAMBAHAN: Pcs, Kg, dll
        'harga_satuan',     // TAMBAHAN: Harga per unit
        'nominal',          // Tetap (Total Akhir)
        'tanggal',
        'metode_pembayaran',// TAMBAHAN: Cash/Transfer
        'keterangan',
    ];

    protected $casts = [
        'tanggal' => 'date', 
        'nominal' => 'decimal:2',
        'harga_satuan' => 'decimal:2',
    ];

    /**
     * Relasi ke Category (Expense Type)
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    /**
     * Relasi ke Villa
     */
    public function villa(): BelongsTo
    {
        return $this->belongsTo(Villa::class);
    }

    protected static function booted()
    {
        static::created(function (Pengeluaran $pengeluaran) {
            // Log lebih detail dengan nama kategori
            $catName = $pengeluaran->category->name ?? 'N/A';
            $pengeluaran->logActivity("Created Pengeluaran: [{$catName}] {$pengeluaran->nama_pengeluaran}");
        });

        static::updated(function (Pengeluaran $pengeluaran) {
            $pengeluaran->logActivity("Updated Pengeluaran ID: {$pengeluaran->id}");
        });

        static::deleted(function (Pengeluaran $pengeluaran) {
            $pengeluaran->logActivity("Deleted Pengeluaran ID: {$pengeluaran->id}");
        });
    }
}