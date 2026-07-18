<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;

class StandarLabkesmasController extends Controller
{
    /**
     * Dashboard Standar Peralatan Labkesmas.
     * Kerangka awal — model data peralatan/standar belum didefinisikan (lihat catatan di PRD berikutnya).
     */
    public function index(): Response
    {
        return Inertia::render('StandarLabkesmas/Index');
    }
}
