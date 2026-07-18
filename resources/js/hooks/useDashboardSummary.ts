import { useCallback } from 'react';
import { useAsyncData } from './useAsyncData';
import { dashboardService } from '@/services/api/dashboardService';
import type { DashboardFilter } from '@/types/dashboard';

export function useDashboardSummary(filter: DashboardFilter) {
    const { wilayah, tier } = filter;

    const fetcher = useCallback(
        () => dashboardService.getSummary(filter),
        [wilayah.regionalId, wilayah.provinsiId, wilayah.kabupatenKotaId, tier],
    );

    return useAsyncData(
        fetcher,
        [wilayah.regionalId, wilayah.provinsiId, wilayah.kabupatenKotaId, tier],
        (data) => data.length === 0,
    );
}
