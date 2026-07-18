<?php

namespace App\Repositories\Eloquent;

use App\Models\KabupatenKota;
use App\Models\Negara;
use App\Models\Provinsi;
use App\Models\Regional;
use App\Repositories\Contracts\WilayahRepositoryInterface;
use Illuminate\Support\Collection;

class WilayahRepository implements WilayahRepositoryInterface
{
    public function listRegional(): Collection
    {
        return Regional::query()->orderBy('nama')->get();
    }

    public function listProvinsi(string $regionalId): Collection
    {
        // where() menggunakan parameterized binding → aman dari SQL injection.
        return Provinsi::query()
            ->where('regional_id', $regionalId)
            ->orderBy('nama')
            ->get();
    }

    public function listKabupatenKota(string $provinsiId): Collection
    {
        return KabupatenKota::query()
            ->where('provinsi_id', $provinsiId)
            ->orderBy('nama')
            ->get();
    }

    public function negaraForAdmin(): array
    {
        return Negara::query()
            ->orderBy('nama')
            ->get()
            ->map(fn (Negara $n) => ['id' => $n->id, 'nama' => $n->nama])
            ->all();
    }

    public function regionalForAdmin(): array
    {
        // Eager load parent agar tidak N+1 saat menampilkan kolom "Negara".
        return Regional::query()
            ->with('negara')
            ->orderBy('nama')
            ->get()
            ->map(fn (Regional $r) => [
                'id' => $r->id,
                'nama' => $r->nama,
                'parent_id' => $r->negara_id,
                'parent_nama' => $r->negara?->nama,
            ])
            ->all();
    }

    public function provinsiForAdmin(): array
    {
        return Provinsi::query()
            ->with('regional')
            ->orderBy('nama')
            ->get()
            ->map(fn (Provinsi $p) => [
                'id' => $p->id,
                'nama' => $p->nama,
                'parent_id' => $p->regional_id,
                'parent_nama' => $p->regional?->nama,
            ])
            ->all();
    }

    public function kabupatenKotaForAdmin(): array
    {
        return KabupatenKota::query()
            ->with('provinsi')
            ->orderBy('nama')
            ->get()
            ->map(fn (KabupatenKota $k) => [
                'id' => $k->id,
                'nama' => $k->nama,
                'parent_id' => $k->provinsi_id,
                'parent_nama' => $k->provinsi?->nama,
            ])
            ->all();
    }
}
