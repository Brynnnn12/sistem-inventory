import { Plus, Search, Trash2, X } from 'lucide-react';
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

interface EmployeeToolbarProps {
    searchValue: string;
    roleValue: string;
    onSearchChange: (value: string) => void;
    onRoleChange: (value: string) => void;
    onAddClick: () => void;
    onBulkDeleteClick: () => void;
    onClearFilters: () => void;
    selectedCount: number;
    isSearching: boolean;
    hasActiveFilters: boolean;
}

export function EmployeeToolbar({
    searchValue,
    roleValue,
    onSearchChange,
    onRoleChange,
    onAddClick,
    onBulkDeleteClick,
    onClearFilters,
    selectedCount,
    isSearching,
    hasActiveFilters,
}: EmployeeToolbarProps) {
    const { isSuperAdmin } = useAuth();
    return (
        <>
            {/* Header */}
            <div className="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 className="text-3xl font-bold">Karyawan</h1>
                    {selectedCount > 0 ? (
                        <p className="mt-1 text-sm text-muted-foreground">
                            {selectedCount} karyawan dipilih
                        </p>
                    ) : (
                        <p className="mt-1 text-sm text-muted-foreground">
                            Kelola data karyawan Anda
                        </p>
                    )}
                </div>
                <div className="flex gap-2">
                    {selectedCount > 0 && isSuperAdmin && (
                        <Button
                            variant="destructive"
                            onClick={onBulkDeleteClick}
                            className="gap-2"
                        >
                            <Trash2 className="h-4 w-4" />
                            Hapus
                        </Button>
                    )}
                    {isSuperAdmin && (
                        <Button onClick={onAddClick} className="gap-2">
                            <Plus className="h-4 w-4" />
                            Tambah Karyawan
                        </Button>
                    )}
                </div>
            </div>

            {/* Search & Filters */}
            <div className="mb-4 flex flex-col gap-2 sm:flex-row">
                <div className="relative flex-1">
                    <Search className="absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                    <Input
                        placeholder="Cari berdasarkan nama atau email"
                        value={searchValue}
                        onChange={(e) => onSearchChange(e.target.value)}
                        className="pl-9"
                        disabled={isSearching}
                    />
                </div>
                <Select
                    value={roleValue || 'all'}
                    onValueChange={onRoleChange}
                    disabled={isSearching}
                >
                    <SelectTrigger className="w-full sm:w-45">
                        <SelectValue placeholder="Filter berdasarkan peran" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem value="all">Semua Peran</SelectItem>
                        <SelectItem value="super-admin">Super Admin</SelectItem>
                        <SelectItem value="admin">Admin</SelectItem>
                        <SelectItem value="viewer">Viewer</SelectItem>
                    </SelectContent>
                </Select>
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
