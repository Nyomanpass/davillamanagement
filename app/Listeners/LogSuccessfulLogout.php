<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Logout; // Import event Logout
use App\Models\ActivityLog; // Import Model ActivityLog
use Illuminate\Support\Facades\Log;

class LogSuccessfulLogout
{
    /**
     * Handle the event.
     */
    public function handle(Logout $event): void
    {
        // PENTING: Saat logout, data user masih tersedia di $event->user
        // Namun, pastikan data $event->user tidak null
        if ($event->user) {
            try {
                // Catat aktivitas logout
                ActivityLog::create([
                    'user_id' => $event->user->id,
                    'activity_type' => 'User logged out',
                    'loggable_type' => 'App\Models\User',
                    'loggable_id' => $event->user->id,
                ]);
            } catch (\Exception $e) {
                Log::error('Gagal mencatat log logout: ' . $e->getMessage());
            }
        }
    }
}