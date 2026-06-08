import { Search, X, Plus } from 'lucide-react';
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
import type { MutationToolbarProps } from '@/types/models/mutation';

export function MutationToolbar({
    searchValue,
    onSearchChange,
    onAddClick,
    onClearFilters,
    onStatusChange,
    onTypeChange,
    isSearching,
    hasActiveFilters,
    filters,
}: MutationToolbarProps) {
    const { isSuperAdmin, isAdmin } = useAuth();
    return (
        <>
            {/* Header */}
            <div className="mb-6 flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
                <div>
                    <h1 className="text-3xl font-bold tracking-tight">
                        Mutasi
                    </h1>
                    <p className="mt-1 text-sm text-muted-foreground">
                        Kelola perpindahan stok antar gudang
                    </p>
                </div>
                {(isSuperAdmin || isAdmin) && (
                    <Button onClick={onAddClick} className="gap-2">
                        <Plus className="h-4 w-4" />
                        Buat Mutasi
                    </Button>
                )}
            </div>

            {/* Filters */}
            <div className="mb-6 grid grid-cols-1 gap-4 md:grid-cols-4">
                <div className="relative">
                    <Search className="absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                    <Input
                        placeholder="Cari kode atau produk..."
                        value={searchValue}
                        onChange={(e) => onSearchChange(e.target.value)}
                        className="h-10 pl-9"
                        disabled={isSearching}
                    />
                </div>
                <Select
                    value={filters.status || 'all'}
                    onValueChange={onStatusChange}
                >
                    <SelectTrigger className="h-10">
                        <SelectValue placeholder="Semua Status" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem value="all">Semua Status</SelectItem>
                        <SelectItem value="sent">Dikirim</SelectItem>
                        <SelectItem value="completed">Selesai</SelectItem>
                        <SelectItem value="rejected">Ditolak</SelectItem>
                    </SelectContent>
                </Select>
                <Select
                    value={filters.type || 'all'}
                    onValueChange={onTypeChange}
                >
                    <SelectTrigger className="h-10">
                        <SelectValue placeholder="Semua Tipe" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem value="all">Semua Tipe</SelectItem>
                        <SelectItem value="outgoing">Keluar</SelectItem>
                        <SelectItem value="incoming">Masuk</SelectItem>
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
