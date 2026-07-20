import { useCallback } from 'react';
import { useAsyncData } from './useAsyncData';
import { standarService } from '@/services/api/standarService';
import type { GroupByDimension } from '@/lib/constants';
import type { DashboardFilter } from '@/types/dashboard';

/** Ringkasan % pemenuhan dikelompokkan menurut dimensi (tier/provinsi/regional/kab-kota). */
export function useGroupedFulfillment(filter: DashboardFilter, groupBy: GroupByDimension) {
    const { wilayah, tier } = filter;

    const fetcher = useCallback(
        () => standarService.getGrouped(filter, groupBy),
        [wilayah.regionalId, wilayah.provinsiId, wilayah.kabupatenKotaId, tier, groupBy],
    );

    return useAsyncData(
        fetcher,
        [wilayah.regionalId, wilayah.provinsiId, wilayah.kabupatenKotaId, tier, groupBy],
        (data) => data.length === 0,
    );
}
