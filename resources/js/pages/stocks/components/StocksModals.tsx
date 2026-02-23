import type { StocksModalsProps } from '@/types/models/stocks';
import { StocksShowModal } from './StocksShowModal';

export function StocksModals({
    showModal,
    selectedStock,
    onCloseShowModal,
}: StocksModalsProps) {
    return (
        <>
            <StocksShowModal
                stock={selectedStock}
                isOpen={showModal}
                onClose={onCloseShowModal}
            />
        </>
    );
}