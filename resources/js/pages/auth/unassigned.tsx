import { Head, Link } from '@inertiajs/react';
import { Building2, LogOut } from 'lucide-react';
import AuthLayout from '@/layouts/auth-layout';
import { logout } from '@/routes';

export default function Unassigned() {
    return (
        <AuthLayout
            title="Akses Belum Tersedia"
            description="Akun Anda belum memiliki akses ke gudang."
        >
            <Head title="Akses Belum Tersedia" />

            <div className="mx-auto w-full max-w-md">
                <div className="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                    <div className="mb-5 flex items-center gap-3">
                        <div className="flex h-12 w-12 items-center justify-center rounded-xl bg-gray-100">
                            <Building2 className="h-6 w-6 text-gray-700" />
                        </div>

                        <div>
                            <h2 className="text-lg font-semibold text-gray-900">
                                Belum ada penugasan gudang
                            </h2>
                            <p className="text-sm text-gray-500">
                                Akses akun masih menunggu penugasan.
                            </p>
                        </div>
                    </div>

                    <div className="space-y-4">
                        <p className="text-sm leading-6 text-gray-600">
                            Saat ini akun Anda belum ditugaskan ke gudang mana
                            pun, sehingga dashboard dan data inventaris belum
                            dapat diakses.
                        </p>

                        <div className="rounded-xl bg-gray-50 p-4">
                            <p className="text-sm font-medium text-gray-900">
                                Yang perlu dilakukan
                            </p>

                            <ul className="mt-2 space-y-2 text-sm text-gray-600">
                                <li>
                                    • Hubungi Super Admin untuk mendapatkan
                                    penugasan gudang.
                                </li>
                                <li>
                                    • Setelah penugasan selesai, silakan login
                                    kembali.
                                </li>
                            </ul>
                        </div>

                        <Link
                            href={logout()}
                            className="inline-flex w-full items-center justify-center gap-2 rounded-xl border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50"
                        >
                            <LogOut className="h-4 w-4" />
                            Keluar dari akun
                        </Link>
                    </div>
                </div>
            </div>
        </AuthLayout>
    );
}
