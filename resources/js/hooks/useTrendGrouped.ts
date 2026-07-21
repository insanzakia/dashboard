import { useCallback } from 'react';
import { useAsyncData } from './useAsyncData';
import { dashboardService } from '@/services/api/dashboardService';
import type { TrendGroupByDimension } from '@/lib/constants';
import type { DashboardFilter } from '@/types/dashboard';

type GroupableDimension = Exclude<TrendGroupByDimension, 'labkesmas'>;

/**
 * Tren bulanan dikelompokkan menurut provinsi/regional/tier (dalam cakupan filter aktif).
 * groupBy null → tidak memanggil API (dipakai saat mode "Labkesmas" aktif di komponen pemanggil).
 */
export function useTrendGrouped(filter: DashboardFilter, groupBy: GroupableDimension | null) {
    const { wilayah, tier } = filter;

    const fetcher = useCallback(
        () => (groupBy === null ? Promise.resolve([]) : dashboardService.getTrendGrouped(filter, groupBy)),
        // eslint-disable-next-line react-hooks/exhaustive-deps
        [wilayah.regionalId, wilayah.provinsiId, wilayah.kabupatenKotaId, tier, groupBy],
    );

    return useAsyncData(
        fetcher,
        [wilayah.regionalId, wilayah.provinsiId, wilayah.kabupatenKotaId, tier, groupBy],
        (data) => data.length === 0,
    );
}
