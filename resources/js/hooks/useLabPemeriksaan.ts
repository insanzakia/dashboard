import { useCallback } from 'react';
import { useAsyncData } from './useAsyncData';
import { dashboardService } from '@/services/api/dashboardService';

/** Ringkasan pemeriksaan satu Labkesmas (total + per jenis + tren) untuk halaman profil. */
export function useLabPemeriksaan(labId: string) {
    const fetcher = useCallback(() => dashboardService.getLabPemeriksaan(labId), [labId]);

    return useAsyncData(fetcher, [labId], (data) => data.total === 0);
}
