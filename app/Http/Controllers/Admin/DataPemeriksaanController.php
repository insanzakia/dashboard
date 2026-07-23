<?php

namespace App\Http\Controllers\Admin;

use App\Actions\DataPemeriksaan\DeleteDataPemeriksaanAction;
use App\Actions\DataPemeriksaan\RecordDataPemeriksaanAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\DataPemeriksaan\StoreDataPemeriksaanRequest;
use App\Models\DataPemeriksaan;
use App\Repositories\Contracts\DataPemeriksaanRepositoryInterface;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class DataPemeriksaanController extends Controller
{
    public function index(DataPemeriksaanRepositoryInterface $repo): Response
    {
        // Batasi opsi & daftar ke cakupan akun (null = super_admin lihat semua).
        $allowed = auth()->user()->allowedLabkesmasIds();

        return Inertia::render('Admin/DataPemeriksaan/Index', [
            'labkesmasOptions' => $repo->labkesmasOptions($allowed),
            'jenisTesOptions' => $repo->jenisTesOptions(),
            'recentEntries' => $repo->recentEntries(15, $allowed),
        ]);
    }

    public function store(StoreDataPemeriksaanRequest $request, RecordDataPemeriksaanAction $action): RedirectResponse
    {
        $action->execute($request->validated());

        return back()->with('success', 'Data pemeriksaan berhasil disimpan.');
    }

    public function destroy(DataPemeriksaan $dataPemeriksaan, DeleteDataPemeriksaanAction $action): RedirectResponse
    {
        abort_unless(auth()->user()->canAccessLabkesmas($dataPemeriksaan->labkesmas_id), 403);

        $action->execute($dataPemeriksaan);

        return back()->with('success', 'Data pemeriksaan berhasil dihapus.');
    }
}
