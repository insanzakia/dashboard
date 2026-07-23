<?php

namespace App\Repositories\Contracts;

interface InventarisAlatRepositoryInterface
{
    /**
     * @param  array<int, string>|null  $allowedLabkesmasIds  null = semua (super_admin); array = batasi ke id tsb.
     * @return array<int, array{id: string, nama_kantor: string, tier_labkesmas: int, jenis_lab: ?string}>
     */
    public function labkesmasOptions(?array $allowedLabkesmasIds = null): array;

    /**
     * Daftar alat yang DIWAJIBKAN untuk tier lab tsb, beserta jumlah yang sudah dimiliki.
     * Dipakai form input inventaris (grid per kategori).
     *
     * @return array<int, array{
     *   alat_id: string, nama_alat: string, kategori: string,
     *   jumlah_minimal: int, jumlah_dimiliki: int
     * }>
     */
    public function requiredItemsForLab(string $labkesmasId): array;
}
