<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\LogsActivity; 
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pendapatan extends Model
{
    use HasFactory;
    use LogsActivity; 

    protected $table = 'pendapatans'; // Sesuaikan dengan nama tabel di migration terbaru

    protected $fillable = [
        'villa_id', 
        'category_id',     // GANTI: jenis_pendapatan jadi category_id
        'check_in',        // TAMBAHAN: Untuk Room
        'check_out',       // TAMBAHAN: Untuk Room
        'nights',          // TAMBAHAN: Untuk Room
        'price_per_night', // TAMBAHAN: Untuk Room
        'item_name',       // TAMBAHAN: Untuk Umum
        'qty',             // TAMBAHAN: Untuk Umum
        'price_per_item',  // TAMBAHAN: Untuk Umum
        'nominal',         // Tetap (Total Akhir)
        'tanggal', 
        'metode_pembayaran',
        'keterangan',
    ];
    
    protected $casts = [
        'tanggal' => 'date',
        'check_in' => 'date',
        'check_out' => 'date',
        'nominal' => 'decimal:2',
    ];

    /**
     * Relasi ke Category
     * Ini menggantikan string 'jenis_pendapatan'
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
    
    /**
     * Activity Logging Logic
     */
    protected static function booted()
    {
        static::created(function (Pendapatan $pendapatan) {
            $pendapatan->logActivity('Created Pendapatan: ' . ($pendapatan->category->name ?? 'N/A'));
        });

        static::updated(function (Pendapatan $pendapatan) {
            $pendapatan->logActivity('Updated Pendapatan ID: ' . $pendapatan->id);
        });

        static::deleted(function (Pendapatan $pendapatan) {
            $pendapatan->logActivity('Deleted Pendapatan ID: ' . $pendapatan->id);
        });
    }
}