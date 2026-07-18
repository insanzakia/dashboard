<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Catat waktu login terakhir setiap kali autentikasi berhasil (audit dasar, PRD Section 6).
        Event::listen(Login::class, function (Login $event): void {
            if ($event->user instanceof User) {
                $event->user->forceFill(['last_login' => now()])->saveQuietly();
            }
        });
    }
}
