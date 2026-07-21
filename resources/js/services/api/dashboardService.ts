import { apiClient } from './client';
import { DASHBOARD_ENDPOINTS, type TrendGroupByDimension } from '@/lib/constants';
import type { DashboardFilter, SummaryCardData, TrendSeries } from '@/types/dashboard';
import type { JenisTesOption } from '@/types/dataPemeriksaan';

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

    async getTrendByJenis(filter: DashboardFilter, jenisIds: string[]): Promise<TrendSeries[]> {
        const response = await apiClient.get<TrendSeries[]>(DASHBOARD_ENDPOINTS.trendByJenis, {
            params: { ...toQueryParams(filter), jenis_ids: jenisIds },
        });
        return response.data;
    },

    async getJenisPemeriksaanOptions(): Promise<JenisTesOption[]> {
        const response = await apiClient.get<JenisTesOption[]>(DASHBOARD_ENDPOINTS.jenisPemeriksaan);
        return response.data;
    },

    async getTrendGrouped(
        filter: DashboardFilter,
        groupBy: Exclude<TrendGroupByDimension, 'labkesmas'>,
    ): Promise<TrendSeries[]> {
        const response = await apiClient.get<TrendSeries[]>(DASHBOARD_ENDPOINTS.trendGrouped, {
            params: { ...toQueryParams(filter), group_by: groupBy },
        });
        return response.data;
    },

    async getTrendMultiLabkesmas(labIds: string[]): Promise<TrendSeries[]> {
        const response = await apiClient.get<TrendSeries[]>(DASHBOARD_ENDPOINTS.trendMultiLabkesmas, {
            params: { lab_ids: labIds },
        });
        return response.data;
    },
};
