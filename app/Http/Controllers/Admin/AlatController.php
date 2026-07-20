<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Alat\DeleteAlatAction;
use App\Actions\Alat\SaveAlatAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Alat\StoreAlatRequest;
use App\Http\Requests\Alat\UpdateAlatRequest;
use App\Models\Alat;
use App\Repositories\Contracts\AlatRepositoryInterface;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class AlatController extends Controller
{
    public function index(AlatRepositoryInterface $repo): Response
    {
        return Inertia::render('Admin/Alat/Index', [
            'items' => $repo->catalog(),
        ]);
    }

    public function store(StoreAlatRequest $request, SaveAlatAction $action): RedirectResponse
    {
        $action->execute($request->validated());

        return back()->with('success', 'Alat berhasil ditambahkan.');
    }

    public function update(UpdateAlatRequest $request, Alat $alat, SaveAlatAction $action): RedirectResponse
    {
        $action->execute($request->validated(), $alat);

        return back()->with('success', 'Alat berhasil diperbarui.');
    }

    public function destroy(Alat $alat, DeleteAlatAction $action): RedirectResponse
    {
        $action->execute($alat);

        return back()->with('success', 'Alat berhasil dihapus.');
    }
}
