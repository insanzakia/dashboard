<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Models\KabupatenKota;
use App\Models\Labkesmas;
use App\Models\Provinsi;
use App\Models\Regional;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Kelola akun (khusus super_admin): buat akun 'admin' terbatas + atur cakupan wilayahnya.
 * Akun super_admin lain tidak dapat diubah/dihapus dari sini (hindari lockout).
 */
class UserController extends Controller
{
    public function index(): Response
    {
        $regional = Regional::pluck('nama', 'id');
        $provinsi = Provinsi::pluck('nama', 'id');
        $kabupatenKota = KabupatenKota::pluck('nama', 'id');
        $labkesmas = Labkesmas::pluck('nama_kantor', 'id');

        $labelFor = fn (string $type, string $id): string => match ($type) {
            'regional' => $regional[$id] ?? '—',
            'provinsi' => $provinsi[$id] ?? '—',
            'kabupaten_kota' => $kabupatenKota[$id] ?? '—',
            'labkesmas' => $labkesmas[$id] ?? '—',
            default => '—',
        };

        $users = User::with('scopes')->orderBy('username')->get()->map(fn (User $u) => [
            'id' => $u->id,
            'username' => $u->username,
            'role' => $u->role,
            'is_super_admin' => $u->isSuperAdmin(),
            'scopes' => $u->scopes->map(fn ($s) => [
                'scope_type' => $s->scope_type,
                'scope_id' => $s->scope_id,
                'label' => $labelFor($s->scope_type, $s->scope_id),
            ])->values(),
        ])->values();

        return Inertia::render('Admin/Users/Index', [
            'users' => $users,
            // Opsi untuk pembuat cakupan (pilih level → entitas), dengan relasi induk untuk kaskade.
            'wilayah' => [
                'regional' => Regional::orderBy('nama')->get(['id', 'nama']),
                'provinsi' => Provinsi::orderBy('nama')->get(['id', 'nama', 'regional_id']),
                'kabupaten_kota' => KabupatenKota::orderBy('nama')->get(['id', 'nama', 'provinsi_id']),
                'labkesmas' => Labkesmas::orderBy('nama_kantor')->get(['id', 'nama_kantor', 'kabupaten_kota_id']),
            ],
        ]);
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $data = $request->validated();

        DB::transaction(function () use ($data) {
            $user = User::create([
                'username' => $data['username'],
                'password' => $data['password'], // cast 'hashed' otomatis
                'role' => 'admin',               // hanya boleh membuat role di bawah super_admin
            ]);

            $this->syncScopes($user, $data['scopes'] ?? []);
        });

        return back()->with('success', 'Akun admin berhasil dibuat.');
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        abort_if($user->isSuperAdmin(), 403, 'Akun super admin tidak dapat diubah dari sini.');

        $data = $request->validated();

        DB::transaction(function () use ($user, $data) {
            if (! empty($data['password'])) {
                $user->update(['password' => $data['password']]);
            }
            $this->syncScopes($user, $data['scopes'] ?? []);
        });

        return back()->with('success', 'Akun berhasil diperbarui.');
    }

    public function destroy(User $user): RedirectResponse
    {
        abort_if($user->isSuperAdmin(), 403, 'Akun super admin tidak dapat dihapus dari sini.');
        abort_if($user->id === auth()->id(), 403);

        $user->delete(); // user_scopes ikut terhapus (cascadeOnDelete)

        return back()->with('success', 'Akun berhasil dihapus.');
    }

    /**
     * Ganti seluruh cakupan akun dengan set baru (verifikasi entitas ada, buang duplikat).
     *
     * @param  array<int, array{scope_type: string, scope_id: string}>  $scopes
     */
    private function syncScopes(User $user, array $scopes): void
    {
        $valid = [];
        foreach ($scopes as $scope) {
            $type = $scope['scope_type'];
            $id = $scope['scope_id'];

            $exists = match ($type) {
                'regional' => Regional::whereKey($id)->exists(),
                'provinsi' => Provinsi::whereKey($id)->exists(),
                'kabupaten_kota' => KabupatenKota::whereKey($id)->exists(),
                'labkesmas' => Labkesmas::whereKey($id)->exists(),
                default => false,
            };

            if ($exists) {
                $valid["$type|$id"] = ['scope_type' => $type, 'scope_id' => $id];
            }
        }

        $user->scopes()->delete();
        foreach ($valid as $entry) {
            $user->scopes()->create($entry);
        }
    }
}
