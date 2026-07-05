import { router } from '@inertiajs/react';
import { AlertTriangle, Trash2 } from 'lucide-react';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import type { Opname } from '@/types/models/opname';

interface OpnameDeleteModalProps {
    open: boolean;
    opname: Opname | null;
    onClose: () => void;
}

export function OpnameDeleteModal({
    open,
    opname,
    onClose,
}: OpnameDeleteModalProps) {
    if (!opname) return null;

    const handleDelete = () => {
        router.delete(`/dashboard/opname/${opname.id}`, {
            onSuccess: () => {
                onClose();
            },
        });
    };

    return (
        <Dialog open={open} onOpenChange={onClose}>
            <DialogContent className="sm:max-w-106.25">
                <DialogHeader>
                    <DialogTitle className="flex items-center gap-2">
                        <AlertTriangle className="h-5 w-5 text-red-500" />
                        Hapus Opname
                    </DialogTitle>
                    <DialogDescription>
                        Apakah Anda yakin ingin menghapus opname{' '}
                        <strong>{opname.code}</strong> yang sudah ditolak?
                        Tindakan ini tidak dapat dibatalkan.
                    </DialogDescription>
                </DialogHeader>

                <div className="py-4">
                    <div className="space-y-2 text-sm">
                        <div className="flex justify-between">
                            <span className="text-muted-foreground">Kode:</span>
                            <span className="font-medium">{opname.code}</span>
                        </div>
                        <div className="flex justify-between">
                            <span className="text-muted-foreground">
                                Gudang:
                            </span>
                            <span className="font-medium">
                                {opname.warehouse?.name}
                            </span>
                        </div>
                        <div className="flex justify-between">
                            <span className="text-muted-foreground">
                                Produk:
                            </span>
                            <span className="font-medium">
                                {opname.product?.name}
                            </span>
                        </div>
                    </div>
                </div>

                <DialogFooter>
                    <Button variant="outline" onClick={onClose}>
                        Batal
                    </Button>
                    <Button
                        onClick={handleDelete}
                        variant="destructive"
                        className="gap-2"
                    >
                        <Trash2 className="h-4 w-4" />
                        Hapus Opname
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    );
}
