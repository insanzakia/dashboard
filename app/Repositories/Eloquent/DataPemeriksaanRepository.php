<?php

namespace App\Repositories\Eloquent;

use App\Models\JenisPemeriksaan;
use App\Models\Labkesmas;
use App\Repositories\Contracts\DataPemeriksaanRepositoryInterface;
use Illuminate\Support\Facades\DB;

class DataPemeriksaanRepository implements DataPemeriksaanRepositoryInterface
{
    public function labkesmasOptions(?array $allowedLabkesmasIds = null): array
    {
        return Labkesmas::query()
            ->when($allowedLabkesmasIds !== null, fn ($q) => $q->whereIn('id', $allowedLabkesmasIds))
            ->orderBy('nama_kantor')
            ->get(['id', 'nama_kantor'])
            ->map(fn (Labkesmas $l) => ['id' => $l->id, 'nama_kantor' => $l->nama_kantor])
            ->all();
    }

    public function jenisTesOptions(): array
    {
        return JenisPemeriksaan::query()
            ->orderBy('nama_tes')
            ->get(['id', 'nama_tes'])
            ->map(fn (JenisPemeriksaan $j) => ['id' => $j->id, 'nama_tes' => $j->nama_tes])
            ->all();
    }

    public function recentEntries(int $limit = 15, ?array $allowedLabkesmasIds = null): array
    {
        // JOIN via query builder (parameterized) → aman dari SQL injection.
        // "Terbaru" = paling baru disentuh (updated_at), agar entri yang baru diinput/diperbarui
        // langsung muncul di atas apa pun periodenya.
        return DB::table('data_pemeriksaan as dp')
            ->join('labkesmas as l', 'l.id', '=', 'dp.labkesmas_id')
            ->join('jenis_pemeriksaan as j', 'j.id', '=', 'dp.jenis_tes_id')
            ->when($allowedLabkesmasIds !== null, fn ($q) => $q->whereIn('dp.labkesmas_id', $allowedLabkesmasIds))
            ->orderByDesc('dp.updated_at')
            ->orderByDesc('dp.tahun')
            ->orderByDesc('dp.bulan')
            ->limit($limit)
            ->get([
                'dp.id',
                'l.nama_kantor as labkesmas_nama',
                'j.nama_tes as jenis_nama',
                'dp.bulan',
                'dp.tahun',
                'dp.jumlah',
            ])
            ->map(fn ($row) => [
                'id' => $row->id,
                'labkesmas_nama' => $row->labkesmas_nama,
                'jenis_nama' => $row->jenis_nama,
                'bulan' => (int) $row->bulan,
                'tahun' => (int) $row->tahun,
                'jumlah' => (int) $row->jumlah,
            ])
            ->all();
    }
}
