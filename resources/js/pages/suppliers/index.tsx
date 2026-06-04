import { Head, router } from '@inertiajs/react';
import { Pagination } from '@/components/pagination';
import { useGenericModals, type ModalWithData } from '@/hooks/useGenericModals';
import { useSearch } from '@/hooks/useSearch';
import { useSelection } from '@/hooks/useSelection';
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
    const {
        searchValue,
        setSearchValue,
        clearSearch,
        isSearching,
        hasActiveSearch,
    } = useSearch({
        route: '/dashboard/suppliers',
        initialSearch: filters.search || '',
    });

    const { modals, openModal, closeModal } = useGenericModals<Supplier>({
        simple: ['create', 'bulkDelete'],
        withData: ['edit', 'delete'],
    });
    const {
        selectedIds,
        toggleSelectAll,
        toggleSelectOne,
        clearSelection,
        allSelected,
        someSelected,
        selectedCount,
    } = useSelection(suppliers.data);

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
                clearSelection();
                closeModal('bulkDelete');
            },
        });
    };

    const clearFilters = () => {
        clearSearch();
    };

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
                    selectedCount={selectedCount}
                    isSearching={isSearching}
                    hasActiveFilters={hasActiveSearch}
                />

                <SupplierTable
                    suppliers={suppliers.data}
                    selectedIds={selectedIds}
                    onSelectAll={toggleSelectAll}
                    onSelectOne={toggleSelectOne}
                    onEdit={(supplier: Supplier) => openModal('edit', supplier)}
                    onDelete={(supplier: Supplier) =>
                        openModal('delete', supplier)
                    }
                    allSelected={allSelected}
                    someSelected={someSelected}
                />

                {suppliers.total > 0 && (
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
                    selectedCount={selectedCount}
                />
            </div>
        </AppLayout>
    );
}
