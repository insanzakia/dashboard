<?php

use App\Exceptions\DuplicatePemeriksaanException;
use App\Http\Middleware\HandleInertiaRequests;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            HandleInertiaRequests::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Konversi exception domain menjadi error form yang ramah Inertia (inline di field 'jumlah').
        $exceptions->render(function (DuplicatePemeriksaanException $e, Request $request) {
            return back()->withErrors(['jumlah' => $e->getMessage()])->withInput();
        });

        // Lockout login (rate limiter) → tampilkan pesan ramah inline, bukan modal 429 mentah.
        $exceptions->render(function (ThrottleRequestsException $e, Request $request) {
            if ($request->header('X-Inertia')) {
                $retryAfter = $e->getHeaders()['Retry-After'] ?? 60;

                return back()->withErrors([
                    'username' => "Terlalu banyak percobaan login. Coba lagi dalam {$retryAfter} detik.",
                ]);
            }
        });
    })->create();
