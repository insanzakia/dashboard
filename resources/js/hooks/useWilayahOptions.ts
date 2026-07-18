import { useCallback } from 'react';
import { useAsyncData } from './useAsyncData';
import { wilayahService } from '@/services/api/wilayahService';

/**
 * Mengelola data pilihan (options) untuk 3 dropdown kaskade Wilayah.
 * Provinsi hanya di-fetch setelah Regional dipilih, Kabupaten/Kota setelah Provinsi dipilih —
 * jika parent belum dipilih, fetcher resolve ke array kosong (state 'empty', bukan 'loading' selamanya).
 */
export function useWilayahOptions(regionalId: string | null, provinsiId: string | null) {
    const regional = useAsyncData(
        useCallback(() => wilayahService.getRegional(), []),
        [],
        (data) => data.length === 0,
    );

    const provinsi = useAsyncData(
        useCallback(
            () => (regionalId ? wilayahService.getProvinsi(regionalId) : Promise.resolve([])),
            [regionalId],
        ),
        [regionalId],
        (data) => data.length === 0,
    );

    const kabupatenKota = useAsyncData(
        useCallback(
            () => (provinsiId ? wilayahService.getKabupatenKota(provinsiId) : Promise.resolve([])),
            [provinsiId],
        ),
        [provinsiId],
        (data) => data.length === 0,
    );

    return { regional, provinsi, kabupatenKota };
}
