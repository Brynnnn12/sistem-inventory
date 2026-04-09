import { Head } from '@inertiajs/react';
import { Pagination } from '@/components/pagination';
import { useFilters } from '@/hooks/useFilters';

import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import type {  Filters, PageProps } from '@/types/models/stock-history';
import { StockHistoryTable } from './components/StockHistoryTable';
import { StockHistoryToolbar } from './components/StockHistoryToolbar';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Stock History', href: '/dashboard/stock-history' },
];

export default function Index({
    stockHistories,
    warehouses,
    products,
    filters = {},
}: {
    stockHistories: PageProps;
    warehouses: Array<{ id: number; name: string }>;
    products: Array<{ id: number; name: string }>;
    filters?: Filters;
}) {
    const { filters: filterState, setFilter, clearFilters, isFiltering, hasActiveFilters } = useFilters({
        route: '/dashboard/stock-history',
        initialFilters: {
            search: filters?.search || '',
            warehouse_id: filters?.warehouse_id || '',
            product_id: filters?.product_id || '',
            reference_type: filters?.reference_type || '',
            start_date: filters?.start_date || '',
            end_date: filters?.end_date || '',
        },
    });

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Stock History" />
            <div className="p-6">
                <div className="mb-6">
                    <h1 className="text-2xl font-bold">Stock History</h1>
                    <p className="text-muted-foreground">
                        Riwayat perubahan stok semua produk di semua gudang
                    </p>
                </div>

                <StockHistoryToolbar
                    searchValue={filterState.search}
                    onSearchChange={(value) => setFilter('search', value)}
                    onClearFilters={clearFilters}
                    isSearching={isFiltering}
                    hasActiveFilters={hasActiveFilters}
                    filters={filterState}
                    warehouses={warehouses}
                    products={products}
                    onWarehouseChange={(value) => setFilter('warehouse_id', value === 'all' ? '' : value)}
                    onProductChange={(value) => setFilter('product_id', value === 'all' ? '' : value)}
                    onReferenceTypeChange={(value) => setFilter('reference_type', value === 'all' ? '' : value)}
                />

                <StockHistoryTable
                    stockHistories={stockHistories.data}
                    isLoading={false}
                />

                {stockHistories.total > 0 && (
                    <div className="mt-6">
                        <Pagination
                            links={stockHistories.links}
                            meta={{
                                current_page: stockHistories.current_page,
                                last_page: stockHistories.last_page,
                                per_page: stockHistories.per_page,
                                total: stockHistories.total,
                                from: stockHistories.from,
                                to: stockHistories.to,
                            }}
                        />
                    </div>
                )}
            </div>
        </AppLayout>
    );
}
