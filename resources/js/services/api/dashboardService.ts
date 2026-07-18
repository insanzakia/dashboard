import { apiClient } from './client';
import { DASHBOARD_ENDPOINTS } from '@/lib/constants';
import type { DashboardFilter, SummaryCardData, TrendSeries } from '@/types/dashboard';

function toQueryParams(filter: DashboardFilter) {
    return {
        regional_id: filter.wilayah.regionalId ?? undefined,
        provinsi_id: filter.wilayah.provinsiId ?? undefined,
        kabupaten_kota_id: filter.wilayah.kabupatenKotaId ?? undefined,
        tier: filter.tier ?? undefined,
    };
}

export const dashboardService = {
    async getSummary(filter: DashboardFilter): Promise<SummaryCardData[]> {
        const response = await apiClient.get<SummaryCardData[]>(DASHBOARD_ENDPOINTS.summary, {
            params: toQueryParams(filter),
        });
        return response.data;
    },

    async getTrend(filter: DashboardFilter): Promise<TrendSeries[]> {
        const response = await apiClient.get<TrendSeries[]>(DASHBOARD_ENDPOINTS.trend, {
            params: toQueryParams(filter),
        });
        return response.data;
    },
};
