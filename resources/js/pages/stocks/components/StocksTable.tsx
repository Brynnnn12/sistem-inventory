import { Eye, Package } from 'lucide-react';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import { formatQuantity } from '@/lib/utils';
import type { StocksTableProps } from '@/types/models/stocks';

export function StocksTable({
    stocks,
    isLoading,
    onShowStock,
}: StocksTableProps) {
    if (isLoading) {
        return (
            <div className="flex h-32 items-center justify-center">
                <div className="text-sm text-muted-foreground">
                    Memuat data...
                </div>
            </div>
        );
    }

    if (stocks.length === 0) {
        return (
            <div className="rounded-lg border border-dashed bg-card">
                <div className="flex flex-col items-center justify-center py-12 text-center">
                    <div className="flex h-20 w-20 items-center justify-center rounded-full bg-muted">
                        <Package className="h-10 w-10 text-muted-foreground" />
                    </div>
                    <h3 className="mt-4 text-lg font-semibold">
                        Belum Ada Stok
                    </h3>
                    <p className="mt-2 max-w-sm text-sm text-muted-foreground">
                        Data stok akan muncul setelah ada transaksi inbound atau
                        mutasi masuk
                    </p>
                </div>
            </div>
        );
    }

    return (
        <div className="rounded-lg border bg-card shadow-sm">
            <Table>
                <TableHeader>
                    <TableRow className="hover:bg-transparent">
                        <TableHead>Produk</TableHead>
                        <TableHead>Gudang</TableHead>
                        <TableHead className="text-right">
                            Qty Tersedia
                        </TableHead>
                        <TableHead className="text-right">Qty Total</TableHead>
                        <TableHead className="text-right">
                            Qty Minimum
                        </TableHead>
                        <TableHead>Status</TableHead>
                        <TableHead className="w-25">Aksi</TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    {stocks.map((stock) => (
                        <TableRow key={stock.id}>
                            <TableCell className="font-medium">
                                {stock.product?.name || '-'}
                            </TableCell>
                            <TableCell>
                                {stock.warehouse?.name || '-'}
                            </TableCell>
                            <TableCell className="text-right font-mono">
                                {formatQuantity(stock.available_qty || 0)}
                            </TableCell>
                            <TableCell className="text-right font-mono">
                                {formatQuantity(stock.quantity || 0)}
                            </TableCell>
                            <TableCell className="text-right font-mono">
                                {formatQuantity(stock.min_stock || 0)}
                            </TableCell>
                            <TableCell>
                                <Badge
                                    variant={
                                        (stock.available_qty || 0) <=
                                        (stock.min_stock || 0)
                                            ? 'destructive'
                                            : (stock.available_qty || 0) <=
                                                (stock.min_stock || 0) * 1.5
                                              ? 'secondary'
                                              : 'default'
                                    }
                                >
                                    {(stock.available_qty || 0) <=
                                    (stock.min_stock || 0)
                                        ? 'Kritis'
                                        : (stock.available_qty || 0) <=
                                            (stock.min_stock || 0) * 1.5
                                          ? 'Rendah'
                                          : 'Normal'}
                                </Badge>
                            </TableCell>
                            <TableCell>
                                <Button
                                    variant="ghost"
                                    size="sm"
                                    onClick={() => onShowStock(stock)}
                                    className="h-8 w-8 p-0"
                                >
                                    <Eye className="h-4 w-4" />
                                </Button>
                            </TableCell>
                        </TableRow>
                    ))}
                </TableBody>
            </Table>
        </div>
    );
}
