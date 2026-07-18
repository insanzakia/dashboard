import { useEffect, useState } from 'react';
import type { RequestState } from '@/types/dashboard';

/**
 * Hook generik: jalankan `fetcher` setiap `deps` berubah, dan petakan hasilnya
 * ke salah satu dari 4 state (loading/success/empty/error) yang wajib ditangani UI.
 * Dipakai oleh useDashboardSummary & useTrendData agar logic fetch tidak diduplikasi.
 */
export function useAsyncData<T>(
    fetcher: () => Promise<T>,
    deps: unknown[],
    isEmpty: (data: T) => boolean,
): RequestState<T> {
    const [state, setState] = useState<RequestState<T>>({
        status: 'loading',
        data: null,
        errorMessage: null,
    });

    useEffect(() => {
        let cancelled = false;
        setState({ status: 'loading', data: null, errorMessage: null });

        fetcher()
            .then((data) => {
                if (cancelled) return;
                setState({
                    status: isEmpty(data) ? 'empty' : 'success',
                    data,
                    errorMessage: null,
                });
            })
            .catch((error: unknown) => {
                if (cancelled) return;
                const message = error instanceof Error ? error.message : 'Gagal memuat data.';
                setState({ status: 'error', data: null, errorMessage: message });
            });

        return () => {
            cancelled = true;
        };
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, deps);

    return state;
}
