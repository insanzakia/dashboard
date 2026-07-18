<?php

namespace App\Repositories\Contracts;

use Illuminate\Support\Collection;

/**
 * Kontrak akses data hierarki wilayah.
 * - method list*  : untuk filter kaskade publik (Resource-shaped di controller).
 * - method *ForAdmin : untuk tabel & dropdown parent di panel admin (sudah di-shape jadi array).
 * Controller/Action bergantung pada abstraksi ini, bukan implementasi Eloquent konkret (DIP).
 */
interface WilayahRepositoryInterface
{
    public function listRegional(): Collection;

    public function listProvinsi(string $regionalId): Collection;

    public function listKabupatenKota(string $provinsiId): Collection;

    /** @return array<int, array{id: string, nama: string}> */
    public function negaraForAdmin(): array;

    /** @return array<int, array{id: string, nama: string, parent_id: string, parent_nama: ?string}> */
    public function regionalForAdmin(): array;

    /** @return array<int, array{id: string, nama: string, parent_id: string, parent_nama: ?string}> */
    public function provinsiForAdmin(): array;

    /** @return array<int, array{id: string, nama: string, parent_id: string, parent_nama: ?string}> */
    public function kabupatenKotaForAdmin(): array;
}
