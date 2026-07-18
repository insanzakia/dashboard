<?php

namespace App\Http\Controllers;

use App\Models\Labkesmas;
use Inertia\Inertia;
use Inertia\Response;

class LandingController extends Controller
{
    /** Halaman sambutan InPULS KEMENKES: total labkesmas + pintu masuk ke dua dashboard. */
    public function index(): Response
    {
        return Inertia::render('Landing/Index', [
            'totalLabkesmas' => Labkesmas::count(),
        ]);
    }
}
