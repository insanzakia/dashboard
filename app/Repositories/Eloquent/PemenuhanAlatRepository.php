<?php

namespace App\Repositories\Eloquent;

use App\Models\Labkesmas;
use App\Repositories\Contracts\PemenuhanAlatRepositoryInterface;
use App\Support\DashboardFilter;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\DB;

/**
 * Menghitung persentase pemenuhan alat (berbasis JENIS alat) Labkesmas terhadap standar KMK.
 * Semua query pakai query builder terparameter → aman dari SQL injection.
 */
class PemenuhanAlatRepository implements PemenuhanAlatRepositoryInterface
{
    public function labFulfillment(string $labkesmasId): ?array
    {
        $lab = Labkesmas::find($labkesmasId, ['id', 'nama_kantor', 'tier_labkesmas', 'jenis_lab']);

        if ($lab === null) {
            return null;
        }

        $effJenis = (int) $lab->tier_labkesmas === 5 ? ($lab->jenis_lab ?? '') : 'umum';

        $items = DB::table('standar_alat as sa')
            ->join('alat as a', 'a.id', '=', 'sa.alat_id')
            ->leftJoin('inventaris_alat as inv', function (JoinClause $join) use ($labkesmasId) {
                $join->on('inv.alat_id', '=', 'sa.alat_id')
                    ->where('inv.labkesmas_id', '=', $labkesmasId);
            })
            ->where('sa.tier', (int) $lab->tier_labkesmas)
            ->where('sa.jenis_lab', $effJenis)
            ->orderBy('a.kategori')
            ->orderBy('a.nama_alat')
            ->selectRaw('a.nama_alat, a.kategori, sa.jumlah_minimal, COALESCE(inv.jumlah, 0) as jumlah_dimiliki')
            ->get();

        $itemsOut = [];
        $perKategori = [];   // kategori => [wajib, terpenuhi]
        $totalWajib = 0;
        $totalTerpenuhi = 0;

        foreach ($items as $row) {
            $wajib = (int) $row->jumlah_minimal;
            $dimiliki = (int) $row->jumlah_dimiliki;
            $terpenuhi = $dimiliki >= $wajib;
            // Status ASPAK 3-nilai: sesuai (≥standar) / kurang (0<dimiliki<standar) / tidak_ada (0).
            $status = $terpenuhi ? 'sesuai' : ($dimiliki > 0 ? 'kurang' : 'tidak_ada');

            $itemsOut[] = [
                'nama_alat' => $row->nama_alat,
                'kategori' => $row->kategori,
                'jumlah_minimal' => $wajib,
                'jumlah_dimiliki' => $dimiliki,
                'terpenuhi' => $terpenuhi,
                'status' => $status,
            ];

            $perKategori[$row->kategori] ??= ['wajib' => 0, 'terpenuhi' => 0];
            $perKategori[$row->kategori]['wajib']++;
            $totalWajib++;
            if ($terpenuhi) {
                $perKategori[$row->kategori]['terpenuhi']++;
                $totalTerpenuhi++;
            }
        }

        $perKategoriOut = [];
        foreach ($perKategori as $kategori => $c) {
            $perKategoriOut[] = [
                'kategori' => $kategori,
                'wajib' => $c['wajib'],
                'terpenuhi' => $c['terpenuhi'],
                'persen' => $this->persen($c['terpenuhi'], $c['wajib']) ?? 0.0,
            ];
        }

        return [
            'labkesmas' => [
                'id' => $lab->id,
                'nama_kantor' => $lab->nama_kantor,
                'tier' => (int) $lab->tier_labkesmas,
                'jenis_lab' => $lab->jenis_lab,
            ],
            'persen_total' => $this->persen($totalTerpenuhi, $totalWajib),
            'total_wajib' => $totalWajib,
            'total_terpenuhi' => $totalTerpenuhi,
            'per_kategori' => $perKategoriOut,
            'items' => $itemsOut,
        ];
    }

    public function aggregateFulfillment(DashboardFilter $filter): array
    {
        $rows = $this->perLabCategory($filter);

        // Kumpulkan per lab (untuk % keseluruhan) dan per (lab, kategori).
        $perLab = [];            // labId => [wajib, terpenuhi]
        $perLabKategori = [];    // kategori => labId => [wajib, terpenuhi]

        foreach ($rows as $r) {
            $labId = $r->labkesmas_id;
            $kat = $r->kategori;
            $wajib = (int) $r->wajib;
            $terpenuhi = (int) $r->terpenuhi;

            $perLab[$labId] ??= ['wajib' => 0, 'terpenuhi' => 0];
            $perLab[$labId]['wajib'] += $wajib;
            $perLab[$labId]['terpenuhi'] += $terpenuhi;

            $perLabKategori[$kat][$labId] = ['wajib' => $wajib, 'terpenuhi' => $terpenuhi];
        }

        // % keseluruhan = rata-rata % antar-lab (lab dengan wajib > 0).
        $labPersen = [];
        foreach ($perLab as $c) {
            $p = $this->persen($c['terpenuhi'], $c['wajib']);
            if ($p !== null) {
                $labPersen[] = $p;
            }
        }

        $perKategoriOut = [];
        foreach ($perLabKategori as $kategori => $labs) {
            $persenList = [];
            foreach ($labs as $c) {
                $p = $this->persen($c['terpenuhi'], $c['wajib']);
                if ($p !== null) {
                    $persenList[] = $p;
                }
            }
            if ($persenList !== []) {
                $perKategoriOut[] = [
                    'kategori' => $kategori,
                    'persen_rata' => round(array_sum($persenList) / count($persenList), 1),
                    'jumlah_lab' => count($persenList),
                ];
            }
        }

        // Urutkan kategori mengikuti % tertinggi agar rapi di UI.
        usort($perKategoriOut, fn ($a, $b) => $b['persen_rata'] <=> $a['persen_rata']);

        return [
            'persen_rata' => $labPersen === [] ? null : round(array_sum($labPersen) / count($labPersen), 1),
            'jumlah_lab' => count($labPersen),
            'per_kategori' => $perKategoriOut,
        ];
    }

    public function labComparison(DashboardFilter $filter): array
    {
        $rows = $this->perLabTotals($filter);

        $out = array_map(fn ($r) => [
            'labkesmas_id' => $r->labkesmas_id,
            'nama_kantor' => $r->nama_kantor,
            'tier' => (int) $r->tier,
            'jenis_lab' => $r->jenis_lab,
            'total_wajib' => (int) $r->wajib,
            'total_terpenuhi' => (int) $r->terpenuhi,
            'persen' => $this->persen((int) $r->terpenuhi, (int) $r->wajib),
        ], $rows);

        // Urut % menurun; lab tanpa standar (persen null) diletakkan paling akhir.
        usort($out, fn ($a, $b) => ($b['persen'] ?? -1) <=> ($a['persen'] ?? -1));

        return $out;
    }

    public function groupedFulfillment(DashboardFilter $filter, string $groupBy): array
    {
        $rows = DB::table('labkesmas as l')
            ->join('kabupaten_kota as kk', 'kk.id', '=', 'l.kabupaten_kota_id')
            ->join('provinsi as p', 'p.id', '=', 'kk.provinsi_id')
            ->join('regional as r', 'r.id', '=', 'p.regional_id')
            ->leftJoin('standar_alat as sa', fn (JoinClause $join) => $this->matchStandar($join))
            ->leftJoin('inventaris_alat as inv', fn (JoinClause $join) => $this->matchInventaris($join))
            ->when($filter->kabupatenKotaId, fn ($q) => $q->where('l.kabupaten_kota_id', $filter->kabupatenKotaId))
            ->when($filter->provinsiId && ! $filter->kabupatenKotaId, fn ($q) => $q->where('kk.provinsi_id', $filter->provinsiId))
            ->when($filter->regionalId && ! $filter->provinsiId && ! $filter->kabupatenKotaId, fn ($q) => $q->where('p.regional_id', $filter->regionalId))
            ->when($filter->tier !== null, fn ($q) => $q->where('l.tier_labkesmas', $filter->tier))
            ->groupBy('l.id', 'l.tier_labkesmas', 'p.id', 'p.nama', 'r.id', 'r.nama', 'kk.id', 'kk.nama')
            ->selectRaw(
                'l.id as lab_id, l.tier_labkesmas as tier, p.id as prov_id, p.nama as prov_nama, '
                .'r.id as reg_id, r.nama as reg_nama, kk.id as kab_id, kk.nama as kab_nama, '
                .'COUNT(sa.id) as wajib, '
                .'SUM(CASE WHEN COALESCE(inv.jumlah, 0) >= sa.jumlah_minimal THEN 1 ELSE 0 END) as terpenuhi'
            )
            ->get();

        // Hitung % per lab lalu kelompokkan & rata-ratakan menurut dimensi terpilih.
        $groups = [];
        foreach ($rows as $r) {
            $p = $this->persen((int) $r->terpenuhi, (int) $r->wajib);
            if ($p === null) {
                continue;
            }
            [$key, $label] = match ($groupBy) {
                'provinsi' => [$r->prov_id, $r->prov_nama],
                'regional' => [$r->reg_id, $r->reg_nama],
                'kabupaten_kota' => [$r->kab_id, $r->kab_nama],
                default => ['tier-'.$r->tier, 'Tier '.$r->tier],
            };
            $groups[$key]['label'] = $label;
            $groups[$key]['tier'] = (int) $r->tier;
            $groups[$key]['persens'][] = $p;
        }

        $out = [];
        foreach ($groups as $key => $g) {
            $out[] = [
                'key' => (string) $key,
                'label' => $g['label'],
                'persen_rata' => round(array_sum($g['persens']) / count($g['persens']), 1),
                'jumlah_lab' => count($g['persens']),
            ];
        }

        if ($groupBy === 'tier') {
            usort($out, fn ($a, $b) => $a['key'] <=> $b['key']);
        } else {
            usort($out, fn ($a, $b) => $b['persen_rata'] <=> $a['persen_rata']);
        }

        return $out;
    }

    public function multiLabFulfillment(array $labIds): array
    {
        $out = [];
        foreach ($labIds as $id) {
            $f = $this->labFulfillment($id);
            if ($f === null) {
                continue;
            }
            $out[] = [
                'labkesmas' => $f['labkesmas'],
                'persen_total' => $f['persen_total'],
                'total_wajib' => $f['total_wajib'],
                'total_terpenuhi' => $f['total_terpenuhi'],
                'per_kategori' => $f['per_kategori'],
            ];
        }

        return $out;
    }

    /**
     * Total wajib & terpenuhi per lab (LEFT JOIN → lab tanpa standar tetap muncul, wajib=0).
     *
     * @return array<int, object>
     */
    private function perLabTotals(DashboardFilter $filter): array
    {
        return $this->scoped($filter)
            ->leftJoin('standar_alat as sa', fn (JoinClause $join) => $this->matchStandar($join))
            ->leftJoin('inventaris_alat as inv', fn (JoinClause $join) => $this->matchInventaris($join))
            ->groupBy('l.id', 'l.nama_kantor', 'l.tier_labkesmas', 'l.jenis_lab')
            ->selectRaw(
                'l.id as labkesmas_id, l.nama_kantor, l.tier_labkesmas as tier, l.jenis_lab, '
                .'COUNT(sa.id) as wajib, '
                .'SUM(CASE WHEN COALESCE(inv.jumlah, 0) >= sa.jumlah_minimal THEN 1 ELSE 0 END) as terpenuhi'
            )
            ->get()
            ->all();
    }

    /**
     * Wajib & terpenuhi per (lab, kategori) (INNER JOIN → hanya lab yang punya standar).
     *
     * @return array<int, object>
     */
    private function perLabCategory(DashboardFilter $filter): array
    {
        return $this->scoped($filter)
            ->join('standar_alat as sa', fn (JoinClause $join) => $this->matchStandar($join))
            ->join('alat as a', 'a.id', '=', 'sa.alat_id')
            ->leftJoin('inventaris_alat as inv', fn (JoinClause $join) => $this->matchInventaris($join))
            ->groupBy('l.id', 'a.kategori')
            ->selectRaw(
                'l.id as labkesmas_id, a.kategori, '
                .'COUNT(sa.id) as wajib, '
                .'SUM(CASE WHEN COALESCE(inv.jumlah, 0) >= sa.jumlah_minimal THEN 1 ELSE 0 END) as terpenuhi'
            )
            ->get()
            ->all();
    }

    /**
     * Query dasar pada labkesmas (alias `l`) dengan filter wilayah kaskade + tier.
     * Meniru DashboardRepository::scopedQuery, tapi berbasis tabel labkesmas.
     */
    private function scoped(DashboardFilter $filter): Builder
    {
        $query = DB::table('labkesmas as l');

        if ($filter->kabupatenKotaId) {
            $query->where('l.kabupaten_kota_id', $filter->kabupatenKotaId);
        } elseif ($filter->provinsiId) {
            $query->join('kabupaten_kota as kk', 'kk.id', '=', 'l.kabupaten_kota_id')
                ->where('kk.provinsi_id', $filter->provinsiId);
        } elseif ($filter->regionalId) {
            $query->join('kabupaten_kota as kk', 'kk.id', '=', 'l.kabupaten_kota_id')
                ->join('provinsi as p', 'p.id', '=', 'kk.provinsi_id')
                ->where('p.regional_id', $filter->regionalId);
        }

        if ($filter->tier !== null) {
            $query->where('l.tier_labkesmas', $filter->tier);
        }

        return $query;
    }

    /**
     * Kondisi join standar_alat ↔ labkesmas: samakan tier & jenis_lab efektif
     * (tier 5 → jenis lab tsb; tier lain → 'umum').
     */
    private function matchStandar(JoinClause $join): void
    {
        $join->on('sa.tier', '=', 'l.tier_labkesmas')
            ->whereRaw("sa.jenis_lab = CASE WHEN l.tier_labkesmas = 5 THEN COALESCE(l.jenis_lab, '') ELSE 'umum' END");
    }

    /** Kondisi LEFT JOIN inventaris: milik lab tsb DAN untuk alat pada baris standar. */
    private function matchInventaris(JoinClause $join): void
    {
        $join->on('inv.labkesmas_id', '=', 'l.id')
            ->on('inv.alat_id', '=', 'sa.alat_id');
    }

    private function persen(int $terpenuhi, int $wajib): ?float
    {
        if ($wajib === 0) {
            return null;
        }

        return round($terpenuhi / $wajib * 100, 1);
    }
}
