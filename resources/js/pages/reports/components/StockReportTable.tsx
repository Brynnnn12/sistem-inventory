import { Badge } from '@/components/ui/badge';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import {  formatQuantity } from '@/lib/utils';
import type { StockItem } from '@/types/models/reports';

interface StockReportTableProps {
    data: StockItem[] | Record<string, StockItem[]>;
    selectedWarehouse: string;
    isLoading: boolean;
}

function getStatusBadge(status: string) {
    switch (status) {
        case 'out_of_stock':
            return <Badge variant="destructive">Habis</Badge>;
        case 'low_stock':
            return <Badge variant="secondary">Stok Rendah</Badge>;
        default:
            return <Badge variant="default">Normal</Badge>;
    }
}

function renderStockTable(data: StockItem[]) {
    return (
        <Table>
            <TableHeader>
                <TableRow>
                    <TableHead>Gudang</TableHead>
                    <TableHead>Kode Produk</TableHead>
                    <TableHead>Nama Produk</TableHead>
                    <TableHead>Satuan</TableHead>
                    <TableHead className="text-right">Tersedia</TableHead>
                    <TableHead>Status</TableHead>
                </TableRow>
            </TableHeader>
            <TableBody>
                {data.map((item, index) => (
                    <TableRow key={index}>
                        <TableCell>{item.warehouse_name}</TableCell>
                        <TableCell>{item.product_code}</TableCell>
                        <TableCell>{item.product_name}</TableCell>
                        <TableCell>{item.unit}</TableCell>
                        <TableCell className="text-right">
                            {formatQuantity(item.available_qty)}
                        </TableCell>
                        <TableCell>{getStatusBadge(item.status)}</TableCell>
                    </TableRow>
                ))}
            </TableBody>
        </Table>
    );
}

export function StockReportTable({
    data,
    selectedWarehouse,
    isLoading,
}: StockReportTableProps) {
    if (isLoading) {
        return (
            <Card>
                <CardHeader>
                    <CardTitle>Detail Stok</CardTitle>
                    <CardDescription>
                        Data stok{' '}
                        {selectedWarehouse !== 'all'
                            ? 'per gudang'
                            : 'semua gudang'}
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <div className="flex h-32 items-center justify-center">
                        <div className="text-sm text-muted-foreground">
                            Memuat data...
                        </div>
                    </div>
                </CardContent>
            </Card>
        );
    }

    const isEmpty = Array.isArray(data)
        ? data.length === 0
        : Object.keys(data).length === 0;

    if (isEmpty) {
        return (
            <Card>
                <CardHeader>
                    <CardTitle>Detail Stok</CardTitle>
                    <CardDescription>
                        Data stok{' '}
                        {selectedWarehouse !== 'all'
                            ? 'per gudang'
                            : 'semua gudang'}
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <div className="flex flex-col items-center justify-center py-12 text-center">
                        <div className="flex h-20 w-20 items-center justify-center rounded-full bg-muted">
                            <div className="h-10 w-10 rounded-full bg-muted-foreground/20" />
                        </div>
                        <h3 className="mt-4 text-lg font-semibold">
                            Tidak Ada Data Stok
                        </h3>
                        <p className="mt-2 max-w-sm text-sm text-muted-foreground">
                            Data stok akan muncul di sini
                        </p>
                    </div>
                </CardContent>
            </Card>
        );
    }

    return (
        <Card>
            <CardHeader>
                <CardTitle>Detail Stok</CardTitle>
                <CardDescription>
                    Data stok{' '}
                    {selectedWarehouse !== 'all'
                        ? 'per gudang'
                        : 'semua gudang'}
                </CardDescription>
            </CardHeader>
            <CardContent>
                {Array.isArray(data)
                    ? renderStockTable(data)
                    : Object.entries(data).map(([warehouse, items]) => (
                          <div key={warehouse} className="mb-6">
                              <h3 className="mb-4 text-lg font-semibold">
                                  {warehouse}
                              </h3>
                              {renderStockTable(items as StockItem[])}
                          </div>
                      ))}
            </CardContent>
        </Card>
    );
}
