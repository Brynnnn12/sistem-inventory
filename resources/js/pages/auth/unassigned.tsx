import { Head, Link } from '@inertiajs/react';
import { Building2, LogOut } from 'lucide-react';
import AuthLayout from '@/layouts/auth-layout';
import { logout } from '@/routes';

export default function Unassigned() {
    return (
        <AuthLayout
            title="Belum Ditugaskan"
            description="Akun Anda belum memiliki akses ke sistem."
        >
            <Head title="Belum Ditugaskan" />

            <div className="flex flex-col items-center gap-6 text-center">
                <div className="flex h-20 w-20 items-center justify-center rounded-full bg-amber-100">
                    <Building2 className="h-10 w-10 text-amber-600" />
                </div>

                <div className="space-y-2">
                    <h2 className="text-xl font-semibold text-gray-900">
                        Anda Belum Ditugaskan ke Gudang
                    </h2>
                    <p className="text-sm text-gray-500">
                        Akun Anda belum ditugaskan ke gudang mana pun. Silakan
                        hubungi Super Admin untuk mendapatkan penugasan gudang.
                    </p>
                </div>

                <div className="w-full space-y-3">
                    <div className="rounded-lg border border-amber-200 bg-amber-50 p-4 text-left text-sm text-amber-800">
                        <p className="font-medium">Informasi:</p>
                        <ul className="mt-1 list-inside list-disc space-y-1">
                            <li>
                                Super Admin dapat menugaskan Anda melalui menu{' '}
                                <span className="font-medium">
                                    Warehouse &gt; Penugasan
                                </span>
                            </li>
                            <li>
                                Setelah ditugaskan, silakan login kembali untuk
                                mengakses dashboard
                            </li>
                        </ul>
                    </div>

                    <Link
                        href={logout()}
                        className="inline-flex w-full items-center justify-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50"
                    >
                        <LogOut className="h-4 w-4" />
                        Keluar
                    </Link>
                </div>
            </div>
        </AuthLayout>
    );
}
