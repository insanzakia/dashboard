<?php

namespace App\Repositories\Contracts;

interface DataPemeriksaanRepositoryInterface
{
    /** @return array<int, array{id: string, nama_kantor: string}> */
    public function labkesmasOptions(): array;

    /** @return array<int, array{id: string, nama_tes: string}> */
    public function jenisTesOptions(): array;

    /**
     * Entri pemeriksaan terbaru (untuk tabel di halaman input).
     *
     * @return array<int, array{
     *   id: string, labkesmas_nama: ?string, jenis_nama: ?string,
     *   bulan: int, tahun: int, jumlah: int
     * }>
     */
    public function recentEntries(int $limit = 15): array;
}
