import type { ComponentType } from 'react';
import { Head, Link } from '@inertiajs/react';
import { motion, type Variants } from 'framer-motion';
import { Activity, ClipboardCheck, ArrowRight } from 'lucide-react';
import { KemenkesEmblem } from '@/Components/atoms/KemenkesEmblem';
import { useCountUp } from '@/hooks/useCountUp';
import { formatNumber } from '@/lib/utils';

interface LandingProps {
    totalLabkesmas: number;
}

interface EntryCardProps {
    href: string;
    icon: ComponentType<{ className?: string }>;
    title: string;
    description: string;
}

/** Entrance fade — anak-anak muncul bertahap hanya dengan perubahan opacity. */
const containerVariants: Variants = {
    hidden: { opacity: 0 },
    show: { opacity: 1, transition: { staggerChildren: 0.12, delayChildren: 0.15 } },
};

const itemVariants: Variants = {
    hidden: { opacity: 0 },
    show: { opacity: 1, transition: { duration: 0.6, ease: 'easeOut' } },
};

/** Kartu pintu masuk ke salah satu dashboard. */
function EntryCard({ href, icon: Icon, title, description }: EntryCardProps) {
    return (
        <Link
            href={href}
            className="group flex flex-col gap-3 rounded-xl border bg-card p-6 text-left shadow-xs transition-all hover:border-primary hover:shadow-md"
        >
            <span className="flex h-11 w-11 items-center justify-center rounded-lg bg-accent text-accent-foreground">
                <Icon className="h-5 w-5" />
            </span>
            <div>
                <div className="flex items-center gap-1.5 text-base font-semibold text-foreground">
                    {title}
                    <ArrowRight className="h-4 w-4 -translate-x-1 opacity-0 transition-all group-hover:translate-x-0 group-hover:opacity-100" />
                </div>
                <p className="mt-1 text-sm text-muted-foreground">{description}</p>
            </div>
        </Link>
    );
}

export default function Landing({ totalLabkesmas }: LandingProps) {
    const count = useCountUp(totalLabkesmas);

    return (
        <>
            <Head title="InPULS KEMENKES" />
            <div className="relative flex min-h-screen flex-col items-center justify-center overflow-hidden bg-background px-4 py-12">
                {/* Logo Kemenkes blur — fade-in sebagai latar */}
                <motion.div
                    className="pointer-events-none absolute inset-0 flex items-center justify-center"
                    initial={{ opacity: 0 }}
                    animate={{ opacity: 1 }}
                    transition={{ duration: 1.1, ease: 'easeOut' }}
                >
                    <KemenkesEmblem className="h-[540px] w-[540px] text-primary/10 blur-2xl" />
                </motion.div>

                {/* Konten utama — fade-in bertahap */}
                <motion.div
                    className="relative z-10 flex w-full max-w-3xl flex-col items-center text-center"
                    variants={containerVariants}
                    initial="hidden"
                    animate="show"
                >
                    <motion.p
                        variants={itemVariants}
                        className="mb-3 text-xs font-medium uppercase tracking-[0.3em] text-primary"
                    >
                        Kementerian Kesehatan RI
                    </motion.p>
                    <motion.h1
                        variants={itemVariants}
                        className="font-display text-6xl font-extrabold tracking-tight text-foreground sm:text-7xl"
                    >
                        InPULS KEMENKES
                    </motion.h1>
                    <motion.p variants={itemVariants} className="mt-4 text-sm text-muted-foreground">
                        Pemantauan Data Laboratorium Kesehatan Masyarakat
                    </motion.p>

                    {/* Counter total labkesmas */}
                    <motion.div variants={itemVariants} className="mt-12 flex flex-col items-center">
                        <span className="font-display text-6xl font-bold tabular-nums text-primary sm:text-7xl">
                            {formatNumber(count)}
                        </span>
                        <span className="mt-2 text-sm text-muted-foreground">Total Labkesmas Terdaftar</span>
                    </motion.div>

                    {/* Dua pintu masuk dashboard */}
                    <motion.div
                        variants={itemVariants}
                        className="mt-14 grid w-full grid-cols-1 gap-4 sm:grid-cols-2"
                    >
                        <EntryCard
                            href="/standar-labkesmas"
                            icon={ClipboardCheck}
                            title="Standar Labkesmas"
                            description="Dashboard standar peralatan laboratorium."
                        />
                        <EntryCard
                            href="/pemeriksaan"
                            icon={Activity}
                            title="Pemeriksaan"
                            description="Dashboard tren & data pemeriksaan bulanan."
                        />
                    </motion.div>
                </motion.div>
            </div>
        </>
    );
}
