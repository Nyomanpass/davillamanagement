<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengeluaran extends Model
{
    use HasFactory;

    protected $table = 'pengeluarans';

    protected $fillable = [
        'villa_id',
        'jenis_pengeluaran',
        'tanggal',
        'nominal',
        'keterangan',
    ];

    // Tambahkan baris ini
    protected $casts = [
        'tanggal' => 'date', 
    ];

    public function villa()
    {
        return $this->belongsTo(Villa::class);
    }
}