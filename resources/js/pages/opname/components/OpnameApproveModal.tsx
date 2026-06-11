import { router } from '@inertiajs/react';
import { AlertTriangle, CheckCircle } from 'lucide-react';
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

interface OpnameApproveModalProps {
    open: boolean;
    opname: Opname | null;
    onClose: () => void;
}

export function OpnameApproveModal({
    open,
    opname,
    onClose,
}: OpnameApproveModalProps) {
    if (!opname) return null;

    const handleApprove = () => {
        router.post(
            `/dashboard/opname/${opname.id}/approve`,
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
                        <AlertTriangle className="h-5 w-5 text-amber-500" />
                        Setujui Opname
                    </DialogTitle>
                    <DialogDescription>
                        Apakah Anda yakin ingin menyetujui opname{' '}
                        <strong>{opname.code}</strong>? Tindakan ini akan
                        menyesuaikan stok berdasarkan hasil opname dan tidak
                        dapat dibatalkan.
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
                    <Button onClick={handleApprove} className="gap-2">
                        <CheckCircle className="h-4 w-4" />
                        Setujui Opname
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    );
}
