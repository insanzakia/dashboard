import type { ReactNode } from 'react';
import { Link, usePage } from '@inertiajs/react';
import { APP_NAME } from '@/lib/constants';
import { cn } from '@/lib/utils';

interface PublicLayoutProps {
    children: ReactNode;
}

const NAV_ITEMS = [
    { href: '/standar-labkesmas', label: 'Standar Peralatan' },
    { href: '/list-labkesmas', label: 'List Labkesmas' },
    { href: '/pemeriksaan', label: 'Pemeriksaan' },
] as const;

/** Shell halaman publik: header brand + nav antar-dashboard + container konten responsif. */
export function PublicLayout({ children }: PublicLayoutProps) {
    const { url } = usePage();
    const isActive = (href: string) => url === href || url.startsWith(`${href}/`);

    return (
        <div className="min-h-screen bg-background">
            <header className="border-b bg-card">
                <div className="mx-auto flex max-w-6xl flex-col gap-3 px-4 py-3 sm:flex-row sm:items-center sm:gap-8 sm:px-6 lg:px-8">
                    <Link href="/" className="flex items-center gap-2.5">
                        <span
                            className="flex h-6 w-6 items-center justify-center rounded-md bg-primary text-xs font-bold text-primary-foreground"
                            aria-hidden="true"
                        >
                            L
                        </span>
                        <span className="text-sm font-semibold tracking-tight text-foreground">{APP_NAME}</span>
                    </Link>
                    <nav className="flex items-center gap-1 overflow-x-auto">
                        {NAV_ITEMS.map((item) => (
                            <Link
                                key={item.href}
                                href={item.href}
                                className={cn(
                                    'whitespace-nowrap rounded-md px-3 py-1.5 text-sm font-medium transition-colors',
                                    isActive(item.href)
                                        ? 'bg-primary/10 text-primary'
                                        : 'text-muted-foreground hover:bg-muted hover:text-foreground',
                                )}
                            >
                                {item.label}
                            </Link>
                        ))}
                    </nav>
                </div>
            </header>
            <main className="mx-auto max-w-6xl px-4 py-8 sm:px-6 lg:px-8">{children}</main>
        </div>
    );
}
