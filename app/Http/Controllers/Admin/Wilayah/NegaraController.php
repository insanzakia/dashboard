<?php

namespace App\Http\Controllers\Admin\Wilayah;

use App\Actions\Wilayah\Negara\DeleteNegaraAction;
use App\Actions\Wilayah\Negara\SaveNegaraAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Wilayah\StoreNegaraRequest;
use App\Http\Requests\Wilayah\UpdateNegaraRequest;
use App\Models\Negara;
use App\Repositories\Contracts\WilayahRepositoryInterface;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class NegaraController extends Controller
{
    /** Konfigurasi level yang dikirim ke halaman generik Admin/Wilayah/Index. */
    private const LEVEL = [
        'key' => 'negara',
        'label' => 'Negara',
        'singular' => 'Negara',
        'parentLabel' => null,
        'parentField' => null,
    ];

    public function index(WilayahRepositoryInterface $wilayah): Response
    {
        return Inertia::render('Admin/Wilayah/Index', [
            'level' => self::LEVEL,
            'items' => $wilayah->negaraForAdmin(),
            'parents' => [], // Negara tidak punya parent.
        ]);
    }

    public function store(StoreNegaraRequest $request, SaveNegaraAction $action): RedirectResponse
    {
        $action->execute($request->validated());

        return back()->with('success', 'Negara berhasil ditambahkan.');
    }

    public function update(UpdateNegaraRequest $request, Negara $negara, SaveNegaraAction $action): RedirectResponse
    {
        $action->execute($request->validated(), $negara);

        return back()->with('success', 'Negara berhasil diperbarui.');
    }

    public function destroy(Negara $negara, DeleteNegaraAction $action): RedirectResponse
    {
        $action->execute($negara);

        return back()->with('success', 'Negara berhasil dihapus.');
    }
}
