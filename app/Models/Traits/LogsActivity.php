<?php

namespace App\Models\Traits;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

trait LogsActivity
{
    public function logActivity(string $type, array $details = [])
    {
        // Pastikan ada pengguna yang login sebelum mencatat
        if (Auth::check()) {
            ActivityLog::create([
                'user_id' => Auth::id(), // Mengambil ID pengguna yang sedang aktif
                'activity_type' => $type,
                'loggable_type' => self::class, // Class Model saat ini (Pendapatan/Pengeluaran)
                'loggable_id' => $this->id, // ID data yang dimanipulasi
                'details' => $details,
            ]);
        }
    }
}