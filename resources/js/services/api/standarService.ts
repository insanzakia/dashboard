import { apiClient } from './client';
import { STANDAR_ENDPOINTS } from '@/lib/constants';
import type { DashboardFilter } from '@/types/dashboard';
import type { AggregateFulfillment, LabComparisonRow, LabFulfillment } from '@/types/pemenuhanAlat';

/** Ubah filter dashboard (wilayah + tier) menjadi query params backend. */
function toQueryParams(filter: DashboardFilter) {
    return {
        regional_id: filter.wilayah.regionalId ?? undefined,
        provinsi_id: filter.wilayah.provinsiId ?? undefined,
        kabupaten_kota_id: filter.wilayah.kabupatenKotaId ?? undefined,
        tier: filter.tier ?? undefined,
    };
}

export const standarService = {
    async getLabFulfillment(labId: string): Promise<LabFulfillment | null> {
        const response = await apiClient.get<LabFulfillment | null>(STANDAR_ENDPOINTS.lab(labId));
        return response.data;
    },

    async getAggregate(filter: DashboardFilter): Promise<AggregateFulfillment> {
        const response = await apiClient.get<AggregateFulfillment>(STANDAR_ENDPOINTS.agregat, {
            params: toQueryParams(filter),
        });
        return response.data;
    },

    async getComparison(filter: DashboardFilter): Promise<LabComparisonRow[]> {
        const response = await apiClient.get<LabComparisonRow[]>(STANDAR_ENDPOINTS.perbandingan, {
            params: toQueryParams(filter),
        });
        return response.data;
    },
};
