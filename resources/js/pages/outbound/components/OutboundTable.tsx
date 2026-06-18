import { Eye, FolderOpen, Printer } from 'lucide-react';
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
import { formatQuantity, formatDate } from '@/lib/utils';
import { download as proofDocDownload } from '@/routes/proof-documents';
import type { OutboundTableProps } from '@/types/models/outbound';

export function OutboundTable({ outbounds, onShow }: OutboundTableProps) {
    if (outbounds.length === 0) {
        return (
            <div className="rounded-lg border border-dashed bg-card">
                <div className="flex flex-col items-center justify-center py-12 text-center">
                    <div className="flex h-20 w-20 items-center justify-center rounded-full bg-muted">
                        <FolderOpen className="h-10 w-10 text-muted-foreground" />
                    </div>
                    <h3 className="mt-4 text-lg font-semibold">
                        Belum Ada Outbound
                    </h3>
                    <p className="mt-2 max-w-sm text-sm text-muted-foreground">
                        Mulai dengan menambahkan transaksi outbound pertama
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
                        <TableHead className="font-semibold">Kode</TableHead>
                        <TableHead className="font-semibold">
                            Customer
                        </TableHead>
                        <TableHead className="font-semibold">Produk</TableHead>
                        <TableHead className="font-semibold">
                            Warehouse
                        </TableHead>
                        <TableHead className="font-semibold">Qty</TableHead>
                        <TableHead className="font-semibold">Tanggal</TableHead>
                        <TableHead className="text-right font-semibold">
                            Aksi
                        </TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    {outbounds.map((outbound) => (
                        <TableRow key={outbound.id} className="group">
                            <TableCell>
                                <Badge
                                    variant="secondary"
                                    className="font-mono text-xs"
                                >
                                    {outbound.code}
                                </Badge>
                            </TableCell>
                            <TableCell>
                                <div className="font-medium">
                                    {outbound.customer.name}
                                </div>
                            </TableCell>
                            <TableCell>
                                <div className="font-medium">
                                    {outbound.product.name}
                                </div>
                            </TableCell>
                            <TableCell>
                                <div className="font-medium">
                                    {outbound.warehouse.name}
                                </div>
                            </TableCell>
                            <TableCell>
                                <div className="font-medium">
                                    {formatQuantity(outbound.quantity)}
                                </div>
                            </TableCell>
                            <TableCell>
                                <div className="font-medium">
                                    {formatDate(outbound.sale_date)}
                                </div>
                            </TableCell>
                            <TableCell>
                                <div className="flex items-center justify-end gap-1">
                                    <Button
                                        variant="ghost"
                                        size="sm"
                                        onClick={() =>
                                            window.open(
                                                proofDocDownload({
                                                    type: 'outbound',
                                                    id: outbound.id,
                                                }).url,
                                                '_blank',
                                            )
                                        }
                                        className="h-8 w-8 p-0"
                                    >
                                        <Printer className="h-3.5 w-3.5" />
                                    </Button>
                                    <Button
                                        variant="ghost"
                                        size="sm"
                                        onClick={() => onShow(outbound)}
                                        className="h-8 gap-1.5"
                                    >
                                        <Eye className="h-3.5 w-3.5" />
                                        <span className="sr-only sm:not-sr-only">
                                            Lihat
                                        </span>
                                    </Button>
                                </div>
                            </TableCell>
                        </TableRow>
                    ))}
                </TableBody>
            </Table>
        </div>
    );
}
