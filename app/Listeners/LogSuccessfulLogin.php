<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\ActivityLog; // Import Model ActivityLog
use Illuminate\Support\Facades\Log; // Opsional: untuk debugging

class LogSuccessfulLogin
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(Login $event): void
    {
        try {
            // Catat aktivitas login
            ActivityLog::create([
                'user_id' => $event->user->id,
                'activity_type' => 'User logged in',
                'loggable_type' => 'App\Models\User',
                'loggable_id' => $event->user->id,
            ]);
            
            // Log::info('User logged in successfully: ' . $event->user->id); // Opsional: untuk debugging

        } catch (\Exception $e) {
            // Log jika terjadi error saat mencatat log
            Log::error('Gagal mencatat log login: ' . $e->getMessage());
        }
    }
}