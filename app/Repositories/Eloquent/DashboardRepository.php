<?php

namespace App\Repositories\Eloquent;

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
