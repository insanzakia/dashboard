import { Head } from '@inertiajs/react';
import { PublicLayout } from '@/Layouts/PublicLayout';
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card';
import { Button } from '@/Components/ui/button';
import { WilayahCascadeFilter } from '@/Components/organisms/WilayahCascadeFilter';
import { TierSelector } from '@/Components/molecules/TierSelector';
import { SummaryCardsGrid } from '@/Components/organisms/SummaryCardsGrid';
import { TrendChart } from '@/Components/organisms/TrendChart';
import { DashboardFilterProvider, useDashboardFilter } from '@/context/DashboardFilterContext';
import { useDashboardSummary } from '@/hooks/useDashboardSummary';
import { useTrendData } from '@/hooks/useTrendData';

function DashboardContent() {
    const { filter, setTier, resetFilter } = useDashboardFilter();
    const summary = useDashboardSummary(filter);
    const trend = useTrendData(filter);

    return (
        <div className="flex flex-col gap-8">
            <section className="flex flex-col gap-4">
                <div className="flex items-center justify-between">
                    <h1 className="text-lg font-semibold text-foreground">Ringkasan Pemeriksaan</h1>
                    <Button variant="ghost" size="sm" onClick={resetFilter}>
                        Reset Filter
                    </Button>
                </div>
                <Card>
                    <CardContent className="pt-6">
                        <div className="grid grid-cols-1 gap-4 sm:grid-cols-4">
                            <div className="sm:col-span-3">
                                <WilayahCascadeFilter />
                            </div>
                            <TierSelector value={filter.tier} onChange={setTier} />
                        </div>
                    </CardContent>
                </Card>
            </section>

            <section>
                <SummaryCardsGrid state={summary} />
            </section>

            <section>
                <Card>
                    <CardHeader>
                        <CardTitle className="text-base font-semibold text-foreground">
                            Tren Pemeriksaan Bulanan
                        </CardTitle>
                    </CardHeader>
                    <CardContent>
                        <TrendChart state={trend} />
                    </CardContent>
                </Card>
            </section>
        </div>
    );
}

export default function DashboardIndex() {
    return (
        <PublicLayout>
            <Head title="Dashboard" />
            <DashboardFilterProvider>
                <DashboardContent />
            </DashboardFilterProvider>
        </PublicLayout>
    );
}
