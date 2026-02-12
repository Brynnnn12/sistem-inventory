import { Head, router } from '@inertiajs/react';
import { useState } from 'react';
import { Pagination } from '@/components/pagination';
import { useGenericModals, type ModalWithData } from '@/hooks/useGenericModals';
import { useSearch } from '@/hooks/useSearch';
import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import type { Customer, Filters, PageProps } from '@/types/models/customers';
import { CustomerModals } from './components/CustomerModals';
import { CustomerTable } from './components/CustomerTable';
import { CustomerToolbar } from './components/CustomerToolbar';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Customers', href: '/dashboard/customers' },
];

export default function Index({
    customers,
    filters = {},
}: {
    customers: PageProps;
    filters?: Filters;
}) {
    const { searchValue, setSearchValue, clearSearch, isSearching, hasActiveSearch } = useSearch({
        route: '/dashboard/customers',
        initialSearch: filters.search || '',
        only: ['customers'],
    });

    const { modals, openModal, closeModal } = useGenericModals<Customer>({
        simple: ['create', 'bulkDelete'],
        withData: ['edit', 'delete']
    });
    const [selectedIds, setSelectedIds] = useState<number[]>([]);

    const handleDelete = () => {
        const deleteModal = modals.delete as ModalWithData<Customer>;
        if (!deleteModal.data) return;

        router.delete(`/dashboard/customers/${deleteModal.data.id}`, {
            preserveScroll: true,
            onSuccess: () => {
                closeModal('delete');
            },
        });
    };

    const handleBulkDelete = () => {
        router.delete('/dashboard/customers/bulk-destroy', {
            data: { ids: selectedIds },
            preserveScroll: true,
            onSuccess: () => {
                setSelectedIds([]);
                closeModal('bulkDelete');
            },
        });
    };

    const toggleSelectAll = (checked: boolean) => {
        setSelectedIds(checked ? customers.data.map((c: Customer) => c.id) : []);
    };

    const toggleSelectOne = (id: number, checked: boolean) => {
        setSelectedIds(prev =>
            checked ? [...prev, id] : prev.filter(selectedId => selectedId !== id)
        );
    };

    const clearFilters = () => {
        clearSearch();
    };

    const allSelected = customers.data.length > 0 && selectedIds.length === customers.data.length;
    const someSelected = selectedIds.length > 0 && selectedIds.length < customers.data.length;

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Customers" />
            <div className="p-6">
                <CustomerToolbar
                    searchValue={searchValue}
                    onSearchChange={setSearchValue}
                    onAddClick={() => openModal('create')}
                    onBulkDeleteClick={() => openModal('bulkDelete')}
                    onClearFilters={clearFilters}
                    selectedCount={selectedIds.length}
                    isSearching={isSearching}
                    hasActiveFilters={hasActiveSearch}
                />

                <CustomerTable
                    customers={customers.data}
                    selectedIds={selectedIds}
                    onSelectAll={toggleSelectAll}
                    onSelectOne={toggleSelectOne}
                    onEdit={(customer: Customer) => openModal('edit', customer)}
                    onDelete={(customer: Customer) => openModal('delete', customer)}
                    allSelected={allSelected}
                    someSelected={someSelected}
                />

                {customers.last_page > 1 && (
                    <div className="mt-4">
                        <Pagination
                            links={customers.links}
                            meta={{
                                current_page: customers.current_page,
                                last_page: customers.last_page,
                                per_page: customers.per_page,
                                total: customers.total,
                                from: customers.from,
                                to: customers.to,
                            }}
                        />
                    </div>
                )}

                <CustomerModals
                    modals={modals}
                    onCloseModal={closeModal}
                    onConfirmDelete={handleDelete}
                    onConfirmBulkDelete={handleBulkDelete}
                    selectedCount={selectedIds.length}
                />
            </div>
        </AppLayout>
    );
}
