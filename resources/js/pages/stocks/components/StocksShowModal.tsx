import { Badge } from '@/components/ui/badge';
import { Dialog, DialogContent, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { formatQuantity, formatDateTime } from '@/lib/utils';
import type { StocksShowModalProps } from '@/types/models/stocks';

export function StocksShowModal({
    stock,
    isOpen,
    onClose,
}: StocksShowModalProps) {
    if (!stock) return null;

    return (
        <Dialog open={isOpen} onOpenChange={onClose}>
            <DialogContent className="max-w-2xl">
                <DialogHeader>
                    <DialogTitle>Detail Stok</DialogTitle>
                </DialogHeader>

                <div className="space-y-6">
                    {/* Stock Information */}
                    <div className="grid grid-cols-2 gap-4">
                        <div>
                            <label className="text-sm font-medium text-muted-foreground">Produk</label>
                            <p className="text-sm font-medium">{stock.product?.name || '-'}</p>
                        </div>
                        <div>
                            <label className="text-sm font-medium text-muted-foreground">Gudang</label>
                            <p className="text-sm font-medium">{stock.warehouse?.name || '-'}</p>
                        </div>
                        <div>
                            <label className="text-sm font-medium text-muted-foreground">Qty Total</label>
                            <p className="text-sm font-medium font-mono">{formatQuantity(stock.quantity || 0)}</p>
                        </div>
                        <div>
                            <label className="text-sm font-medium text-muted-foreground">Qty Tersedia</label>
                            <p className="text-sm font-medium font-mono">{formatQuantity(stock.available_qty || 0)}</p>
                        </div>
                        <div>
                            <label className="text-sm font-medium text-muted-foreground">Qty Minimum</label>
                            <p className="text-sm font-medium font-mono">{formatQuantity(stock.min_stock || 0)}</p>
                        </div>
                        <div>
                            <label className="text-sm font-medium text-muted-foreground">Status</label>
                            <Badge
                                variant={
                                    (stock.available_qty || 0) <= (stock.min_stock || 0)
                                        ? 'destructive'
                                        : (stock.available_qty || 0) <= ((stock.min_stock || 0) * 1.5)
                                        ? 'secondary'
                                        : 'default'
                                }
                            >
                                {(stock.available_qty || 0) <= (stock.min_stock || 0)
                                    ? 'Kritis'
                                    : (stock.available_qty || 0) <= ((stock.min_stock || 0) * 1.5)
                                    ? 'Rendah'
                                    : 'Normal'
                                }
                            </Badge>
                        </div>
                        <div>
                            <label className="text-sm font-medium text-muted-foreground">Terakhir Update</label>
                            <p className="text-sm font-medium">
                                {stock.updated_at ? formatDateTime(stock.updated_at) : '-'}
                            </p>
                        </div>
                    </div>
                </div>
            </DialogContent>
        </Dialog>
    );
}
