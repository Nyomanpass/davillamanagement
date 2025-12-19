<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 
        'activity_type', 
        'loggable_type', 
        'loggable_id', 
        'details'
    ];
    
    // Pastikan Laravel tahu bahwa kolom 'details' adalah JSON
    protected $casts = [
        'details' => 'array',
    ];

    /**
     * Relasi ke User: Siapa yang melakukan aksi.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi Polymorphic: Model apa yang dimanipulasi.
     */
    public function loggable()
    {
        return $this->morphTo();
    }
}