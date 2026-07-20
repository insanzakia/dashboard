import { useCallback } from 'react';
import { useAsyncData } from './useAsyncData';
import { standarService } from '@/services/api/standarService';
import type { DashboardFilter } from '@/types/dashboard';

/** Agregasi pemenuhan alat (rata-rata antar-lab) untuk cakupan wilayah/tier terpilih. */
export function useAggregateFulfillment(filter: DashboardFilter) {
    const { wilayah, tier } = filter;

    const fetcher = useCallback(
        () => standarService.getAggregate(filter),
        [wilayah.regionalId, wilayah.provinsiId, wilayah.kabupatenKotaId, tier],
    );

    return useAsyncData(
        fetcher,
        [wilayah.regionalId, wilayah.provinsiId, wilayah.kabupatenKotaId, tier],
        (data) => data.persen_rata === null,
    );
}
