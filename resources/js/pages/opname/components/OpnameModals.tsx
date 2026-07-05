import type { ModalState, ModalWithData } from '@/hooks/useGenericModals';
import type { Opname } from '@/types/models/opname';
import { OpnameApproveModal } from './OpnameApproveModal';
import { OpnameDeleteModal } from './OpnameDeleteModal';
import { OpnameFormModal } from './OpnameFormModal';
import { OpnameRejectModal } from './OpnameRejectModal';
import { OpnameShowModal } from './OpnameShowModal';

interface OpnameModalsProps {
    modals: ModalState<Opname>;
    onCloseModal: (type: string) => void;
    warehouses: Array<{ id: number; name: string }>;
    products: Array<{ id: number; name: string }>;
    stocks: Array<{
        id: number;
        warehouse_id: number;
        product_id: number;
        quantity: number;
        product: { id: number; name: string };
        warehouse: { id: number; name: string };
    }>;
    canSelectWarehouse: boolean;
}

export function OpnameModals({
    modals,
    onCloseModal,
    warehouses,
    products,
    stocks,
    canSelectWarehouse,
}: OpnameModalsProps) {
    return (
        <>
            <OpnameFormModal
                open={
                    typeof modals.create === 'boolean'
                        ? modals.create
                        : modals.create.isOpen
                }
                onClose={() => onCloseModal('create')}
                warehouses={warehouses}
                products={products}
                stocks={stocks}
                canSelectWarehouse={canSelectWarehouse}
            />

            <OpnameShowModal
                open={(modals.show as ModalWithData<Opname>).isOpen}
                opname={(modals.show as ModalWithData<Opname>).data}
                onClose={() => onCloseModal('show')}
            />

            <OpnameApproveModal
                open={(modals.approve as ModalWithData<Opname>).isOpen}
                opname={(modals.approve as ModalWithData<Opname>).data}
                onClose={() => onCloseModal('approve')}
            />

            <OpnameRejectModal
                open={(modals.reject as ModalWithData<Opname>).isOpen}
                opname={(modals.reject as ModalWithData<Opname>).data}
                onClose={() => onCloseModal('reject')}
            />

            <OpnameDeleteModal
                open={(modals.delete as ModalWithData<Opname>).isOpen}
                opname={(modals.delete as ModalWithData<Opname>).data}
                onClose={() => onCloseModal('delete')}
            />
        </>
    );
}
