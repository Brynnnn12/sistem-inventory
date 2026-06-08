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
import { useAuth } from '@/hooks/use-auth';
import type { OpnameToolbarProps } from '@/types/models/opname';

export function OpnameToolbar({
    searchValue,
    onSearchChange,
    onAddClick,
    onClearFilters,
    onWarehouseChange,
    onDifferenceTypeChange,
    isSearching,
    hasActiveFilters,
    filters,
    warehouses,
}: OpnameToolbarProps) {
    const { isSuperAdmin, isAdmin } = useAuth();
    return (
        <>
            {/* Header */}
            <div className="mb-6 flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
                <div>
                    <h1 className="text-3xl font-bold tracking-tight">
                        Opname
                    </h1>
                    <p className="mt-1 text-sm text-muted-foreground">
                        Kelola opname stok
                    </p>
                </div>
                <div className="flex gap-2">
                    {(isSuperAdmin || isAdmin) && (
                        <Button onClick={onAddClick} className="gap-2">
                            <Plus className="h-4 w-4" />
                            Tambah Opname
                        </Button>
                    )}
                </div>
            </div>

            {/* Filters */}
            <div className="mb-6 grid grid-cols-1 gap-4 md:grid-cols-3">
                <div className="relative">
                    <Search className="absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                    <Input
                        placeholder="Cari kode, produk..."
                        value={searchValue}
                        onChange={(e) => onSearchChange(e.target.value)}
                        className="h-10 pl-9"
                        disabled={isSearching}
                    />
                </div>
                <Select
                    value={filters.warehouse_id || 'all'}
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
                    value={filters.difference_type || 'all'}
                    onValueChange={onDifferenceTypeChange}
                >
                    <SelectTrigger className="h-10">
                        <SelectValue placeholder="Tipe Selisih" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem value="all">Semua</SelectItem>
                        <SelectItem value="lebih">Lebih</SelectItem>
                        <SelectItem value="kurang">Kurang</SelectItem>
                        <SelectItem value="sama">Sama</SelectItem>
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
