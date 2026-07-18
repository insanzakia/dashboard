<?php

namespace App\Http\Controllers\Admin\Wilayah;

use App\Actions\Wilayah\Regional\DeleteRegionalAction;
use App\Actions\Wilayah\Regional\SaveRegionalAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Wilayah\StoreRegionalRequest;
use App\Http\Requests\Wilayah\UpdateRegionalRequest;
use App\Models\Regional;
use App\Repositories\Contracts\WilayahRepositoryInterface;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Admin CRUD Regional (Inertia). Thin controller: validasi via Form Request,
 * eksekusi via Action, kembalikan redirect + flash (format native yang dipahami Inertia).
 */
class RegionalController extends Controller
{
    private const LEVEL = [
        'key' => 'regional',
        'label' => 'Regional',
        'singular' => 'Regional',
        'parentLabel' => 'Negara',
        'parentField' => 'negara_id',
    ];

    public function index(WilayahRepositoryInterface $wilayah): Response
    {
        return Inertia::render('Admin/Wilayah/Index', [
            'level' => self::LEVEL,
            'items' => $wilayah->regionalForAdmin(),
            'parents' => $wilayah->negaraForAdmin(),
        ]);
    }

    public function store(StoreRegionalRequest $request, SaveRegionalAction $action): RedirectResponse
    {
        $action->execute($request->validated());

        return back()->with('success', 'Regional berhasil ditambahkan.');
    }

    public function update(UpdateRegionalRequest $request, Regional $regional, SaveRegionalAction $action): RedirectResponse
    {
        $action->execute($request->validated(), $regional);

        return back()->with('success', 'Regional berhasil diperbarui.');
    }

    public function destroy(Regional $regional, DeleteRegionalAction $action): RedirectResponse
    {
        $action->execute($regional);

        return back()->with('success', 'Regional berhasil dihapus.');
    }
}
