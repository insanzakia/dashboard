<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;

/**
 * Halaman "Akun Saya" — info akun + form ganti password.
 * Data akun (username/role) sudah di-share via HandleInertiaRequests (auth.user),
 * jadi controller cukup me-render halaman. Ganti password ditangani Fortify (PUT /user/password).
 */
class AkunController extends Controller
{
    public function edit(): Response
    {
        return Inertia::render('Akun/Edit');
    }
}
