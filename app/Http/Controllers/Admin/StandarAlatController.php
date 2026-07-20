<?php

namespace App\Http\Controllers\Admin;

use App\Actions\StandarAlat\DeleteStandarAlatAction;
use App\Actions\StandarAlat\SaveStandarAlatAction;
use App\Actions\StandarAlat\SyncStandarAlatAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\StandarAlat\StoreStandarAlatRequest;
use App\Http\Requests\StandarAlat\SyncStandarAlatRequest;
use App\Models\Alat;
use App\Models\StandarAlat;
use Illuminate\Http\RedirectResponse;

class StandarAlatController extends Controller
{
    public function store(StoreStandarAlatRequest $request, SaveStandarAlatAction $action): RedirectResponse
    {
        $action->execute($request->validated());

        return back()->with('success', 'Standar alat berhasil disimpan.');
    }

    /** Simpan seluruh standar satu alat (semua tier) dalam satu aksi. */
    public function sync(SyncStandarAlatRequest $request, Alat $alat, SyncStandarAlatAction $action): RedirectResponse
    {
        $action->execute(['alat_id' => $alat->id, 'items' => $request->validated()['items']]);

        return back()->with('success', 'Standar alat berhasil diperbarui.');
    }

    public function destroy(StandarAlat $standarAlat, DeleteStandarAlatAction $action): RedirectResponse
    {
        $action->execute($standarAlat);

        return back()->with('success', 'Standar alat berhasil dihapus.');
    }
}
