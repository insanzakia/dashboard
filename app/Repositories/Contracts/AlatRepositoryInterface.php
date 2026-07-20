<?php

namespace App\Repositories\Contracts;

interface AlatRepositoryInterface
{
    /**
     * Katalog alat + standar per tier (untuk halaman admin "Alat & Standar").
     *
     * @return array<int, array{
     *   id: string, nama_alat: string, kategori: string, keterangan: ?string,
     *   standar: array<int, array{id: string, tier: int, jenis_lab: string, jumlah_minimal: int}>
     * }>
     */
    public function catalog(): array;
}
