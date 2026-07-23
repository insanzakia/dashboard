<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Labkesmas\DeleteLabkesmasAction;
use App\Actions\Labkesmas\RegisterLabkesmasAction;
use App\Actions\Labkesmas\UpdateLabkesmasAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Labkesmas\StoreLabkesmasRequest;
use App\Http\Requests\Labkesmas\UpdateLabkesmasRequest;
use App\Models\Labkesmas;
use App\Repositories\Contracts\LabkesmasRepositoryInterface;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class LabkesmasController extends Controller
{
    public function index(LabkesmasRepositoryInterface $labkesmas): Response
    {
        return Inertia::render('Admin/Labkesmas/Index', [
            'items' => $labkesmas->labkesmasForAdmin(auth()->user()->allowedLabkesmasIds()),
        ]);
    }

    public function store(StoreLabkesmasRequest $request, RegisterLabkesmasAction $action): RedirectResponse
    {
        $action->execute($request->validated());

        return back()->with('success', 'Labkesmas berhasil didaftarkan.');
    }

    public function update(UpdateLabkesmasRequest $request, Labkesmas $labkesmas, UpdateLabkesmasAction $action): RedirectResponse
    {
        $action->execute($labkesmas, $request->validated());

        return back()->with('success', 'Labkesmas berhasil diperbarui.');
    }

    public function destroy(Labkesmas $labkesmas, DeleteLabkesmasAction $action): RedirectResponse
    {
        abort_unless(auth()->user()->canAccessLabkesmas($labkesmas->id), 403);

        $action->execute($labkesmas);

        return back()->with('success', 'Labkesmas berhasil dihapus.');
    }
}
