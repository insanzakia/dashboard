import { useCallback } from 'react';
import { useAsyncData } from './useAsyncData';
import { dashboardService } from '@/services/api/dashboardService';
import type { DashboardFilter } from '@/types/dashboard';

/** Tren bulanan per jenis pemeriksaan terpilih. Tidak memanggil API selama belum ada yang dicentang. */
export function useTrendByJenis(filter: DashboardFilter, jenisIds: string[]) {
    const { wilayah, tier } = filter;
    const jenisKey = jenisIds.join(',');

    const fetcher = useCallback(
        () => (jenisIds.length === 0 ? Promise.resolve([]) : dashboardService.getTrendByJenis(filter, jenisIds)),
        // eslint-disable-next-line react-hooks/exhaustive-deps
        [wilayah.regionalId, wilayah.provinsiId, wilayah.kabupatenKotaId, tier, jenisKey],
    );

    return useAsyncData(
        fetcher,
        [wilayah.regionalId, wilayah.provinsiId, wilayah.kabupatenKotaId, tier, jenisKey],
        (data) => data.length === 0,
    );
}
