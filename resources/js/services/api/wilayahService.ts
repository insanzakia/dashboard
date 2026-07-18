import { apiClient } from './client';
import { DASHBOARD_ENDPOINTS } from '@/lib/constants';
import type { Regional, Provinsi, KabupatenKota } from '@/types/wilayah';

export const wilayahService = {
    async getRegional(): Promise<Regional[]> {
        const response = await apiClient.get<Regional[]>(DASHBOARD_ENDPOINTS.regional);
        return response.data;
    },

    async getProvinsi(regionalId: string): Promise<Provinsi[]> {
        const response = await apiClient.get<Provinsi[]>(DASHBOARD_ENDPOINTS.provinsi, {
            params: { regional_id: regionalId },
        });
        return response.data;
    },

    async getKabupatenKota(provinsiId: string): Promise<KabupatenKota[]> {
        const response = await apiClient.get<KabupatenKota[]>(DASHBOARD_ENDPOINTS.kabupatenKota, {
            params: { provinsi_id: provinsiId },
        });
        return response.data;
    },
};
