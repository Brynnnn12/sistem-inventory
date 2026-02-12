import { Head } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';
import { ProductsList } from '@/pages/dashboard/ProductsList';
import { EmployeesList } from '@/pages/dashboard/EmployeesList';
import { dashboard } from '@/routes';
import type { BreadcrumbItem } from '@/types';

interface DashboardProps {
    products: Array<{
        id: number;
        code: string;
        name: string;
        unit: string;
        price: number;
        is_active: boolean;
        image_url?: string | null;
        category?: {
            name: string;
        };
    }>;
    employees: Array<{
        id: number;
        name: string;
        email: string;
        role?: string;
        is_active: boolean;
        created_at: string;
    }>;
}

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
];

export default function Dashboard({
    products,
    employees,
}: DashboardProps) {
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Dashboard" />
            <div className="flex h-full flex-1 flex-col gap-6 overflow-x-auto rounded-xl p-4">
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-3xl font-bold tracking-tight">Dashboard</h1>
                        <p className="text-muted-foreground">Ringkasan produk dan karyawan</p>
                    </div>
                </div>

                {/* Products and Employees Grid */}
                <div className="grid gap-6 lg:grid-cols-2">
                    <ProductsList products={products} />
                    <EmployeesList employees={employees} />
                </div>
            </div>
        </AppLayout>
    );
}
