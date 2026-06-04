import { Plus, Search, X } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import type { InboundToolbarProps } from '@/types/models/inbound';

export function InboundToolbar({
    searchValue,
    onSearchChange,
    onAddClick,
    onClearFilters,
    onWarehouseChange,
    isSearching,
    hasActiveFilters,
    filters,
    warehouses,
}: InboundToolbarProps) {
    return (
        <>
            {/* Header */}
            <div className="mb-6 flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
                <div>
                    <h1 className="text-3xl font-bold tracking-tight">
                        Inbound
                    </h1>
                    <p className="mt-1 text-sm text-muted-foreground">
                        Kelola transaksi inbound
                    </p>
                </div>
                <div className="flex gap-2">
                    <Button onClick={onAddClick} className="gap-2">
                        <Plus className="h-4 w-4" />
                        Tambah Inbound
                    </Button>
                </div>
            </div>

            {/* Filters */}
            <div className="mb-6 grid grid-cols-1 gap-4 md:grid-cols-2">
                <div className="relative">
                    <Search className="absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                    <Input
                        placeholder="Cari kode, supplier, produk..."
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
