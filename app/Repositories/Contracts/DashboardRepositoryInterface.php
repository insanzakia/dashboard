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

    /**
     * Deret tren bulanan per jenis pemeriksaan, dibatasi pada $jenisTesIds yang diminta.
     * Satu series per jenis (urutan mengikuti $jenisTesIds), titik periode diselaraskan
     * (union periode dari semua jenis terpilih) agar bisa langsung ditumpuk di satu grafik.
     *
     * @param  array<int, string>  $jenisTesIds
     * @return array<int, array{id: string, label: string, points: array<int, array{periode: string, jumlah: int}>}>
     */
    public function trendByJenis(DashboardFilter $filter, array $jenisTesIds): array;

    /**
     * Daftar jenis pemeriksaan untuk checklist publik (bukan CRUD admin).
     *
     * @return array<int, array{id: string, nama_tes: string}>
     */
    public function jenisPemeriksaanOptions(): array;

    /**
     * Deret tren bulanan dikelompokkan menurut dimensi wilayah/tier ('provinsi'|'regional'|'tier').
     * Filter wilayah/tier tetap berlaku sebagai cakupan (kaskade sama seperti scopedQuery); dimensi
     * yang dipilih hanya menentukan bagaimana hasil di dalam cakupan tsb dipecah jadi series.
     *
     * @return array<int, array{id: string, label: string, points: array<int, array{periode: string, jumlah: int}>}>
     */
    public function trendGrouped(DashboardFilter $filter, string $groupBy): array;

    /**
     * Deret tren bulanan untuk beberapa Labkesmas terpilih (perbandingan berdampingan,
     * termasuk kasus satu-vs-satu). Tidak menerapkan filter wilayah/tier — lab yang diminta
     * sudah eksplisit dipilih pengguna.
     *
     * @param  array<int, string>  $labkesmasIds
     * @return array<int, array{id: string, label: string, points: array<int, array{periode: string, jumlah: int}>}>
     */
    public function trendMultiLabkesmas(array $labkesmasIds): array;
}
