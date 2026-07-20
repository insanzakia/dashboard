import type { ReactNode } from 'react';
import { Link, router, usePage } from '@inertiajs/react';
import { Button } from '@/Components/ui/button';
import { cn } from '@/lib/utils';
import { APP_NAME } from '@/lib/constants';
import type { SharedPageProps } from '@/types/inertia';

interface NavItem {
    label: string;
    href: string;
}

/** Menu navigasi admin — sumber tunggal, hindari hardcode tersebar. */
const NAV_ITEMS: NavItem[] = [
    { label: 'Dashboard', href: '/admin' },
    { label: 'Negara', href: '/admin/wilayah/negara' },
    { label: 'Regional', href: '/admin/wilayah/regional' },
    { label: 'Provinsi', href: '/admin/wilayah/provinsi' },
    { label: 'Kab/Kota', href: '/admin/wilayah/kabupaten-kota' },
    { label: 'Jenis Tes', href: '/admin/jenis-pemeriksaan' },
    { label: 'Labkesmas', href: '/admin/labkesmas' },
    { label: 'Input Bulanan', href: '/admin/data-pemeriksaan' },
    { label: 'Alat & Standar', href: '/admin/alat' },
    { label: 'Input Alat', href: '/admin/inventaris-alat' },
];

export function AdminLayout({ title, children }: { title?: string; children: ReactNode }) {
    const page = usePage<SharedPageProps>();
    const { auth, flash } = page.props;
    const currentPath = page.url;

    const logout = () => router.post('/logout');

    return (
        <div className="min-h-screen bg-muted/30">
            <header className="border-b bg-background">
                <div className="mx-auto flex max-w-6xl items-center justify-between px-4 py-3 sm:px-6 lg:px-8">
                    <div className="flex items-baseline gap-2">
                        <span className="text-sm font-semibold tracking-tight text-foreground">{APP_NAME}</span>
                        <span className="text-xs text-muted-foreground">Admin</span>
                    </div>
                    <div className="flex items-center gap-3">
                        {auth.user && (
                            <span className="text-sm text-muted-foreground">
                                {auth.user.username} · {auth.user.role}
                            </span>
                        )}
                        <Button variant="outline" size="sm" onClick={logout}>
                            Keluar
                        </Button>
                    </div>
                </div>
                <nav className="mx-auto flex max-w-6xl gap-1 overflow-x-auto px-4 sm:px-6 lg:px-8">
                    {NAV_ITEMS.map((item) => {
                        const active = currentPath === item.href;
                        return (
                            <Link
                                key={item.href}
                                href={item.href}
                                className={cn(
                                    'border-b-2 px-3 py-2 text-sm transition-colors',
                                    active
                                        ? 'border-foreground font-medium text-foreground'
                                        : 'border-transparent text-muted-foreground hover:text-foreground',
                                )}
                            >
                                {item.label}
                            </Link>
                        );
                    })}
                </nav>
            </header>

            <main className="mx-auto max-w-6xl px-4 py-8 sm:px-6 lg:px-8">
                {flash.success && (
                    <div className="mb-4 rounded-md border border-emerald-500/30 bg-emerald-500/10 px-4 py-2.5 text-sm text-emerald-700 dark:text-emerald-400">
                        {flash.success}
                    </div>
                )}
                {flash.error && (
                    <div className="mb-4 rounded-md border border-destructive/30 bg-destructive/10 px-4 py-2.5 text-sm text-destructive">
                        {flash.error}
                    </div>
                )}
                {title && <h1 className="mb-6 text-lg font-semibold text-foreground">{title}</h1>}
                {children}
            </main>
        </div>
    );
}
