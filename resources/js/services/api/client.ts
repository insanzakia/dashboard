import axios from 'axios';

/** Bentuk envelope konsisten dari backend (App\Support\ApiResponse). */
interface ApiEnvelope<T> {
    success: boolean;
    message: string;
    data: T;
    errors: Record<string, unknown> | null;
}

/**
 * Instance axios khusus untuk fetch data dashboard (partial reload di luar siklus visit Inertia).
 * Header X-Requested-With sudah diset global di bootstrap.ts agar Laravel mengenali request AJAX.
 */
export const apiClient = axios.create({
    baseURL: '/',
    headers: {
        Accept: 'application/json',
    },
});

/**
 * Interceptor membongkar envelope { success, message, data, errors }:
 * - sukses → kembalikan `data` mentah, sehingga service layer tetap bersih (tidak tahu soal envelope),
 * - success:false → lempar Error dengan pesan ramah agar ditangani state 'error' di hook.
 */
apiClient.interceptors.response.use((response) => {
    const envelope = response.data as ApiEnvelope<unknown>;

    if (envelope && typeof envelope === 'object' && 'success' in envelope) {
        if (!envelope.success) {
            throw new Error(envelope.message || 'Terjadi kesalahan pada server.');
        }
        response.data = envelope.data;
    }

    return response;
});
