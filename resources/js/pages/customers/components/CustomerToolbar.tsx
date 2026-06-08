import { Plus, Search, Trash2, X } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { useAuth } from '@/hooks/use-auth';

interface CustomerToolbarProps {
    searchValue: string;
    onSearchChange: (value: string) => void;
    onAddClick: () => void;
    onBulkDeleteClick: () => void;
    onClearFilters: () => void;
    selectedCount: number;
    isSearching: boolean;
    hasActiveFilters: boolean;
}

export function CustomerToolbar({
    searchValue,
    onSearchChange,
    onAddClick,
    onBulkDeleteClick,
    onClearFilters,
    selectedCount,
    isSearching,
    hasActiveFilters,
}: CustomerToolbarProps) {
    const { isSuperAdmin } = useAuth();
    return (
        <>
            <div className="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 className="text-3xl font-bold">Customer</h1>
                    {selectedCount > 0 ? (
                        <p className="mt-1 text-sm text-muted-foreground">
                            {selectedCount} customer dipilih
                        </p>
                    ) : (
                        <p className="mt-1 text-sm text-muted-foreground">
                            Kelola customer inventaris Anda
                        </p>
                    )}
                </div>
                <div className="flex gap-2">
                    {selectedCount > 0 && isSuperAdmin && (
                        <Button
                            variant="destructive"
                            onClick={onBulkDeleteClick}
                        >
                            <Trash2 className="mr-2 h-4 w-4" />
                            Hapus
                        </Button>
                    )}
                    {isSuperAdmin && (
                        <Button onClick={onAddClick}>
                            <Plus className="mr-2 h-4 w-4" />
                            Tambah Customer
                        </Button>
                    )}
                </div>
            </div>

            <div className="mb-4 flex flex-col gap-2 sm:flex-row">
                <div className="relative flex-1">
                    <Search className="absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                    <Input
                        placeholder="Cari berdasarkan nama customer"
                        value={searchValue}
                        onChange={(e) => onSearchChange(e.target.value)}
                        className="pl-9"
                        disabled={isSearching}
                    />
                </div>
                <div className="flex gap-2">
                    {hasActiveFilters && (
                        <Button
                            variant="outline"
                            size="icon"
                            onClick={onClearFilters}
                            disabled={isSearching}
                            title="Hapus filter"
                        >
                            <X className="h-4 w-4" />
                        </Button>
                    )}
                </div>
            </div>
        </>
    );
}
