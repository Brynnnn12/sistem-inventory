import { Search, X } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import type { StocksToolbarProps } from '@/types/models/stocks';

export function StocksToolbar({
    searchValue,
    onSearchChange,
    onClearFilters,
    isSearching,
    hasActiveFilters,
    filters,
    warehouses,
    products,
    onWarehouseChange,
    onProductChange,
}: StocksToolbarProps) {
    return (
        <>
            {/* Header */}
            <div className="mb-6 flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
                <div>
                    <h1 className="text-3xl font-bold tracking-tight">Stok</h1>
                    <p className="mt-1 text-sm text-muted-foreground">
                        Lihat stok produk per gudang
                    </p>
                </div>
            </div>

            {/* Filters */}
            <div className="mb-6 grid grid-cols-1 gap-4 md:grid-cols-4">
                <div className="relative">
                    <Search className="absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                    <Input
                        placeholder="Cari produk..."
                        value={searchValue}
                        onChange={(e) => onSearchChange(e.target.value)}
                        className="h-10 pl-9"
                        disabled={isSearching}
                    />
                </div>
                <Select
                    value={filters?.warehouse_id?.toString() || 'all'}
                    onValueChange={onWarehouseChange}
                >
                    <SelectTrigger className="h-10">
                        <SelectValue placeholder="Pilih Warehouse" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem value="all">Semua Warehouse</SelectItem>
                        {warehouses.map((warehouse) => (
                            <SelectItem
                                key={warehouse.id}
                                value={warehouse.id.toString()}
                            >
                                {warehouse.name}
                            </SelectItem>
                        ))}
                    </SelectContent>
                </Select>
                <Select
                    value={
                        filters.product_id
                            ? filters.product_id.toString()
                            : 'all'
                    }
                    onValueChange={(value) => {
                        onProductChange(value === 'all' ? '' : value);
                    }}
                >
                    <SelectTrigger className="h-10">
                        <SelectValue placeholder="Pilih Produk" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem value="all">Semua Produk</SelectItem>
                        {products.map((product) => (
                            <SelectItem
                                key={product.id}
                                value={product.id.toString()}
                            >
                                {product.name}
                            </SelectItem>
                        ))}
                    </SelectContent>
                </Select>
                <div></div> {/* Empty for alignment */}
            </div>

            {hasActiveFilters && (
                <div className="mb-6">
                    <Button
                        variant="outline"
                        onClick={onClearFilters}
                        disabled={isSearching}
                        className="gap-2"
                    >
                        <X className="h-4 w-4" />
                        Hapus Filter
                    </Button>
                </div>
            )}
        </>
    );
}
