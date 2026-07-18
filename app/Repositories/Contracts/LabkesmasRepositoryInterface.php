<?php

namespace App\Repositories\Contracts;

interface LabkesmasRepositoryInterface
{
    /**
     * Daftar labkesmas untuk tabel admin, lengkap dengan konteks lokasi (kab/kota → provinsi → regional)
     * agar form edit dapat memulihkan pilihan kaskade.
     *
     * @return array<int, array{
     *   id: string, nama_kantor: string, tier_labkesmas: int,
     *   kabupaten_kota_id: string, kabupaten_nama: ?string,
     *   provinsi_id: ?string, provinsi_nama: ?string,
     *   regional_id: ?string, regional_nama: ?string
     * }>
     */
    public function labkesmasForAdmin(): array;
}
