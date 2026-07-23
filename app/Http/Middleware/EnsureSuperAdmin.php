<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Batasi akses ke fitur khusus super_admin (kelola akun & master data).
 * Akun 'admin' terbatas hanya boleh input data sesuai cakupannya.
 */
class EnsureSuperAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        abort_unless($request->user()?->isSuperAdmin() === true, 403, 'Hanya super admin yang boleh mengakses halaman ini.');

        return $next($request);
    }
}
