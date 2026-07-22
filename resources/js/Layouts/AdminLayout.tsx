import type { ReactNode } from 'react';
import { usePage } from '@inertiajs/react';
import { AppHeader } from '@/Components/organisms/AppHeader';
import type { SharedPageProps } from '@/types/inertia';

/**
 * Shell halaman admin — tampilan SAMA dengan halaman publik (AppHeader bersama),
 * ditambah flash message & judul halaman. Navigasi admin ada di menu "Admin" pada header.
 */
export function AdminLayout({ title, children }: { title?: string; children: ReactNode }) {
    const { flash } = usePage<SharedPageProps>().props;

    return (
        <div className="min-h-screen bg-background">
            <AppHeader />

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
