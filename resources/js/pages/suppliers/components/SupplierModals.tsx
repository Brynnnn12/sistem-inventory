import {
    AlertDialog,
    AlertDialogAction,
    AlertDialogCancel,
    AlertDialogContent,
    AlertDialogDescription,
    AlertDialogFooter,
    AlertDialogHeader,
    AlertDialogTitle,
} from '@/components/ui/alert-dialog';
import type { ModalState, ModalWithData } from '@/hooks/useGenericModals';
import type { Supplier } from '@/types/models/suppliers';

import { SupplierFormModal } from './SupplierFormModal';

const DeleteConfirmDialog = ({
    open,
    title,
    description,
    onConfirm,
    onClose,
}: {
    open: boolean;
    title: string;
    description: string;
    onConfirm: () => void;
    onClose: () => void;
}) => (
    <AlertDialog open={open} onOpenChange={onClose}>
        <AlertDialogContent>
            <AlertDialogHeader>
                <AlertDialogTitle>{title}</AlertDialogTitle>
                <AlertDialogDescription>{description}</AlertDialogDescription>
            </AlertDialogHeader>
            <AlertDialogFooter>
                <AlertDialogCancel>Batal</AlertDialogCancel>
                <AlertDialogAction
                    onClick={onConfirm}
                    className="bg-destructive text-destructive-foreground hover:bg-destructive/90"
                >
                    Hapus
                </AlertDialogAction>
            </AlertDialogFooter>
        </AlertDialogContent>
    </AlertDialog>
);

interface SupplierModalsProps {
    modals: ModalState<Supplier>;
    onCloseModal: (type: string) => void;
    onConfirmDelete: () => void;
    onConfirmBulkDelete: () => void;
    selectedCount: number;
}

export function SupplierModals({
    modals,
    onCloseModal,
    onConfirmDelete,
    onConfirmBulkDelete,
    selectedCount,
}: SupplierModalsProps) {
    const editModal = modals.edit as ModalWithData<Supplier>;
    const deleteModal = modals.delete as ModalWithData<Supplier>;

    return (
        <>
            <SupplierFormModal
                open={modals.create as boolean}
                onClose={() => onCloseModal('create')}
            />

            <SupplierFormModal
                open={editModal.isOpen}
                supplier={editModal.data}
                onClose={() => onCloseModal('edit')}
            />

            <DeleteConfirmDialog
                open={modals.bulkDelete as boolean}
                title="Hapus Beberapa Supplier"
                description={`Apakah Anda yakin ingin menghapus ${selectedCount} supplier? Tindakan ini tidak dapat dibatalkan.`}
                onConfirm={onConfirmBulkDelete}
                onClose={() => onCloseModal('bulkDelete')}
            />

            <DeleteConfirmDialog
                open={deleteModal.isOpen}
                title="Hapus Supplier"
                description={`Apakah Anda yakin ingin menghapus supplier "${deleteModal.data?.name}" (Kode: ${deleteModal.data?.code})? Tindakan ini tidak dapat dibatalkan.`}
                onConfirm={onConfirmDelete}
                onClose={() => onCloseModal('delete')}
            />
        </>
    );
}
