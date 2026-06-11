import { Head, router } from '@inertiajs/react';
import { Pagination } from '@/components/pagination';
import { useGenericModals, type ModalWithData } from '@/hooks/useGenericModals';
import { useSearch } from '@/hooks/useSearch';
import { useSelection } from '@/hooks/useSelection';
import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import type {
    WarehouseUser,
    Filters,
    PageProps,
    Warehouse,
    User,
} from '@/types/models/warehouse-users';
import { WarehouseUserModals } from './components/WarehouseUserModals';
import { WarehouseUserTable } from './components/WarehouseUserTable';
import { WarehouseUserToolbar } from './components/WarehouseUserToolbar';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Warehouse Users', href: '/dashboard/warehouse-users' },
];

export default function Index({
    warehouseUsers,
    warehouses,
    users,
    filters = {},
}: {
    warehouseUsers: PageProps;
    warehouses: Warehouse[];
    users: User[];
    filters?: Filters;
}) {
    const {
        searchValue,
        setSearchValue,
        clearSearch,
        isSearching,
        hasActiveSearch,
    } = useSearch({
        route: '/dashboard/warehouse-users',
        initialSearch: filters.search || '',
    });

    const { modals, openModal, closeModal } = useGenericModals<WarehouseUser>({
        simple: ['create', 'bulkDelete', 'swap'],
        withData: ['delete'],
    });
    const {
        selectedIds,
        toggleSelectAll,
        toggleSelectOne,
        clearSelection,
        allSelected,
        someSelected,
        selectedCount,
    } = useSelection(warehouseUsers.data);

    const handleDelete = () => {
        const deleteModal = modals.delete as ModalWithData<WarehouseUser>;
        if (!deleteModal.data) return;

        router.delete(`/dashboard/warehouse-users/${deleteModal.data.id}`, {
            preserveScroll: true,
            onSuccess: () => closeModal('delete'),
        });
    };

    const handleBulkDelete = () => {
        router.delete('/dashboard/warehouse-users/bulk-destroy', {
            data: { ids: selectedIds },
            preserveScroll: true,
            onSuccess: () => {
                clearSelection();
                closeModal('bulkDelete');
            },
        });
    };

    const handleSwap = () => {
        if (
            selectedIds.length !== 2 ||
            selectedIds[1] === undefined ||
            selectedIds[0] === undefined
        )
            return;

        router.post(
            '/dashboard/warehouse-users/swap',
            {
                warehouse_user1_id: selectedIds[1].toString(),
                warehouse_user2_id: selectedIds[0].toString(),
            },
            {
                preserveScroll: true,
                onSuccess: () => {
                    clearSelection();
                    closeModal('swap');
                },
            },
        );
    };

    const selectedItems = warehouseUsers.data.filter((wu) =>
        selectedIds.includes(wu.id),
    );
    const canSwap =
        selectedItems.length === 2 &&
        selectedItems[0].warehouse_id !== selectedItems[1].warehouse_id;

    const clearFilters = () => {
        clearSearch();
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Warehouse Users" />
            <div className="p-6">
                <WarehouseUserToolbar
                    searchValue={searchValue}
                    onSearchChange={setSearchValue}
                    onAddClick={() => openModal('create')}
                    onBulkDeleteClick={() => openModal('bulkDelete')}
                    onSwapClick={() => openModal('swap')}
                    onClearFilters={clearFilters}
                    selectedCount={selectedCount}
                    canSwap={canSwap}
                    isSearching={isSearching}
                    hasActiveFilters={hasActiveSearch}
                />

                <WarehouseUserTable
                    warehouseUsers={warehouseUsers.data}
                    selectedIds={selectedIds}
                    onSelectAll={toggleSelectAll}
                    onSelectOne={toggleSelectOne}
                    onDelete={(warehouseUser) =>
                        openModal('delete', warehouseUser)
                    }
                    allSelected={allSelected}
                    someSelected={someSelected}
                />

                {/* Pagination */}
                {warehouseUsers.total > 0 && (
                    <div className="mt-4">
                        <Pagination
                            links={warehouseUsers.links}
                            meta={{
                                current_page: warehouseUsers.current_page,
                                last_page: warehouseUsers.last_page,
                                per_page: warehouseUsers.per_page,
                                total: warehouseUsers.total,
                                from: warehouseUsers.from,
                                to: warehouseUsers.to,
                            }}
                        />
                    </div>
                )}

                <WarehouseUserModals
                    modals={modals}
                    warehouses={warehouses}
                    users={users}
                    onCloseModal={closeModal}
                    onConfirmDelete={handleDelete}
                    onConfirmBulkDelete={handleBulkDelete}
                    onConfirmSwap={handleSwap}
                    selectedCount={selectedCount}
                />
            </div>
        </AppLayout>
    );
}
