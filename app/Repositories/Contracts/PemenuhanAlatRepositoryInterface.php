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
     *   items: array<int, array{nama_alat: string, kategori: string, jumlah_minimal: int, jumlah_dimiliki: int, terpenuhi: bool}>
     * }|null  null jika lab tidak ditemukan.
     */
    public function labFulfillment(string $labkesmasId): ?array;

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
