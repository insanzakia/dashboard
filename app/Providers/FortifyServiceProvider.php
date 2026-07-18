<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Laravel\Fortify\Fortify;

/**
 * Konfigurasi Fortify (headless auth) untuk panel admin:
 * - halaman login dirender sebagai komponen React via Inertia,
 * - rate limiter 'login' → lockout setelah percobaan gagal berulang (PRD: keamanan login).
 */
class FortifyServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Fortify menangani POST /login; GET /login menampilkan komponen Inertia ini.
        Fortify::loginView(fn () => Inertia::render('Auth/Login'));

        // Lockout: maksimal 5 percobaan/menit per kombinasi username + IP.
        RateLimiter::for('login', function (Request $request) {
            $username = (string) $request->input(Fortify::username());
            $throttleKey = Str::transliterate(Str::lower($username).'|'.$request->ip());

            return Limit::perMinute(5)->by($throttleKey);
        });
    }
}
