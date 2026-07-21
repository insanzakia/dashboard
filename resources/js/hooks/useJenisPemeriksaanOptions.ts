import { useCallback } from 'react';
import { useAsyncData } from './useAsyncData';
import { dashboardService } from '@/services/api/dashboardService';

/** Opsi checklist jenis pemeriksaan (dimuat sekali, tidak bergantung pada filter wilayah/tier). */
export function useJenisPemeriksaanOptions() {
    const fetcher = useCallback(() => dashboardService.getJenisPemeriksaanOptions(), []);

    return useAsyncData(fetcher, [], (data) => data.length === 0);
}
