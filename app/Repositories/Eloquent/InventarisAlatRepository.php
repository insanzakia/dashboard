<?php

namespace App\Repositories\Eloquent;

use App\Models\Labkesmas;
use App\Repositories\Contracts\InventarisAlatRepositoryInterface;
use Illuminate\Support\Facades\DB;

class InventarisAlatRepository implements InventarisAlatRepositoryInterface
{
    public function labkesmasOptions(): array
    {
        return Labkesmas::query()
            ->orderBy('nama_kantor')
            ->get(['id', 'nama_kantor', 'tier_labkesmas', 'jenis_lab'])
            ->map(fn (Labkesmas $l) => [
                'id' => $l->id,
                'nama_kantor' => $l->nama_kantor,
                'tier_labkesmas' => (int) $l->tier_labkesmas,
                'jenis_lab' => $l->jenis_lab,
            ])
            ->all();
    }

    public function requiredItemsForLab(string $labkesmasId): array
    {
        $lab = Labkesmas::find($labkesmasId, ['id', 'tier_labkesmas', 'jenis_lab']);

        if ($lab === null) {
            return [];
        }

        // Untuk tier 5 gunakan jenis lab spesifik; tier 2-4 selalu 'umum'.
        $effJenis = (int) $lab->tier_labkesmas === 5 ? ($lab->jenis_lab ?? '') : 'umum';

        return DB::table('standar_alat as sa')
            ->join('alat as a', 'a.id', '=', 'sa.alat_id')
            ->leftJoin('inventaris_alat as inv', function ($join) use ($labkesmasId) {
                $join->on('inv.alat_id', '=', 'sa.alat_id')
                    ->where('inv.labkesmas_id', '=', $labkesmasId);
            })
            ->where('sa.tier', (int) $lab->tier_labkesmas)
            ->where('sa.jenis_lab', $effJenis)
            ->orderBy('a.kategori')
            ->orderBy('a.nama_alat')
            ->selectRaw('sa.alat_id, a.nama_alat, a.kategori, sa.jumlah_minimal, COALESCE(inv.jumlah, 0) as jumlah_dimiliki')
            ->get()
            ->map(fn ($row) => [
                'alat_id' => $row->alat_id,
                'nama_alat' => $row->nama_alat,
                'kategori' => $row->kategori,
                'jumlah_minimal' => (int) $row->jumlah_minimal,
                'jumlah_dimiliki' => (int) $row->jumlah_dimiliki,
            ])
            ->all();
    }
}
