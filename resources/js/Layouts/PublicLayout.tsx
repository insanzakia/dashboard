import type { ReactNode } from 'react';
import { AppHeader } from '@/Components/organisms/AppHeader';

interface PublicLayoutProps {
    children: ReactNode;
}

/** Shell halaman publik: header/navigasi bersama + container konten responsif. */
export function PublicLayout({ children }: PublicLayoutProps) {
    return (
        <div className="min-h-screen bg-background">
            <AppHeader />
            <main className="mx-auto max-w-6xl px-4 py-8 sm:px-6 lg:px-8">{children}</main>
        </div>
    );
}
