import type { LucideIcon } from 'lucide-react';
import { Inbox } from 'lucide-react';

export interface EmptyStateProps {
    title: string;
    description: string;
    icon?: LucideIcon;
}

export function EmptyState({ title, description, icon: Icon = Inbox }: EmptyStateProps) {
    return (
        <div className="flex flex-col items-center justify-center gap-2 rounded-lg border border-dashed py-12 text-center">
            <Icon className="h-8 w-8 text-muted-foreground" aria-hidden="true" />
            <p className="text-sm font-medium text-foreground">{title}</p>
            <p className="max-w-sm text-xs text-muted-foreground">{description}</p>
        </div>
    );
}
