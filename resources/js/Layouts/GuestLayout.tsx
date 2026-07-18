import type { ReactNode } from 'react';
import { APP_NAME } from '@/lib/constants';

/** Shell terpusat untuk halaman tamu (login) — kartu di tengah layar. */
export function GuestLayout({ children }: { children: ReactNode }) {
    return (
        <div className="flex min-h-screen items-center justify-center bg-muted/40 px-4">
            <div className="w-full max-w-sm">
                <div className="mb-6 text-center">
                    <span className="text-base font-semibold tracking-tight text-foreground">{APP_NAME}</span>
                    <p className="mt-1 text-sm text-muted-foreground">Panel Administrasi</p>
                </div>
                {children}
            </div>
        </div>
    );
}
