import { Head, router } from '@inertiajs/react';
import { useState } from 'react';
import { Pagination } from '@/components/pagination';
import { useGenericModals, type ModalWithData } from '@/hooks/useGenericModals';
import { useSearch } from '@/hooks/useSearch';
import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import type { Supplier, Filters, PageProps } from '@/types/models/suppliers';
import { SupplierModals } from './components/SupplierModals';
import { SupplierTable } from './components/SupplierTable';
import { SupplierToolbar } from './components/SupplierToolbar';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Suppliers', href: '/dashboard/suppliers' },
];

export default function Index({
    suppliers,
    filters = {},
}: {
    suppliers: PageProps;
    filters?: Filters;
}) {
    const { searchValue, setSearchValue, clearSearch, isSearching, hasActiveSearch } = useSearch({
        route: '/dashboard/suppliers',
        initialSearch: filters.search || '',
        only: ['suppliers'],
    });

    const { modals, openModal, closeModal } = useGenericModals<Supplier>({
        simple: ['create', 'bulkDelete'],
        withData: ['edit', 'delete']
    });
    const [selectedIds, setSelectedIds] = useState<number[]>([]);

    const handleDelete = () => {
        const deleteModal = modals.delete as ModalWithData<Supplier>;
        if (!deleteModal.data) return;

        router.delete(`/dashboard/suppliers/${deleteModal.data.id}`, {
            preserveScroll: true,
            onSuccess: () => {
                closeModal('delete');
            },
        });
    };

    const handleBulkDelete = () => {
        router.delete('/dashboard/suppliers/bulk-destroy', {
            data: { ids: selectedIds },
            preserveScroll: true,
            onSuccess: () => {
                setSelectedIds([]);
                closeModal('bulkDelete');
            },
        });
    };

    const toggleSelectAll = (checked: boolean) => {
        setSelectedIds(checked ? suppliers.data.map((s: Supplier) => s.id) : []);
    };

    const toggleSelectOne = (id: number, checked: boolean) => {
        setSelectedIds(prev =>
            checked ? [...prev, id] : prev.filter(selectedId => selectedId !== id)
        );
    };

    const clearFilters = () => {
        clearSearch();
    };

    const allSelected = suppliers.data.length > 0 && selectedIds.length === suppliers.data.length;
    const someSelected = selectedIds.length > 0 && selectedIds.length < suppliers.data.length;

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Suppliers" />
            <div className="p-6">
                <SupplierToolbar
                    searchValue={searchValue}
                    onSearchChange={setSearchValue}
                    onAddClick={() => openModal('create')}
                    onBulkDeleteClick={() => openModal('bulkDelete')}
                    onClearFilters={clearFilters}
                    selectedCount={selectedIds.length}
                    isSearching={isSearching}
                    hasActiveFilters={hasActiveSearch}
                />

                <SupplierTable
                    suppliers={suppliers.data}
                    selectedIds={selectedIds}
                    onSelectAll={toggleSelectAll}
                    onSelectOne={toggleSelectOne}
                    onEdit={(supplier: Supplier) => openModal('edit', supplier)}
                    onDelete={(supplier: Supplier) => openModal('delete', supplier)}
                    allSelected={allSelected}
                    someSelected={someSelected}
                />

                {suppliers.last_page > 1 && (
                    <div className="mt-4">
                        <Pagination
                            links={suppliers.links}
                            meta={{
                                current_page: suppliers.current_page,
                                last_page: suppliers.last_page,
                                per_page: suppliers.per_page,
                                total: suppliers.total,
                                from: suppliers.from,
                                to: suppliers.to,
                            }}
                        />
                    </div>
                )}

                <SupplierModals
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
