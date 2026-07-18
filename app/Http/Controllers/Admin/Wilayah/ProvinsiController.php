<?php

namespace App\Http\Controllers\Admin\Wilayah;

use App\Actions\Wilayah\Provinsi\DeleteProvinsiAction;
use App\Actions\Wilayah\Provinsi\SaveProvinsiAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Wilayah\StoreProvinsiRequest;
use App\Http\Requests\Wilayah\UpdateProvinsiRequest;
use App\Models\Provinsi;
use App\Repositories\Contracts\WilayahRepositoryInterface;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class ProvinsiController extends Controller
{
    private const LEVEL = [
        'key' => 'provinsi',
        'label' => 'Provinsi',
        'singular' => 'Provinsi',
        'parentLabel' => 'Regional',
        'parentField' => 'regional_id',
    ];

    public function index(WilayahRepositoryInterface $wilayah): Response
    {
        return Inertia::render('Admin/Wilayah/Index', [
            'level' => self::LEVEL,
            'items' => $wilayah->provinsiForAdmin(),
            'parents' => $wilayah->regionalForAdmin(),
        ]);
    }

    public function store(StoreProvinsiRequest $request, SaveProvinsiAction $action): RedirectResponse
    {
        $action->execute($request->validated());

        return back()->with('success', 'Provinsi berhasil ditambahkan.');
    }

    public function update(UpdateProvinsiRequest $request, Provinsi $provinsi, SaveProvinsiAction $action): RedirectResponse
    {
        $action->execute($request->validated(), $provinsi);

        return back()->with('success', 'Provinsi berhasil diperbarui.');
    }

    public function destroy(Provinsi $provinsi, DeleteProvinsiAction $action): RedirectResponse
    {
        $action->execute($provinsi);

        return back()->with('success', 'Provinsi berhasil dihapus.');
    }
}
