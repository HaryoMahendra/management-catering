<?php

namespace App\Providers;

use App\Helpers\ActivityHelper; 
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        Event::listen(Login::class, function ($event) {
            $event->user->update([
                'last_login_at' => now(),
                'last_login_ip' => request()->ip(),
            ]);

            // Catat otomatis ke tabel log aktivitas
            ActivityHelper::record(
                'Login', 
                "User {$event->user->name} ({$event->user->email}) berhasil masuk ke sistem."
            );
        });
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}