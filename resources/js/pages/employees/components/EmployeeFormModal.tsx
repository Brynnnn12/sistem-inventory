import { useForm } from '@inertiajs/react';
import { Eye, EyeOff, Lock, Mail, Save, Shield, User } from 'lucide-react';
import { useEffect, useState } from 'react';
import InputError from '@/components/input-error';
import { ModalHeader } from '@/components/modal-header';
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogFooter } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { Separator } from '@/components/ui/separator';
import type { User as EmployeeUser } from '@/types/models/employee';

interface EmployeeFormModalProps {
    open: boolean;
    employee?: EmployeeUser | null;
    onClose: () => void;
}

export function EmployeeFormModal({
    open,
    employee,
    onClose,
}: EmployeeFormModalProps) {
    const isEditing = !!employee;
    const [showPassword, setShowPassword] = useState(false);
    const [showConfirm, setShowConfirm] = useState(false);

    const form = useForm({
        name: '',
        email: '',
        phone_number: '',
        role: 'viewer',
        password: '',
        password_confirmation: '',
    });

    useEffect(() => {
        if (open) {
            if (employee) {
                form.setData({
                    name: employee.name,
                    email: employee.email,
                    phone_number: employee.phone_number || '',
                    role: employee.roles?.[0]?.name || 'viewer',
                    password: '',
                    password_confirmation: '',
                });
            } else {
                form.reset();
                form.clearErrors();
                form.setData({
                    name: '',
                    email: '',
                    phone_number: '',
                    role: 'viewer',
                    password: '',
                    password_confirmation: '',
                });
            }
        }
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [employee, open]);

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();

        if (isEditing) {
            form.put(`/dashboard/employees/${employee.id}`, {
                preserveScroll: true,
                onSuccess: () => {
                    onClose();
                },
            });
        } else {
            form.post('/dashboard/employees', {
                preserveScroll: true,
                onSuccess: () => {
                    form.reset();
                    onClose();
                },
            });
        }
    };

    const handleClose = () => {
        form.reset();
        form.clearErrors();
        onClose();
    };

    return (
        <Dialog open={open} onOpenChange={(isOpen) => !isOpen && handleClose()}>
            <DialogContent className="max-h-[90vh] overflow-y-auto sm:max-w-150">
                <form onSubmit={handleSubmit}>
                    <ModalHeader
                        icon={User}
                        title={isEditing ? 'Edit Karyawan' : 'Tambah Karyawan'}
                        description={
                            isEditing
                                ? 'Perbarui informasi karyawan'
                                : 'Tambahkan karyawan baru ke sistem'
                        }
                    />

                    <div className="space-y-6 py-4">
                        {/* Basic Information */}
                        <div className="space-y-4">
                            <div className="flex items-center gap-2 text-sm font-medium">
                                <User className="h-4 w-4" />
                                <span>Informasi Dasar</span>
                            </div>
                            <Separator />

                            <div className="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <div className="space-y-2">
                                    <Label
                                        htmlFor={`${isEditing ? 'edit' : 'create'}-name`}
                                    >
                                        Nama Lengkap{' '}
                                        <span className="text-destructive">
                                            *
                                        </span>
                                    </Label>
                                    <div className="relative">
                                        <User className="absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                                        <Input
                                            id={`${isEditing ? 'edit' : 'create'}-name`}
                                            value={form.data.name}
                                            onChange={(e) =>
                                                form.setData(
                                                    'name',
                                                    e.target.value,
                                                )
                                            }
                                            placeholder="Contoh: Ahmad Suharto"
                                            required
                                            maxLength={50}
                                            className="pl-9"
                                        />
                                    </div>
                                    <InputError message={form.errors.name} />
                                </div>

                                <div className="space-y-2">
                                    <Label
                                        htmlFor={`${isEditing ? 'edit' : 'create'}-email`}
                                    >
                                        Alamat Email{' '}
                                        <span className="text-destructive">
                                            *
                                        </span>
                                    </Label>
                                    <div className="relative">
                                        <Mail className="absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                                        <Input
                                            id={`${isEditing ? 'edit' : 'create'}-email`}
                                            type="email"
                                            value={form.data.email}
                                            onChange={(e) =>
                                                form.setData(
                                                    'email',
                                                    e.target.value,
                                                )
                                            }
                                            placeholder="contoh@email.com"
                                            required
                                            maxLength={50}
                                            className="pl-9"
                                        />
                                    </div>
                                    <InputError message={form.errors.email} />
                                </div>
                            </div>

                            <div className="space-y-2">
                                <Label
                                    htmlFor={`${isEditing ? 'edit' : 'create'}-phone`}
                                >
                                    Nomor HP (WhatsApp){' '}
                                    <span className="text-destructive">*</span>
                                </Label>
                                <Input
                                    id={`${isEditing ? 'edit' : 'create'}-phone`}
                                    type="tel"
                                    value={form.data.phone_number}
                                    onChange={(e) =>
                                        form.setData(
                                            'phone_number',
                                            e.target.value,
                                        )
                                    }
                                    placeholder="628123456789"
                                    required
                                    maxLength={14}
                                />
                                <InputError
                                    message={form.errors.phone_number}
                                />
                                <p className="text-sm text-muted-foreground">
                                    Format: 628xxxxxxxxxx (diawali 62, maksimal
                                    14 digit)
                                </p>
                            </div>
                        </div>

                        {/* Role */}
                        <div className="space-y-4">
                            <div className="flex items-center gap-2 text-sm font-medium">
                                <Shield className="h-4 w-4" />
                                <span>Peran & Hak Akses</span>
                            </div>
                            <Separator />

                            <div className="space-y-2">
                                <Label
                                    htmlFor={`${isEditing ? 'edit' : 'create'}-role`}
                                >
                                    Peran{' '}
                                    <span className="text-destructive">*</span>
                                </Label>
                                <Select
                                    value={form.data.role}
                                    onValueChange={(value) =>
                                        form.setData('role', value)
                                    }
                                >
                                    <SelectTrigger>
                                        <SelectValue placeholder="Pilih peran" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="admin">
                                            <div className="flex items-center gap-2">
                                                <Shield className="h-4 w-4 text-orange-500" />
                                                <span>Admin</span>
                                            </div>
                                        </SelectItem>
                                        <SelectItem value="viewer">
                                            <div className="flex items-center gap-2">
                                                <User className="h-4 w-4 text-blue-500" />
                                                <span>Viewer</span>
                                            </div>
                                        </SelectItem>
                                    </SelectContent>
                                </Select>
                                <InputError message={form.errors.role} />
                            </div>
                        </div>

                        {/* Security */}
                        <div className="space-y-4">
                            <div className="flex items-center gap-2 text-sm font-medium">
                                <Lock className="h-4 w-4" />
                                <span>Keamanan</span>
                            </div>
                            <Separator />

                            {isEditing && (
                                <p className="text-sm text-muted-foreground">
                                    Kosongkan jika tidak ingin mengubah
                                    password.
                                </p>
                            )}

                            <div className="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <div className="space-y-2">
                                    <Label
                                        htmlFor={`${isEditing ? 'edit' : 'create'}-password`}
                                    >
                                        Password{' '}
                                        <span className="text-destructive">
                                            {!isEditing ? '*' : ''}
                                        </span>
                                    </Label>
                                    <div className="relative">
                                        <Lock className="absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                                        <Input
                                            id={`${isEditing ? 'edit' : 'create'}-password`}
                                            type={
                                                showPassword
                                                    ? 'text'
                                                    : 'password'
                                            }
                                            value={form.data.password}
                                            onChange={(e) =>
                                                form.setData(
                                                    'password',
                                                    e.target.value,
                                                )
                                            }
                                            placeholder="Minimal 8 karakter"
                                            required={!isEditing}
                                            className="pl-9 pr-10"
                                        />
                                        <button
                                            type="button"
                                            onClick={() =>
                                                setShowPassword(!showPassword)
                                            }
                                            className="absolute top-1/2 right-3 -translate-y-1/2 text-muted-foreground hover:text-foreground"
                                            tabIndex={-1}
                                        >
                                            {showPassword ? (
                                                <EyeOff className="h-4 w-4" />
                                            ) : (
                                                <Eye className="h-4 w-4" />
                                            )}
                                        </button>
                                    </div>
                                    <InputError
                                        message={form.errors.password}
                                    />
                                    <p className="text-sm text-muted-foreground">
                                        Minimal 8 karakter, mengandung huruf
                                        besar, huruf kecil, angka, dan simbol.
                                    </p>
                                </div>

                                <div className="space-y-2">
                                    <Label
                                        htmlFor={`${isEditing ? 'edit' : 'create'}-password-confirmation`}
                                    >
                                        Konfirmasi Password{' '}
                                        <span className="text-destructive">
                                            {!isEditing ? '*' : ''}
                                        </span>
                                    </Label>
                                    <div className="relative">
                                        <Lock className="absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                                        <Input
                                            id={`${isEditing ? 'edit' : 'create'}-password-confirmation`}
                                            type={
                                                showConfirm
                                                    ? 'text'
                                                    : 'password'
                                            }
                                            value={
                                                form.data
                                                    .password_confirmation
                                            }
                                            onChange={(e) =>
                                                form.setData(
                                                    'password_confirmation',
                                                    e.target.value,
                                                )
                                            }
                                            placeholder="Ketik ulang password"
                                            required={!isEditing}
                                            className="pl-9 pr-10"
                                        />
                                        <button
                                            type="button"
                                            onClick={() =>
                                                setShowConfirm(!showConfirm)
                                            }
                                            className="absolute top-1/2 right-3 -translate-y-1/2 text-muted-foreground hover:text-foreground"
                                            tabIndex={-1}
                                        >
                                            {showConfirm ? (
                                                <EyeOff className="h-4 w-4" />
                                            ) : (
                                                <Eye className="h-4 w-4" />
                                            )}
                                        </button>
                                    </div>
                                    <InputError
                                        message={
                                            form.errors.password_confirmation
                                        }
                                    />
                                </div>
                            </div>
                        </div>
                    </div>

                    <DialogFooter>
                        <Button
                            type="button"
                            variant="outline"
                            onClick={handleClose}
                            disabled={form.processing}
                        >
                            Batal
                        </Button>
                        <Button type="submit" disabled={form.processing}>
                            <Save className="mr-2 h-4 w-4" />
                            {form.processing
                                ? isEditing
                                    ? 'Menyimpan...'
                                    : 'Membuat...'
                                : isEditing
                                  ? 'Perbarui'
                                  : 'Simpan'}
                        </Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>
    );
}
