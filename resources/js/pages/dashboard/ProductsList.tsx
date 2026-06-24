import { Package } from 'lucide-react';
import { Badge } from '@/components/ui/badge';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { formatCurrency } from '@/utils/format';

interface Product {
    id: number;
    code: string;
    name: string;
    unit: string;
    price: number;
    is_active: boolean;
    category?: {
        name: string;
    };
}

interface ProductsListProps {
    products: Product[];
}

export function ProductsList({ products }: ProductsListProps) {
    return (
        <Card>
            <CardHeader>
                <CardTitle className="flex items-center gap-2">
                    <Package className="h-5 w-5" />
                    Produk Terbaru
                </CardTitle>
                <CardDescription>
                    {products.length} produk terdaftar dalam sistem
                </CardDescription>
            </CardHeader>
            <CardContent>
                <div className="space-y-4">
                    {products.length === 0 ? (
                        <div className="py-8 text-center text-muted-foreground">
                            Belum ada produk terdaftar
                        </div>
                    ) : (
                        products.slice(0, 5).map((product) => (
                            <div
                                key={product.id}
                                className="flex flex-col gap-2 rounded-lg border p-3 sm:flex-row sm:items-center sm:justify-between"
                            >
                                <div className="flex items-center gap-3">
                                    <div className="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-muted">
                                        <Package className="h-5 w-5 text-muted-foreground" />
                                    </div>
                                    <div className="min-w-0">
                                        <div className="font-medium">
                                            {product.name}
                                        </div>
                                        <div className="flex flex-wrap items-center gap-1 text-sm text-muted-foreground">
                                            <Badge
                                                variant="secondary"
                                                className="font-mono text-xs"
                                            >
                                                {product.code}
                                            </Badge>
                                            <span className="hidden sm:inline">
                                                •
                                            </span>
                                            <span className="truncate">
                                                {product.category?.name}
                                            </span>
                                            <span className="hidden sm:inline">
                                                •
                                            </span>
                                            <span>{product.unit}</span>
                                        </div>
                                    </div>
                                </div>
                                <div className="flex items-center justify-between gap-2 sm:flex-col sm:items-end sm:justify-center">
                                    <div className="font-medium">
                                        {formatCurrency(product.price ?? 0)}
                                    </div>
                                    <Badge
                                        variant={
                                            product.is_active
                                                ? 'default'
                                                : 'secondary'
                                        }
                                        className="text-xs"
                                    >
                                        {product.is_active
                                            ? 'Aktif'
                                            : 'Tidak Aktif'}
                                    </Badge>
                                </div>
                            </div>
                        ))
                    )}
                </div>
            </CardContent>
        </Card>
    );
}
