<?php

namespace App\Repositories\Eloquent;

use App\Models\JenisPemeriksaan;
use App\Models\KabupatenKota;
use App\Models\Provinsi;
use App\Models\Regional;
use App\Repositories\Contracts\DashboardRepositoryInterface;
use App\Support\DashboardFilter;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

class DashboardRepository implements DashboardRepositoryInterface
{
    /** Jumlah periode terakhir yang ditampilkan pada grafik tren. */
    private const TREND_PERIODS = 6;

    public function summary(DashboardFilter $filter): array
    {
        $total = (int) $this->scopedQuery($filter)->sum('dp.jumlah');

        // Tidak ada data dalam cakupan → kembalikan array kosong (frontend menampilkan Empty State).
        if ($total === 0) {
            return [];
        }

        $periods = $this->periodTotals($filter);
        $latest = end($periods) ?: null;
        $previous = prev($periods) ?: null;

        $latestTotal = $latest ? (int) $latest->jumlah : 0;
        $deltaPercentase = $this->deltaPercentage(
            current: $latestTotal,
            previous: $previous ? (int) $previous->jumlah : null,
        );

        $labkesmasAktif = (int) $this->scopedQuery($filter)->distinct()->count('dp.labkesmas_id');

        return [
            [
                'id' => 'total-pemeriksaan',
                'label' => 'Total Pemeriksaan',
                'totalPemeriksaan' => $total,
                'deltaPercentase' => null,
            ],
            [
                'id' => 'pemeriksaan-periode-terakhir',
                'label' => 'Pemeriksaan Periode Terakhir',
                'totalPemeriksaan' => $latestTotal,
                'deltaPercentase' => $deltaPercentase,
            ],
            [
                'id' => 'labkesmas-aktif',
                'label' => 'Labkesmas Aktif',
                'totalPemeriksaan' => $labkesmasAktif,
                'deltaPercentase' => null,
            ],
        ];
    }

    public function trend(DashboardFilter $filter): array
    {
        $rows = $this->periodTotals($filter);

        if (empty($rows)) {
            return [];
        }

        // Ambil TREND_PERIODS periode terakhir saja.
        $rows = array_slice($rows, -self::TREND_PERIODS);

        $points = array_map(
            fn ($row) => [
                'periode' => sprintf('%04d-%02d', $row->tahun, $row->bulan),
                'jumlah' => (int) $row->jumlah,
            ],
            $rows,
        );

        return [
            [
                'id' => 'jumlah-pemeriksaan',
                'label' => $this->scopeLabel($filter),
                'points' => array_values($points),
            ],
        ];
    }

    public function trendByJenis(DashboardFilter $filter, array $jenisTesIds): array
    {
        if (empty($jenisTesIds)) {
            return [];
        }

        $rows = $this->scopedQuery($filter)
            ->join('jenis_pemeriksaan as jp', 'jp.id', '=', 'dp.jenis_tes_id')
            ->whereIn('dp.jenis_tes_id', $jenisTesIds)
            ->selectRaw('dp.jenis_tes_id, jp.nama_tes, dp.tahun, dp.bulan, SUM(dp.jumlah) as jumlah')
            ->groupBy('dp.jenis_tes_id', 'jp.nama_tes', 'dp.tahun', 'dp.bulan')
            ->orderBy('dp.tahun')
            ->orderBy('dp.bulan')
            ->get();

        if ($rows->isEmpty()) {
            return [];
        }

        // Union periode dari semua jenis terpilih, TREND_PERIODS terakhir — supaya tiap
        // series punya titik x yang sama persis walau salah satu jenis kosong di suatu bulan.
        $periods = $rows->map(fn ($row) => sprintf('%04d-%02d', $row->tahun, $row->bulan))
            ->unique()
            ->sort()
            ->values()
            ->slice(-self::TREND_PERIODS)
            ->all();

        $byJenis = $rows->groupBy('jenis_tes_id');

        $series = [];
        foreach ($jenisTesIds as $id) {
            $rowsForJenis = $byJenis->get($id);

            if (! $rowsForJenis || $rowsForJenis->isEmpty()) {
                continue;
            }

            $pointsByPeriode = $rowsForJenis->keyBy(
                fn ($row) => sprintf('%04d-%02d', $row->tahun, $row->bulan)
            );

            $series[] = [
                'id' => $id,
                'label' => $rowsForJenis->first()->nama_tes,
                'points' => array_map(
                    fn ($periode) => [
                        'periode' => $periode,
                        'jumlah' => isset($pointsByPeriode[$periode]) ? (int) $pointsByPeriode[$periode]->jumlah : 0,
                    ],
                    $periods,
                ),
            ];
        }

        return $series;
    }

    public function jenisPemeriksaanOptions(): array
    {
        return JenisPemeriksaan::query()
            ->orderBy('nama_tes')
            ->get(['id', 'nama_tes'])
            ->map(fn (JenisPemeriksaan $j) => ['id' => $j->id, 'nama_tes' => $j->nama_tes])
            ->all();
    }

    public function trendGrouped(DashboardFilter $filter, string $groupBy): array
    {
        $rows = DB::table('data_pemeriksaan as dp')
            ->join('labkesmas as l', 'l.id', '=', 'dp.labkesmas_id')
            ->join('kabupaten_kota as kk', 'kk.id', '=', 'l.kabupaten_kota_id')
            ->join('provinsi as p', 'p.id', '=', 'kk.provinsi_id')
            ->join('regional as r', 'r.id', '=', 'p.regional_id')
            ->when($filter->kabupatenKotaId, fn ($q) => $q->where('l.kabupaten_kota_id', $filter->kabupatenKotaId))
            ->when(
                $filter->provinsiId && ! $filter->kabupatenKotaId,
                fn ($q) => $q->where('kk.provinsi_id', $filter->provinsiId)
            )
            ->when(
                $filter->regionalId && ! $filter->provinsiId && ! $filter->kabupatenKotaId,
                fn ($q) => $q->where('p.regional_id', $filter->regionalId)
            )
            ->when($filter->tier !== null, fn ($q) => $q->where('l.tier_labkesmas', $filter->tier))
            ->groupBy('p.id', 'p.nama', 'r.id', 'r.nama', 'l.tier_labkesmas', 'dp.tahun', 'dp.bulan')
            ->selectRaw(
                'p.id as prov_id, p.nama as prov_nama, r.id as reg_id, r.nama as reg_nama, '
                .'l.tier_labkesmas as tier, dp.tahun, dp.bulan, SUM(dp.jumlah) as jumlah'
            )
            ->orderBy('dp.tahun')
            ->orderBy('dp.bulan')
            ->get();

        if ($rows->isEmpty()) {
            return [];
        }

        $periods = $rows->map(fn ($row) => sprintf('%04d-%02d', $row->tahun, $row->bulan))
            ->unique()
            ->sort()
            ->values()
            ->slice(-self::TREND_PERIODS)
            ->all();

        // Kumpulkan & jumlahkan per (kunci dimensi, periode) — beberapa baris SQL (mis. beda tier/kab)
        // bisa jatuh ke kunci yang sama (mis. groupBy provinsi), makanya dijumlahkan di sini, bukan di SQL.
        $byGroup = [];
        foreach ($rows as $row) {
            [$key, $label] = match ($groupBy) {
                'provinsi' => [$row->prov_id, $row->prov_nama],
                'regional' => [$row->reg_id, $row->reg_nama],
                default => ['tier-'.$row->tier, 'Tier '.$row->tier],
            };
            $periode = sprintf('%04d-%02d', $row->tahun, $row->bulan);
            $byGroup[$key]['label'] = $label;
            $byGroup[$key]['points'][$periode] = ($byGroup[$key]['points'][$periode] ?? 0) + (int) $row->jumlah;
        }

        $series = [];
        foreach ($byGroup as $key => $group) {
            $series[] = [
                'id' => (string) $key,
                'label' => $group['label'],
                'points' => array_map(
                    fn ($periode) => ['periode' => $periode, 'jumlah' => $group['points'][$periode] ?? 0],
                    $periods,
                ),
            ];
        }

        usort(
            $series,
            $groupBy === 'tier'
                ? fn ($a, $b) => $a['id'] <=> $b['id']
                : fn ($a, $b) => $a['label'] <=> $b['label'],
        );

        return $series;
    }

    public function trendMultiLabkesmas(array $labkesmasIds): array
    {
        if (empty($labkesmasIds)) {
            return [];
        }

        $rows = DB::table('data_pemeriksaan as dp')
            ->join('labkesmas as l', 'l.id', '=', 'dp.labkesmas_id')
            ->whereIn('dp.labkesmas_id', $labkesmasIds)
            ->selectRaw('dp.labkesmas_id, l.nama_kantor, dp.tahun, dp.bulan, SUM(dp.jumlah) as jumlah')
            ->groupBy('dp.labkesmas_id', 'l.nama_kantor', 'dp.tahun', 'dp.bulan')
            ->orderBy('dp.tahun')
            ->orderBy('dp.bulan')
            ->get();

        if ($rows->isEmpty()) {
            return [];
        }

        $periods = $rows->map(fn ($row) => sprintf('%04d-%02d', $row->tahun, $row->bulan))
            ->unique()
            ->sort()
            ->values()
            ->slice(-self::TREND_PERIODS)
            ->all();

        $byLab = $rows->groupBy('labkesmas_id');

        $series = [];
        foreach ($labkesmasIds as $id) {
            $rowsForLab = $byLab->get($id);

            if (! $rowsForLab || $rowsForLab->isEmpty()) {
                continue;
            }

            $pointsByPeriode = $rowsForLab->keyBy(
                fn ($row) => sprintf('%04d-%02d', $row->tahun, $row->bulan)
            );

            $series[] = [
                'id' => $id,
                'label' => $rowsForLab->first()->nama_kantor,
                'points' => array_map(
                    fn ($periode) => [
                        'periode' => $periode,
                        'jumlah' => isset($pointsByPeriode[$periode]) ? (int) $pointsByPeriode[$periode]->jumlah : 0,
                    ],
                    $periods,
                ),
            ];
        }

        return $series;
    }

    public function labPemeriksaan(string $labkesmasId): array
    {
        $rows = DB::table('data_pemeriksaan as dp')
            ->join('jenis_pemeriksaan as jp', 'jp.id', '=', 'dp.jenis_tes_id')
            ->where('dp.labkesmas_id', $labkesmasId)
            ->selectRaw('dp.jenis_tes_id, jp.nama_tes, dp.tahun, dp.bulan, SUM(dp.jumlah) as jumlah')
            ->groupBy('dp.jenis_tes_id', 'jp.nama_tes', 'dp.tahun', 'dp.bulan')
            ->orderBy('dp.tahun')
            ->orderBy('dp.bulan')
            ->get();

        if ($rows->isEmpty()) {
            return ['total' => 0, 'per_jenis' => [], 'trend' => []];
        }

        // Periode yang sama untuk semua jenis (TREND_PERIODS terakhir) agar tren bisa ditumpuk.
        $periods = $rows->map(fn ($row) => sprintf('%04d-%02d', $row->tahun, $row->bulan))
            ->unique()
            ->sort()
            ->values()
            ->slice(-self::TREND_PERIODS)
            ->all();

        $byJenis = $rows->groupBy('jenis_tes_id');

        $total = 0;
        $perJenis = [];
        $trend = [];

        foreach ($byJenis as $jenisId => $jenisRows) {
            $namaTes = $jenisRows->first()->nama_tes;
            $jenisTotal = (int) $jenisRows->sum('jumlah');
            $total += $jenisTotal;

            $perJenis[] = ['id' => (string) $jenisId, 'nama_tes' => $namaTes, 'total' => $jenisTotal];

            $pointsByPeriode = $jenisRows->keyBy(fn ($row) => sprintf('%04d-%02d', $row->tahun, $row->bulan));
            $trend[] = [
                'id' => (string) $jenisId,
                'label' => $namaTes,
                'points' => array_map(
                    fn ($periode) => [
                        'periode' => $periode,
                        'jumlah' => isset($pointsByPeriode[$periode]) ? (int) $pointsByPeriode[$periode]->jumlah : 0,
                    ],
                    $periods,
                ),
            ];
        }

        // Rincian per jenis diurutkan dari total terbesar.
        usort($perJenis, fn ($a, $b) => $b['total'] <=> $a['total']);

        return ['total' => $total, 'per_jenis' => $perJenis, 'trend' => $trend];
    }

    /**
     * Query dasar pada data_pemeriksaan yang sudah menerapkan filter wilayah + tier.
     * Memakai query builder (binding otomatis) → aman dari SQL injection.
     */
    private function scopedQuery(DashboardFilter $filter): Builder
    {
        $query = DB::table('data_pemeriksaan as dp')
            ->join('labkesmas as l', 'l.id', '=', 'dp.labkesmas_id');

        // Filter wilayah bersifat kaskade: level paling spesifik yang menang.
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

        // Filter tier independen — bisa digabung dengan filter wilayah.
        if ($filter->tier !== null) {
            $query->where('l.tier_labkesmas', $filter->tier);
        }

        return $query;
    }

    /**
     * Total jumlah per periode (tahun, bulan), terurut kronologis.
     *
     * @return array<int, object>
     */
    private function periodTotals(DashboardFilter $filter): array
    {
        return $this->scopedQuery($filter)
            ->selectRaw('dp.tahun, dp.bulan, SUM(dp.jumlah) as jumlah')
            ->groupBy('dp.tahun', 'dp.bulan')
            ->orderBy('dp.tahun')
            ->orderBy('dp.bulan')
            ->get()
            ->all();
    }

    private function deltaPercentage(int $current, ?int $previous): ?float
    {
        if ($previous === null || $previous === 0) {
            return null;
        }

        return round((($current - $previous) / $previous) * 100, 1);
    }

    /** Label deret tren yang menjelaskan cakupan aktif (mis. "Sumatera · Tier 4"). */
    private function scopeLabel(DashboardFilter $filter): string
    {
        $parts = [];

        if ($filter->kabupatenKotaId) {
            $parts[] = KabupatenKota::find($filter->kabupatenKotaId)?->nama ?? 'Wilayah Terpilih';
        } elseif ($filter->provinsiId) {
            $parts[] = Provinsi::find($filter->provinsiId)?->nama ?? 'Provinsi Terpilih';
        } elseif ($filter->regionalId) {
            $parts[] = Regional::find($filter->regionalId)?->nama ?? 'Regional Terpilih';
        }

        if ($filter->tier !== null) {
            $parts[] = "Tier {$filter->tier}";
        }

        return empty($parts) ? 'Nasional' : implode(' · ', $parts);
    }
}
