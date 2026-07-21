import { useCallback } from 'react';
import { useAsyncData } from './useAsyncData';
import { dashboardService } from '@/services/api/dashboardService';

/** Tren bulanan untuk beberapa Labkesmas terpilih. Tidak memanggil API selama belum ada yang dicentang. */
export function useTrendMultiLabkesmas(labIds: string[]) {
    const key = labIds.join(',');

    const fetcher = useCallback(
        () => (labIds.length === 0 ? Promise.resolve([]) : dashboardService.getTrendMultiLabkesmas(labIds)),
        // eslint-disable-next-line react-hooks/exhaustive-deps
        [key],
    );

    return useAsyncData(fetcher, [key], (data) => data.length === 0);
}
