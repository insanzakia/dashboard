import type { ReactNode } from 'react';
import { EmptyState } from '@/Components/molecules/EmptyState';

export interface Column<T> {
    header: string;
    cell: (row: T) => ReactNode;
    className?: string;
}

interface DataTableProps<T> {
    columns: Column<T>[];
    rows: T[];
    emptyTitle: string;
    emptyDescription: string;
}

/** Tabel generik & reusable: kolom dideklarasikan lewat props, menangani Empty State. */
export function DataTable<T extends { id: string }>({
    columns,
    rows,
    emptyTitle,
    emptyDescription,
}: DataTableProps<T>) {
    if (rows.length === 0) {
        return <EmptyState title={emptyTitle} description={emptyDescription} />;
    }

    return (
        <div className="overflow-x-auto rounded-lg border">
            <table className="w-full text-sm">
                <thead>
                    <tr className="border-b bg-muted/50 text-left text-xs font-medium text-muted-foreground">
                        {columns.map((col) => (
                            <th key={col.header} className={`px-4 py-2.5 ${col.className ?? ''}`}>
                                {col.header}
                            </th>
                        ))}
                    </tr>
                </thead>
                <tbody>
                    {rows.map((row) => (
                        <tr key={row.id} className="border-b last:border-0">
                            {columns.map((col) => (
                                <td key={col.header} className={`px-4 py-2.5 text-foreground ${col.className ?? ''}`}>
                                    {col.cell(row)}
                                </td>
                            ))}
                        </tr>
                    ))}
                </tbody>
            </table>
        </div>
    );
}
