import { useCallback } from 'react';
import { useAsyncData } from './useAsyncData';
import { standarService } from '@/services/api/standarService';
import type { DashboardFilter } from '@/types/dashboard';

/** Perbandingan pemenuhan alat antar-lab dalam cakupan wilayah/tier terpilih. */
export function useLabComparison(filter: DashboardFilter) {
    const { wilayah, tier } = filter;

    const fetcher = useCallback(
        () => standarService.getComparison(filter),
        [wilayah.regionalId, wilayah.provinsiId, wilayah.kabupatenKotaId, tier],
    );

    return useAsyncData(
        fetcher,
        [wilayah.regionalId, wilayah.provinsiId, wilayah.kabupatenKotaId, tier],
        (data) => data.length === 0,
    );
}
