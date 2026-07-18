<?php

namespace App\Http\Controllers\Admin;

use App\Actions\JenisPemeriksaan\DeleteJenisPemeriksaanAction;
use App\Actions\JenisPemeriksaan\SaveJenisPemeriksaanAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\JenisPemeriksaan\StoreJenisPemeriksaanRequest;
use App\Http\Requests\JenisPemeriksaan\UpdateJenisPemeriksaanRequest;
use App\Models\JenisPemeriksaan;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class JenisPemeriksaanController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/JenisPemeriksaan/Index', [
            'items' => JenisPemeriksaan::query()
                ->orderBy('nama_tes')
                ->get(['id', 'nama_tes', 'deskripsi']),
        ]);
    }

    public function store(StoreJenisPemeriksaanRequest $request, SaveJenisPemeriksaanAction $action): RedirectResponse
    {
        $action->execute($request->validated());

        return back()->with('success', 'Jenis pemeriksaan berhasil ditambahkan.');
    }

    public function update(UpdateJenisPemeriksaanRequest $request, JenisPemeriksaan $jenisPemeriksaan, SaveJenisPemeriksaanAction $action): RedirectResponse
    {
        $action->execute($request->validated(), $jenisPemeriksaan);

        return back()->with('success', 'Jenis pemeriksaan berhasil diperbarui.');
    }

    public function destroy(JenisPemeriksaan $jenisPemeriksaan, DeleteJenisPemeriksaanAction $action): RedirectResponse
    {
        $action->execute($jenisPemeriksaan);

        return back()->with('success', 'Jenis pemeriksaan berhasil dihapus.');
    }
}
