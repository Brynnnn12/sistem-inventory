import { Edit, Trash2, Users } from 'lucide-react';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import type { Customer } from '@/types/models/customers';

interface CustomerTableProps {
    customers: Customer[];
    selectedIds: number[];
    onSelectAll: (checked: boolean) => void;
    onSelectOne: (id: number, checked: boolean) => void;
    onEdit: (customer: Customer) => void;
    onDelete: (customer: Customer) => void;
    allSelected: boolean;
    someSelected: boolean;
}

export function CustomerTable({
    customers,
    selectedIds,
    onSelectAll,
    onSelectOne,
    onEdit,
    onDelete,
    allSelected,
    someSelected,
}: CustomerTableProps) {
    if (customers.length === 0) {
        return (
            <div className="rounded-lg border border-dashed bg-card">
                <div className="flex flex-col items-center justify-center py-12 text-center">
                    <div className="flex h-20 w-20 items-center justify-center rounded-full bg-muted">
                        <Users className="h-10 w-10 text-muted-foreground" />
                    </div>
                    <h3 className="mt-4 text-lg font-semibold">Belum Ada Customer</h3>
                    <p className="mt-2 text-sm text-muted-foreground max-w-sm">
                        Mulai dengan menambahkan customer pertama ke inventaris Anda
                    </p>
                </div>
            </div>
        );
    }

    return (
        <div className="rounded-lg border bg-card shadow-sm">
            <Table>
                <TableHeader>
                    <TableRow>
                        <TableHead className="w-12.5">
                            <Checkbox
                                checked={allSelected}
                                onCheckedChange={onSelectAll}
                                aria-label="Pilih semua"
                                className={someSelected ? 'data-[state=checked]:bg-muted-foreground' : ''}
                            />
                        </TableHead>
                        <TableHead className="font-semibold">Kode</TableHead>
                        <TableHead className="font-semibold">Nama Customer</TableHead>
                        <TableHead className="font-semibold">Kontak Person</TableHead>
                        <TableHead className="font-semibold">Telepon</TableHead>
                        <TableHead className="font-semibold">Email</TableHead>
                        <TableHead className="font-semibold">Status</TableHead>
                        <TableHead className="text-right font-semibold">Aksi</TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    {customers.map((customer) => (
                        <TableRow key={customer.id} className="group">
                            <TableCell>
                                <Checkbox
                                    checked={selectedIds.includes(customer.id)}
                                    onCheckedChange={(checked) => onSelectOne(customer.id, checked as boolean)}
                                    aria-label={`Pilih ${customer.name}`}
                                />
                            </TableCell>
                            <TableCell>
                                <Badge variant="secondary" className="font-mono text-xs">
                                    {customer.code}
                                </Badge>
                            </TableCell>
                            <TableCell className="font-medium">{customer.name}</TableCell>
                            <TableCell>
                                <div className="flex items-center gap-2">
                                    <Users className="h-4 w-4 text-muted-foreground" />
                                    <span>{customer.contact_person || '-'}</span>
                                </div>
                            </TableCell>
                            <TableCell className="text-muted-foreground">
                                {customer.phone || '-'}
                            </TableCell>
                            <TableCell className="text-muted-foreground">
                                {customer.email || '-'}
                            </TableCell>
                            <TableCell>
                                <span className={`inline-flex items-center px-2 py-1 rounded-full text-xs font-medium ${
                                    customer.is_active
                                        ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300'
                                        : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300'
                                }`}>
                                    {customer.is_active ? 'Aktif' : 'Tidak Aktif'}
                                </span>
                            </TableCell>
                            <TableCell className="text-right">
                                <div className="flex items-center justify-end gap-2">
                                    <Button
                                        variant="ghost"
                                        size="sm"
                                        className="h-8 gap-1.5"
                                        onClick={() => onEdit(customer)}
                                    >
                                        <Edit className="h-3.5 w-3.5" />
                                        <span className="sr-only sm:not-sr-only">Edit</span>
                                    </Button>
                                    <Button
                                        variant="ghost"
                                        size="sm"
                                        className="h-8 gap-1.5 text-destructive hover:text-destructive hover:bg-destructive/10"
                                        onClick={() => onDelete(customer)}
                                    >
                                        <Trash2 className="h-3.5 w-3.5" />
                                        <span className="sr-only sm:not-sr-only">Hapus</span>
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
