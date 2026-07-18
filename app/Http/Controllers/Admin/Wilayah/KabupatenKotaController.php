<?php

namespace App\Http\Controllers\Admin\Wilayah;

use App\Actions\Wilayah\KabupatenKota\DeleteKabupatenKotaAction;
use App\Actions\Wilayah\KabupatenKota\SaveKabupatenKotaAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Wilayah\StoreKabupatenKotaRequest;
use App\Http\Requests\Wilayah\UpdateKabupatenKotaRequest;
use App\Models\KabupatenKota;
use App\Repositories\Contracts\WilayahRepositoryInterface;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class KabupatenKotaController extends Controller
{
    private const LEVEL = [
        'key' => 'kabupaten-kota',
        'label' => 'Kabupaten/Kota',
        'singular' => 'Kabupaten/Kota',
        'parentLabel' => 'Provinsi',
        'parentField' => 'provinsi_id',
    ];

    public function index(WilayahRepositoryInterface $wilayah): Response
    {
        return Inertia::render('Admin/Wilayah/Index', [
            'level' => self::LEVEL,
            'items' => $wilayah->kabupatenKotaForAdmin(),
            'parents' => $wilayah->provinsiForAdmin(),
        ]);
    }

    public function store(StoreKabupatenKotaRequest $request, SaveKabupatenKotaAction $action): RedirectResponse
    {
        $action->execute($request->validated());

        return back()->with('success', 'Kabupaten/Kota berhasil ditambahkan.');
    }

    public function update(UpdateKabupatenKotaRequest $request, KabupatenKota $kabupatenKota, SaveKabupatenKotaAction $action): RedirectResponse
    {
        $action->execute($request->validated(), $kabupatenKota);

        return back()->with('success', 'Kabupaten/Kota berhasil diperbarui.');
    }

    public function destroy(KabupatenKota $kabupatenKota, DeleteKabupatenKotaAction $action): RedirectResponse
    {
        $action->execute($kabupatenKota);

        return back()->with('success', 'Kabupaten/Kota berhasil dihapus.');
    }
}
