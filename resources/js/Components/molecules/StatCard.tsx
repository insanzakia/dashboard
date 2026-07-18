import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card';
import { cn } from '@/lib/utils';

export interface StatCardProps {
    label: string;
    value: string;
    deltaLabel?: string;
    deltaTone?: 'positive' | 'negative' | 'neutral';
    /** Warna aksen (mis. 'var(--chart-1)') untuk strip kiri kartu. */
    accent?: string;
}

const DELTA_TONE_CLASS: Record<NonNullable<StatCardProps['deltaTone']>, string> = {
    positive: 'text-emerald-600 dark:text-emerald-400',
    negative: 'text-rose-600 dark:text-rose-400',
    neutral: 'text-muted-foreground',
};

export function StatCard({ label, value, deltaLabel, deltaTone = 'neutral', accent }: StatCardProps) {
    return (
        <Card className="relative overflow-hidden">
            {accent && (
                <span
                    className="absolute inset-y-0 left-0 w-1"
                    style={{ backgroundColor: accent }}
                    aria-hidden="true"
                />
            )}
            <CardHeader className="pb-2">
                <CardTitle>{label}</CardTitle>
            </CardHeader>
            <CardContent>
                <div className="text-2xl font-semibold text-foreground">{value}</div>
                {deltaLabel && <p className={cn('mt-1 text-xs', DELTA_TONE_CLASS[deltaTone])}>{deltaLabel}</p>}
            </CardContent>
        </Card>
    );
}
