import { Head } from '@inertiajs/react';
import { useState } from 'react';
import { Pagination } from '@/components/pagination';
import { useFilters } from '@/hooks/useFilters';
import { useGenericModals, type ModalWithData } from '@/hooks/useGenericModals';
import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import type { Stock, StockFilters, PageProps } from '@/types/models/stocks';
import { StocksModals } from './components/StocksModals';
import { StocksTable } from './components/StocksTable';
import { StocksToolbar } from './components/StocksToolbar';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Stok', href: '/dashboard/stocks' },
];

export default function Index({
    stocks,
    warehouses,
    products,
    filters = {},
}: {
    stocks: PageProps;
    warehouses: Array<{ id: number; name: string }>;
    products: Array<{ id: number; name: string }>;
    filters?: StockFilters;
}) {
    const [isLoading] = useState(false);

    const { filters: filterState, setFilter, clearFilters, isFiltering, hasActiveFilters } = useFilters({
        route: '/dashboard/stocks',
        initialFilters: {
            search: filters.search || '',
            warehouse_id: filters.warehouse_id || '',
            product_id: filters.product_id || '',
        },
    });

    const { modals, openModal, closeModal } = useGenericModals<Stock>({
        simple: [],
        withData: ['show']
    });

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Stok" />
            <div className="p-6">
                <StocksToolbar
                    searchValue={filterState.search}
                    onSearchChange={(value) => setFilter('search', value)}
                    onClearFilters={clearFilters}
                    isSearching={isFiltering}
                    hasActiveFilters={hasActiveFilters}
                    filters={filterState}
                    warehouses={warehouses}
                    products={products}
                    onWarehouseChange={(value) => setFilter('warehouse_id', value)}
                    onProductChange={(value) => setFilter('product_id', value)}
                />

                <StocksTable
                    stocks={stocks.data}
                    isLoading={isLoading}
                    onShowStock={(stock) => openModal('show', stock)}
                />

                {stocks.total > 0 && (
                    <div className="mt-6">
                        <Pagination
                            links={stocks.links}
                            meta={{
                                current_page: stocks.current_page,
                                last_page: stocks.last_page,
                                per_page: stocks.per_page,
                                total: stocks.total,
                                from: stocks.from,
                                to: stocks.to,
                            }}
                        />
                    </div>
                )}

                <StocksModals
                    showModal={(modals.show as ModalWithData<Stock>).isOpen}
                    selectedStock={(modals.show as ModalWithData<Stock>).data}
                    onCloseShowModal={() => closeModal('show')}
                />
            </div>
        </AppLayout>
    );
}
