import { router } from '@inertiajs/react';
import { XCircle } from 'lucide-react';
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

interface OpnameRejectModalProps {
    open: boolean;
    opname: Opname | null;
    onClose: () => void;
}

export function OpnameRejectModal({
    open,
    opname,
    onClose,
}: OpnameRejectModalProps) {
    if (!opname) return null;

    const handleReject = () => {
        router.post(
            `/dashboard/opname/${opname.id}/reject`,
            {},
            {
                onSuccess: () => {
                    onClose();
                },
            },
        );
    };

    return (
        <Dialog open={open} onOpenChange={onClose}>
            <DialogContent className="sm:max-w-106.25">
                <DialogHeader>
                    <DialogTitle className="flex items-center gap-2">
                        <XCircle className="h-5 w-5 text-red-500" />
                        Tolak Opname
                    </DialogTitle>
                    <DialogDescription>
                        Apakah Anda yakin ingin menolak opname{' '}
                        <strong>{opname.code}</strong>? Status opname akan
                        berubah menjadi ditolak dan tidak akan memengaruhi stok.
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
                        onClick={handleReject}
                        variant="destructive"
                        className="gap-2"
                    >
                        <XCircle className="h-4 w-4" />
                        Tolak Opname
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    );
}
