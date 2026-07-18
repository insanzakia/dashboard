import { useCallback } from 'react';
import { useAsyncData } from './useAsyncData';
import { dashboardService } from '@/services/api/dashboardService';
import type { DashboardFilter } from '@/types/dashboard';

export function useTrendData(filter: DashboardFilter) {
    const { wilayah, tier } = filter;

    const fetcher = useCallback(
        () => dashboardService.getTrend(filter),
        [wilayah.regionalId, wilayah.provinsiId, wilayah.kabupatenKotaId, tier],
    );

    return useAsyncData(
        fetcher,
        [wilayah.regionalId, wilayah.provinsiId, wilayah.kabupatenKotaId, tier],
        (data) => data.every((series) => series.points.length === 0),
    );
}
