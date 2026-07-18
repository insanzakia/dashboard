import { AlertTriangle } from 'lucide-react';
import { Button } from '@/Components/ui/button';

export interface ErrorStateProps {
    message: string;
    retryLabel?: string;
    onRetry?: () => void;
}

export function ErrorState({ message, retryLabel = 'Coba Lagi', onRetry }: ErrorStateProps) {
    return (
        <div className="flex flex-col items-center justify-center gap-2 rounded-lg border border-destructive/30 bg-destructive/5 py-12 text-center">
            <AlertTriangle className="h-8 w-8 text-destructive" aria-hidden="true" />
            <p className="max-w-sm text-sm text-destructive">{message}</p>
            {onRetry && (
                <Button variant="outline" size="sm" onClick={onRetry}>
                    {retryLabel}
                </Button>
            )}
        </div>
    );
}
