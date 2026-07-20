import { useCallback } from 'react';
import { useAsyncData } from './useAsyncData';
import { standarService } from '@/services/api/standarService';
import type { MultiLabRow } from '@/types/pemenuhanAlat';

/**
 * Pemenuhan beberapa lab terpilih. Bila belum ada lab dipilih, status 'empty'
 * (tanpa memanggil API).
 */
export function useMultiLabFulfillment(labIds: string[]) {
    const key = labIds.join(',');

    const fetcher = useCallback(
        (): Promise<MultiLabRow[]> => (labIds.length === 0 ? Promise.resolve([]) : standarService.getMulti(labIds)),
        [key],
    );

    return useAsyncData(fetcher, [key], (data) => data.length === 0);
}
