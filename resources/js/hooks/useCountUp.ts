import { useEffect, useState } from 'react';

/**
 * Menganimasikan angka dari 0 ke `target` dengan easing ease-out (requestAnimationFrame).
 * Dipakai counter total labkesmas di halaman landing.
 */
export function useCountUp(target: number, durationMs = 1600): number {
    const [value, setValue] = useState(0);

    useEffect(() => {
        if (target <= 0) {
            setValue(0);
            return;
        }

        let rafId = 0;
        let startTime: number | null = null;

        const tick = (now: number) => {
            if (startTime === null) startTime = now;
            const progress = Math.min((now - startTime) / durationMs, 1);
            const eased = 1 - Math.pow(1 - progress, 3); // ease-out cubic
            setValue(Math.round(eased * target));
            if (progress < 1) rafId = requestAnimationFrame(tick);
        };

        rafId = requestAnimationFrame(tick);
        return () => cancelAnimationFrame(rafId);
    }, [target, durationMs]);

    return value;
}
