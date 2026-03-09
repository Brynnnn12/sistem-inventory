import { Badge } from '@/components/ui/badge';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Separator } from '@/components/ui/separator';
import { formatQuantity, formatDate } from '@/lib/utils';
import type { Opname } from '@/types/models/opname';

interface OpnameShowModalProps {
    open: boolean;
    opname: Opname | null;
    onClose: () => void;
}

export function OpnameShowModal({
    open,
    opname,
    onClose,
}: OpnameShowModalProps) {
    if (!opname) return null;

    return (
        <Dialog open={open} onOpenChange={onClose}>
            <DialogContent className="max-w-2xl max-h-[90vh] overflow-y-auto">
                <DialogHeader>
                    <DialogTitle className="flex items-center gap-2">
                        Detail Opname
                        <Badge variant="secondary" className="font-mono">
                            {opname.code}
                        </Badge>
                    </DialogTitle>
                    <DialogDescription>
                        Informasi lengkap hasil opname stok
                    </DialogDescription>
                </DialogHeader>

                <div className="space-y-6">
                    {/* Basic Information */}
                    <Card>
                        <CardHeader className="pb-3">
                            <CardTitle className="text-lg">Informasi Dasar</CardTitle>
                        </CardHeader>
                        <CardContent className="space-y-4">
                            <div className="grid grid-cols-2 gap-4">
                                <div>
                                    <label className="text-sm font-medium text-muted-foreground">Produk</label>
                                    <p className="text-sm font-medium">{opname.product.name}</p>
                                </div>
                                <div>
                                    <label className="text-sm font-medium text-muted-foreground">Warehouse</label>
                                    <p className="text-sm font-medium">{opname.warehouse.name}</p>
                                </div>
                                <div>
                                    <label className="text-sm font-medium text-muted-foreground">Tanggal Opname</label>
                                    <p className="text-sm font-medium">{formatDate(opname.opname_date)}</p>
                                </div>
                                <div>
                                    <label className="text-sm font-medium text-muted-foreground mr-2">Status</label>
                                    <Badge variant={opname.status === 'approved' ? 'default' : 'secondary'} className="mt-1">
                                        {opname.status === 'approved' ? 'Approved' : 'Draft'}
                                    </Badge>
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    {/* Quantity Information */}
                    <Card>
                        <CardHeader className="pb-3">
                            <CardTitle className="text-lg">Hasil Opname</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <div className="grid grid-cols-3 gap-4">
                                <div className="text-center p-4 bg-muted/50 rounded-lg">
                                    <label className="text-sm font-medium text-muted-foreground block mb-2">Sistem</label>
                                    <p className="text-lg font-mono font-semibold">{formatQuantity(opname.system_qty)}</p>
                                </div>
                                <div className="text-center p-4 bg-muted/50 rounded-lg">
                                    <label className="text-sm font-medium text-muted-foreground block mb-2">Fisik</label>
                                    <p className="text-lg font-mono font-semibold">{formatQuantity(opname.physical_qty)}</p>
                                </div>
                                <div className="text-center p-4 rounded-lg border-2 border-dashed">
                                    <label className="text-sm font-medium text-muted-foreground block mb-2">Selisih</label>
                                    <p className={`text-lg font-mono font-bold ${
                                        opname.difference_type === 'lebih' ? 'text-green-600' :
                                        opname.difference_type === 'kurang' ? 'text-red-600' : 'text-gray-600'
                                    }`}>
                                        {opname.difference_type === 'lebih' ? '+' : opname.difference_type === 'kurang' ? '-' : ''}
                                        {formatQuantity(opname.difference_qty)}
                                    </p>
                                    {opname.difference_type && (
                                        <Badge
                                            variant={opname.difference_type === 'lebih' ? 'default' : 'destructive'}
                                            className="mt-1 text-xs"
                                        >
                                            {opname.difference_type === 'lebih' ? 'Lebih' : 'Kurang'}
                                        </Badge>
                                    )}
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    {/* Additional Information */}
                    <Card>
                        <CardHeader className="pb-3">
                            <CardTitle className="text-lg">Informasi Tambahan</CardTitle>
                        </CardHeader>
                        <CardContent className="space-y-4">
                            <div className="grid grid-cols-2 gap-4">
                                <div>
                                    <label className="text-sm font-medium text-muted-foreground">Dibuat Oleh</label>
                                    <p className="text-sm font-medium">{opname.creator.name}</p>
                                </div>
                                <div>
                                    <label className="text-sm font-medium text-muted-foreground">Tanggal Dibuat</label>
                                    <p className="text-sm font-medium">{formatDate(opname.created_at)}</p>
                                </div>
                            </div>

                            {opname.notes && (
                                <>
                                    <Separator />
                                    <div>
                                        <label className="text-sm font-medium text-muted-foreground">Catatan</label>
                                        <p className="text-sm mt-1 p-3 bg-muted/50 rounded-md">{opname.notes}</p>
                                    </div>
                                </>
                            )}
                        </CardContent>
                    </Card>
                </div>
            </DialogContent>
        </Dialog>
    );
}
