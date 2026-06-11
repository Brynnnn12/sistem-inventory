import {
    ArrowDownLeft,
    ArrowRightLeft,
    ArrowUpRight,
    RotateCcw,
    XCircle,
} from 'lucide-react';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import { formatDateTime, formatQuantity } from '@/lib/utils';
import type { StockHistoryTableProps } from '@/types/models/stock-history';

const referenceLabels: Record<string, { label: string; icon: React.ReactNode }> = {
    inbound: { label: 'Barang Masuk', icon: <ArrowDownLeft className="h-3.5 w-3.5 text-green-600" /> },
    outbound: { label: 'Barang Keluar', icon: <ArrowUpRight className="h-3.5 w-3.5 text-red-600" /> },
    mutation_sent: { label: 'Mutasi Keluar', icon: <ArrowRightLeft className="h-3.5 w-3.5 text-blue-600" /> },
    mutation_received: { label: 'Mutasi Masuk', icon: <ArrowRightLeft className="h-3.5 w-3.5 text-purple-600" /> },
    mutation_rejected: { label: 'Mutasi Ditolak', icon: <XCircle className="h-3.5 w-3.5 text-gray-500" /> },
    adjustment: { label: 'Penyesuaian', icon: <RotateCcw className="h-3.5 w-3.5 text-amber-600" /> },
};

export function StockHistoryTable({
    stockHistories,
    isLoading,
}: StockHistoryTableProps) {
    if (isLoading) {
        return (
            <div className="flex h-32 items-center justify-center">
                <div className="text-sm text-muted-foreground">
                    Memuat data...
                </div>
            </div>
        );
    }

    if (stockHistories.length === 0) {
        return (
            <div className="rounded-lg border border-dashed bg-card">
                <div className="flex flex-col items-center justify-center py-12 text-center">
                    <div className="flex h-20 w-20 items-center justify-center rounded-full bg-muted">
                        <div className="h-10 w-10 rounded-full bg-muted-foreground/20" />
                    </div>
                    <h3 className="mt-4 text-lg font-semibold">
                        Belum Ada History
                    </h3>
                    <p className="mt-2 max-w-sm text-sm text-muted-foreground">
                        History perubahan stok akan muncul di sini
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
                        <TableHead className="font-semibold">Waktu</TableHead>
                        <TableHead className="font-semibold">Produk</TableHead>
                        <TableHead className="font-semibold">Gudang</TableHead>
                        <TableHead className="font-semibold">
                            Perubahan
                        </TableHead>
                        <TableHead className="font-semibold">
                            Dari → Ke
                        </TableHead>
                        <TableHead className="font-semibold">
                            Referensi
                        </TableHead>
                        <TableHead className="font-semibold">Petugas</TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    {stockHistories.map((history) => (
                        <TableRow key={history.id} className="group">
                            <TableCell className="text-sm">
                                {formatDateTime(history.created_at)}
                            </TableCell>
                            <TableCell>
                                <div className="font-medium">
                                    {history.product?.name || '-'}
                                </div>
                            </TableCell>
                            <TableCell>
                                <div className="font-medium">
                                    {history.warehouse?.name || '-'}
                                </div>
                            </TableCell>
                            <TableCell>
                                <div
                                    className={`font-medium ${
                                        history.change_qty > 0
                                            ? 'text-green-600'
                                            : history.change_qty < 0
                                              ? 'text-red-600'
                                              : 'text-gray-600'
                                    }`}
                                >
                                    {history.change_qty > 0 ? '+' : ''}
                                    {formatQuantity(history.change_qty)}
                                </div>
                            </TableCell>
                            <TableCell className="font-mono text-sm">
                                {formatQuantity(history.previous_qty)} →{' '}
                                {formatQuantity(history.new_qty)}
                            </TableCell>
                            <TableCell>
                                <div className="flex items-center gap-2">
                                    {referenceLabels[history.reference_type]
                                        ?.icon || null}
                                    <div className="flex flex-col">
                                        <span className="text-sm font-medium">
                                            {referenceLabels[
                                                history.reference_type
                                            ]?.label ||
                                                history.reference_type}
                                        </span>
                                        {history.reference_code && (
                                            <span className="font-mono text-xs text-muted-foreground">
                                                {history.reference_code}
                                            </span>
                                        )}
                                    </div>
                                </div>
                            </TableCell>
                            <TableCell className="text-sm">
                                {history.creator?.name || '-'}
                            </TableCell>
                        </TableRow>
                    ))}
                </TableBody>
            </Table>
        </div>
    );
}
