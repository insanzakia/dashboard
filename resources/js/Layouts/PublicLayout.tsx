import type { ReactNode } from 'react';
import { Link } from '@inertiajs/react';
import { APP_NAME } from '@/lib/constants';

interface PublicLayoutProps {
    children: ReactNode;
}

/** Shell halaman publik: header brand (link ke landing) + container konten responsif. */
export function PublicLayout({ children }: PublicLayoutProps) {
    return (
        <div className="min-h-screen bg-background">
            <header className="border-b bg-card">
                <div className="mx-auto flex max-w-6xl items-center px-4 py-4 sm:px-6 lg:px-8">
                    <Link href="/" className="flex items-center gap-2.5">
                        <span
                            className="flex h-6 w-6 items-center justify-center rounded-md bg-primary text-xs font-bold text-primary-foreground"
                            aria-hidden="true"
                        >
                            L
                        </span>
                        <span className="text-sm font-semibold tracking-tight text-foreground">{APP_NAME}</span>
                    </Link>
                </div>
            </header>
            <main className="mx-auto max-w-6xl px-4 py-8 sm:px-6 lg:px-8">{children}</main>
        </div>
    );
}
