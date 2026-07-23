import { useState } from 'react';
import { Link, router, usePage } from '@inertiajs/react';
import { ChevronDown, KeyRound, LogIn, LogOut, User } from 'lucide-react';
import { Button } from '@/Components/ui/button';
import { APP_NAME } from '@/lib/constants';
import { cn } from '@/lib/utils';
import type { SharedPageProps } from '@/types/inertia';

const NAV_ITEMS = [
    { href: '/standar-labkesmas', label: 'Standar Peralatan' },
    { href: '/list-labkesmas', label: 'List Labkesmas' },
    { href: '/pemeriksaan', label: 'Pemeriksaan' },
] as const;

/** Menu input data — untuk semua admin (dibatasi cakupan di server). */
const ADMIN_INPUT_GROUP = {
    label: 'Input Data',
    items: [
        { href: '/admin/labkesmas', label: 'Labkesmas' },
        { href: '/admin/data-pemeriksaan', label: 'Data Pemeriksaan' },
        { href: '/admin/inventaris-alat', label: 'Input Alat' },
    ],
} as const;

/** Menu khusus SUPER ADMIN — kelola akun & seluruh master data. */
const SUPERADMIN_GROUPS = [
    {
        label: 'Akun',
        items: [{ href: '/admin/users', label: 'Kelola Akun' }],
    },
    {
        label: 'Wilayah',
        items: [
            { href: '/admin/wilayah/negara', label: 'Negara' },
            { href: '/admin/wilayah/regional', label: 'Regional' },
            { href: '/admin/wilayah/provinsi', label: 'Provinsi' },
            { href: '/admin/wilayah/kabupaten-kota', label: 'Kab/Kota' },
        ],
    },
    {
        label: 'Master Data',
        items: [
            { href: '/admin/jenis-pemeriksaan', label: 'Jenis Pemeriksaan' },
            { href: '/admin/alat', label: 'Alat & Standar' },
        ],
    },
] as const;

const ROLE_LABEL: Record<string, string> = {
    super_admin: 'Super Admin',
    admin: 'Admin',
};

/** Header + navigasi bersama untuk halaman publik & admin (tampilan seragam). */
export function AppHeader() {
    const { url, props } = usePage<SharedPageProps>();
    const user = props.auth?.user ?? null;
    const isSuperAdmin = user?.role === 'super_admin';
    const [adminOpen, setAdminOpen] = useState(false);
    const [accountOpen, setAccountOpen] = useState(false);

    const isActive = (href: string) => url === href || url.startsWith(`${href}/`);
    const isAdminActive = url.startsWith('/admin');
    const logout = () => router.post('/logout');

    // Admin biasa hanya melihat menu input data; super admin melihat semuanya.
    const adminGroups = isSuperAdmin ? [ADMIN_INPUT_GROUP, ...SUPERADMIN_GROUPS] : [ADMIN_INPUT_GROUP];

    return (
        <header className="relative z-20 border-b bg-card">
            <div className="mx-auto flex max-w-6xl flex-col gap-3 px-4 py-3 sm:flex-row sm:items-center sm:gap-6 sm:px-6 lg:px-8">
                <Link href="/" className="flex items-center gap-2.5">
                    <span
                        className="flex h-6 w-6 items-center justify-center rounded-md bg-primary text-xs font-bold text-primary-foreground"
                        aria-hidden="true"
                    >
                        L
                    </span>
                    <span className="text-sm font-semibold tracking-tight text-foreground">{APP_NAME}</span>
                </Link>

                <nav className="flex flex-wrap items-center gap-1">
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

                    {user && (
                        <div className="relative">
                            <button
                                type="button"
                                onClick={() => setAdminOpen((open) => !open)}
                                className={cn(
                                    'flex items-center gap-1 whitespace-nowrap rounded-md px-3 py-1.5 text-sm font-bold transition-colors',
                                    isAdminActive || adminOpen
                                        ? 'bg-primary/10 text-primary'
                                        : 'text-foreground hover:bg-muted',
                                )}
                                aria-haspopup="menu"
                                aria-expanded={adminOpen}
                            >
                                Admin
                                <ChevronDown
                                    className={cn('h-3.5 w-3.5 transition-transform', adminOpen && 'rotate-180')}
                                />
                            </button>

                            {adminOpen && (
                                <>
                                    <div
                                        className="fixed inset-0 z-10"
                                        aria-hidden="true"
                                        onClick={() => setAdminOpen(false)}
                                    />
                                    <div
                                        role="menu"
                                        className="absolute left-0 z-20 mt-1 w-56 rounded-lg border bg-card p-1.5 shadow-lg"
                                    >
                                        <Link
                                            href="/admin"
                                            onClick={() => setAdminOpen(false)}
                                            className={cn(
                                                'block rounded-md px-2 py-1.5 text-sm font-medium',
                                                url === '/admin'
                                                    ? 'bg-primary/10 text-primary'
                                                    : 'text-foreground hover:bg-muted',
                                            )}
                                        >
                                            Dashboard Admin
                                        </Link>
                                        {adminGroups.map((group) => (
                                            <div key={group.label} className="mt-1">
                                                <p className="px-2 pb-1 pt-1.5 text-[11px] font-medium uppercase tracking-wide text-muted-foreground">
                                                    {group.label}
                                                </p>
                                                {group.items.map((item) => (
                                                    <Link
                                                        key={item.href}
                                                        href={item.href}
                                                        onClick={() => setAdminOpen(false)}
                                                        className={cn(
                                                            'block rounded-md px-2 py-1.5 text-sm',
                                                            isActive(item.href)
                                                                ? 'bg-primary/10 text-primary'
                                                                : 'text-foreground hover:bg-muted',
                                                        )}
                                                    >
                                                        {item.label}
                                                    </Link>
                                                ))}
                                            </div>
                                        ))}
                                    </div>
                                </>
                            )}
                        </div>
                    )}
                </nav>

                <div className="flex items-center gap-3 sm:ml-auto">
                    {user ? (
                        <div className="relative">
                            <button
                                type="button"
                                onClick={() => setAccountOpen((open) => !open)}
                                className={cn(
                                    'flex items-center gap-2 rounded-md border px-3 py-1.5 text-sm font-medium transition-colors',
                                    accountOpen ? 'bg-muted' : 'hover:bg-muted',
                                )}
                                aria-haspopup="menu"
                                aria-expanded={accountOpen}
                            >
                                <User className="h-4 w-4" />
                                <span className="max-w-[120px] truncate">{user.username}</span>
                                <ChevronDown
                                    className={cn('h-3.5 w-3.5 transition-transform', accountOpen && 'rotate-180')}
                                />
                            </button>

                            {accountOpen && (
                                <>
                                    <div
                                        className="fixed inset-0 z-10"
                                        aria-hidden="true"
                                        onClick={() => setAccountOpen(false)}
                                    />
                                    <div
                                        role="menu"
                                        className="absolute right-0 z-20 mt-1 w-60 rounded-lg border bg-card p-1.5 shadow-lg"
                                    >
                                        <div className="border-b px-2 pb-2 pt-1">
                                            <p className="text-[11px] uppercase tracking-wide text-muted-foreground">
                                                Masuk sebagai
                                            </p>
                                            <p className="truncate text-sm font-semibold text-foreground">
                                                {user.username}
                                            </p>
                                            <span className="mt-1 inline-block rounded-full bg-primary/10 px-2 py-0.5 text-[11px] font-medium text-primary">
                                                {ROLE_LABEL[user.role] ?? user.role}
                                            </span>
                                        </div>
                                        <Link
                                            href="/akun"
                                            onClick={() => setAccountOpen(false)}
                                            className="mt-1 flex items-center gap-2 rounded-md px-2 py-1.5 text-sm text-foreground hover:bg-muted"
                                        >
                                            <KeyRound className="h-4 w-4" />
                                            Ganti Password
                                        </Link>
                                        <button
                                            type="button"
                                            onClick={() => {
                                                setAccountOpen(false);
                                                logout();
                                            }}
                                            className="flex w-full items-center gap-2 rounded-md px-2 py-1.5 text-left text-sm text-destructive hover:bg-destructive/10"
                                        >
                                            <LogOut className="h-4 w-4" />
                                            Keluar
                                        </button>
                                    </div>
                                </>
                            )}
                        </div>
                    ) : (
                        <Button asChild size="sm" variant="outline">
                            <Link href="/login">
                                <LogIn />
                                Login Admin
                            </Link>
                        </Button>
                    )}
                </div>
            </div>
        </header>
    );
}
