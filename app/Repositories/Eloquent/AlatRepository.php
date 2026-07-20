<?php

namespace App\Repositories\Eloquent;

use App\Models\Alat;
use App\Repositories\Contracts\AlatRepositoryInterface;

class AlatRepository implements AlatRepositoryInterface
{
    public function catalog(): array
    {
        return Alat::query()
            ->with(['standar' => fn ($q) => $q->orderBy('tier')->orderBy('jenis_lab')])
            ->orderBy('kategori')
            ->orderBy('nama_alat')
            ->get()
            ->map(fn (Alat $alat) => [
                'id' => $alat->id,
                'nama_alat' => $alat->nama_alat,
                'kategori' => $alat->kategori,
                'keterangan' => $alat->keterangan,
                'standar' => $alat->standar
                    ->map(fn ($s) => [
                        'id' => $s->id,
                        'tier' => (int) $s->tier,
                        'jenis_lab' => $s->jenis_lab,
                        'jumlah_minimal' => (int) $s->jumlah_minimal,
                    ])
                    ->all(),
            ])
            ->all();
    }
}
