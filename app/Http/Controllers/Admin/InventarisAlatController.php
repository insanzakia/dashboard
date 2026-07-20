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
        $selectedId = $request->query('labkesmas_id');

        return Inertia::render('Admin/InventarisAlat/Index', [
            'labkesmasOptions' => $repo->labkesmasOptions(),
            'selectedLabkesmasId' => $selectedId,
            // Daftar item hanya diambil bila sebuah lab dipilih (via query string).
            'items' => $selectedId ? $repo->requiredItemsForLab($selectedId) : [],
        ]);
    }

    public function store(StoreInventarisAlatRequest $request, RecordInventarisAlatAction $action): RedirectResponse
    {
        $action->execute($request->validated());

        return back()->with('success', 'Data kepemilikan alat berhasil disimpan.');
    }
}
