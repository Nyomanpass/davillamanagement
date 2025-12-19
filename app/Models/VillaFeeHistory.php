<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VillaFeeHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'villa_id',
        'fee_manajemen',
        'service_karyawan',
        'mulai_berlaku'
    ];

    // Mengubah string tanggal otomatis menjadi objek Carbon
    protected $casts = [
        'mulai_berlaku' => 'date',
    ];

    // Relasi balik ke Villa
    public function villa()
    {
        return $this->belongsTo(Villa::class);
    }
}