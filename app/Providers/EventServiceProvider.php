<?php

namespace App\Providers;

use Illuminate\Auth\Events\Login; // Tambahkan ini
use Illuminate\Auth\Events\Logout; // Import event Logout
use App\Listeners\LogSuccessfulLogin; // Tambahkan ini
use App\Listeners\LogSuccessfulLogout;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        // \Illuminate\Auth\Events\Registered::class => [
        //     \App\Listeners\SendEmailVerificationNotification::class,
        // ],

        // Bagian yang Anda butuhkan: Mendaftarkan Listener Login
        Login::class => [
            LogSuccessfulLogin::class,
        ],

        Logout::class => [
            LogSuccessfulLogout::class,
        ],

    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        parent::boot();
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}