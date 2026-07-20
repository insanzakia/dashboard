<?php

namespace App\Repositories\Contracts;

use App\Support\DashboardFilter;

interface PemenuhanAlatRepositoryInterface
{
    /**
     * Rincian pemenuhan alat satu Labkesmas terhadap standar tier-nya.
     * Metrik berbasis JENIS alat (dimiliki ≥ standar = terpenuhi).
     *
     * @return array{
     *   labkesmas: array{id: string, nama_kantor: string, tier: int, jenis_lab: ?string},
     *   persen_total: ?float, total_wajib: int, total_terpenuhi: int,
     *   per_kategori: array<int, array{kategori: string, wajib: int, terpenuhi: int, persen: float}>,
     *   items: array<int, array{nama_alat: string, kategori: string, jumlah_minimal: int, jumlah_dimiliki: int, terpenuhi: bool, status: string}>
     * }|null  null jika lab tidak ditemukan.
     */
    public function labFulfillment(string $labkesmasId): ?array;

    /**
     * Rata-rata % pemenuhan dikelompokkan menurut dimensi tertentu, dalam cakupan filter.
     *
     * @param  string  $groupBy  'tier' | 'provinsi' | 'regional' | 'kabupaten_kota'
     * @return array<int, array{key: string, label: string, persen_rata: float, jumlah_lab: int}>
     */
    public function groupedFulfillment(DashboardFilter $filter, string $groupBy): array;

    /**
     * Rincian ringkas pemenuhan beberapa lab terpilih (untuk perbandingan berdampingan).
     *
     * @param  array<int, string>  $labIds
     * @return array<int, array{
     *   labkesmas: array{id: string, nama_kantor: string, tier: int, jenis_lab: ?string},
     *   persen_total: ?float, total_wajib: int, total_terpenuhi: int,
     *   per_kategori: array<int, array{kategori: string, wajib: int, terpenuhi: int, persen: float}>
     * }>
     */
    public function multiLabFulfillment(array $labIds): array;

    /**
     * Agregasi pemenuhan pada cakupan wilayah/tier (rata-rata antar-lab).
     *
     * @return array{
     *   persen_rata: ?float, jumlah_lab: int,
     *   per_kategori: array<int, array{kategori: string, persen_rata: float, jumlah_lab: int}>
     * }
     */
    public function aggregateFulfillment(DashboardFilter $filter): array;

    /**
     * Perbandingan pemenuhan antar-lab dalam cakupan (urut % menurun).
     *
     * @return array<int, array{
     *   labkesmas_id: string, nama_kantor: string, tier: int, jenis_lab: ?string,
     *   total_wajib: int, total_terpenuhi: int, persen: ?float
     * }>
     */
    public function labComparison(DashboardFilter $filter): array;
}
