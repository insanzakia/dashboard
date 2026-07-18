<?php

namespace App\Repositories\Contracts;

use App\Support\DashboardFilter;

/**
 * Kontrak agregasi data dashboard. Semua query berat (JOIN + GROUP BY) hidup di implementasi,
 * tidak bocor ke controller.
 */
interface DashboardRepositoryInterface
{
    /**
     * Kartu ringkasan "First Win" sesuai cakupan filter.
     *
     * @return array<int, array{id: string, label: string, totalPemeriksaan: int, deltaPercentase: float|null}>
     */
    public function summary(DashboardFilter $filter): array;

    /**
     * Deret tren bulanan sesuai cakupan filter.
     *
     * @return array<int, array{id: string, label: string, points: array<int, array{periode: string, jumlah: int}>}>
     */
    public function trend(DashboardFilter $filter): array;
}
