<?php

namespace App\Http\Controllers\Admin;

use App\Actions\InventarisAlat\RecordInventarisAlatAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\InventarisAlat\StoreInventarisAlatRequest;
use App\Repositories\Contracts\InventarisAlatRepositoryInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class InventarisAlatController extends Controller
{
    public function index(Request $request, InventarisAlatRepositoryInterface $repo): Response
    {
        $allowed = auth()->user()->allowedLabkesmasIds();
        $selectedId = $request->query('labkesmas_id');

        // Abaikan lab terpilih yang di luar cakupan (jangan bocorkan datanya).
        $allowedSelected = $selectedId !== null && auth()->user()->canAccessLabkesmas($selectedId)
            ? $selectedId
            : null;

        return Inertia::render('Admin/InventarisAlat/Index', [
            'labkesmasOptions' => $repo->labkesmasOptions($allowed),
            'selectedLabkesmasId' => $allowedSelected,
            // Daftar item hanya diambil bila sebuah lab (dalam cakupan) dipilih.
            'items' => $allowedSelected ? $repo->requiredItemsForLab($allowedSelected) : [],
        ]);
    }

    public function store(StoreInventarisAlatRequest $request, RecordInventarisAlatAction $action): RedirectResponse
    {
        $action->execute($request->validated());

        return back()->with('success', 'Data kepemilikan alat berhasil disimpan.');
    }
}
