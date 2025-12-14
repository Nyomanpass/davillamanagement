<?php



namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Pendapatan extends Model
{
    use HasFactory;

    protected $table = 'pendapatan'; 

    protected $fillable = [
        'villa_id', 
        'jenis_pendapatan',
        'nominal',
        'tanggal',
        'metode_pembayaran',
    ];
    
    protected $casts = [
        'tanggal' => 'date', 
    ];

    /**
     * Relasi: Pendapatan milik satu Villa
     */
    public function villa()
    {
        // Pastikan Model Villa sudah ada
        return $this->belongsTo(Villa::class); 
    }
}