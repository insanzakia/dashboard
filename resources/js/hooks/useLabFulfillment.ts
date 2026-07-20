import { useCallback } from 'react';
import { useAsyncData } from './useAsyncData';
import { standarService } from '@/services/api/standarService';
import type { LabFulfillment } from '@/types/pemenuhanAlat';

/**
 * Rincian pemenuhan alat satu Labkesmas. Bila belum ada lab terpilih (labId null),
 * status akan 'empty' (frontend menampilkan ajakan memilih lab).
 */
export function useLabFulfillment(labId: string | null) {
    const fetcher = useCallback(
        (): Promise<LabFulfillment | null> =>
            labId ? standarService.getLabFulfillment(labId) : Promise.resolve(null),
        [labId],
    );

    return useAsyncData(fetcher, [labId], (data) => data === null);
}
