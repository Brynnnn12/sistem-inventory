import { Head, Link } from '@inertiajs/react';
import {
    Package,
    Warehouse,
    AlertTriangle,
    PackageX,
    ArrowUpRight,
    ArrowDownLeft,
    ArrowRightLeft,
    BarChart3,
    FileText,
    Bell,
} from 'lucide-react';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { useAuth } from '@/hooks/use-auth';
import AppLayout from '@/layouts/app-layout';
import { EmployeesList } from '@/pages/dashboard/EmployeesList';
import MonthlyChart from '@/pages/dashboard/MonthlyChart';
import { ProductsList } from '@/pages/dashboard/ProductsList';
import { dashboard } from '@/routes';
import type { BreadcrumbItem } from '@/types';

import type { DashboardProps } from '@/types/models/dashboard';
import { formatQuantity } from '@/utils/format';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
];

export default function Dashboard({
    products,
    employees,
    stockSummary,
    recentTransactions,
    stockAlerts,
    monthlyChart,
}: DashboardProps) {
    const { isSuperAdmin } = useAuth();

    const getTransactionIcon = (type: string) => {
        switch (type) {
            case 'inbound':
                return <ArrowDownLeft className="h-4 w-4 text-green-600" />;
            case 'outbound':
                return <ArrowUpRight className="h-4 w-4 text-red-600" />;
            case 'mutation':
                return <ArrowRightLeft className="h-4 w-4 text-blue-600" />;
            default:
                return null;
        }
    };

    const getTransactionBadge = (type: string) => {
        switch (type) {
            case 'inbound':
                return (
                    <Badge
                        variant="default"
                        className="bg-green-100 text-green-800"
                    >
                        Masuk
                    </Badge>
                );
            case 'outbound':
                return (
                    <Badge
                        variant="default"
                        className="bg-red-100 text-red-800"
                    >
                        Keluar
                    </Badge>
                );
            case 'mutation':
                return (
                    <Badge
                        variant="default"
                        className="bg-blue-100 text-blue-800"
                    >
                        Mutasi
                    </Badge>
                );
            default:
                return <Badge variant="secondary">{type}</Badge>;
        }
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Dashboard" />
            <div className="flex h-full flex-1 flex-col gap-6 rounded-xl p-4">
                <div className="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h1 className="text-2xl font-bold tracking-tight sm:text-3xl">
                            Dashboard GudangKu
                        </h1>
                        <p className="text-sm text-muted-foreground sm:text-base">
                            Ringkasan inventory dan aktivitas terbaru
                        </p>
                    </div>
                    <div className="flex gap-2">
                        <Button variant="outline" size="sm" asChild>
                            <Link href="/dashboard/reports/stock">
                                <FileText className="h-4 w-4 sm:mr-2" />
                                <span className="hidden sm:inline">
                                    Laporan Stok
                                </span>
                            </Link>
                        </Button>
                        <Button variant="outline" size="sm" asChild>
                            <Link href="/dashboard/reports/alerts">
                                <Bell className="h-4 w-4 sm:mr-2" />
                                <span className="hidden sm:inline">
                                    Notifikasi
                                </span>
                            </Link>
                        </Button>
                    </div>
                </div>

                {/* Stock Summary Cards */}
                <div className="grid grid-cols-2 gap-4 md:grid-cols-2 lg:grid-cols-4">
                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium">
                                Total Produk
                            </CardTitle>
                            <Package className="h-4 w-4 text-muted-foreground" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold">
                                {stockSummary.total_products}
                            </div>
                        </CardContent>
                    </Card>
                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium">
                                Total Gudang
                            </CardTitle>
                            <Warehouse className="h-4 w-4 text-muted-foreground" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold">
                                {stockSummary.total_warehouses}
                            </div>
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium">
                                Stok Rendah
                            </CardTitle>
                            <AlertTriangle className="h-4 w-4 text-yellow-600" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold text-yellow-600">
                                {stockSummary.low_stock_count}
                            </div>
                        </CardContent>
                    </Card>
                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium">
                                Stok Habis
                            </CardTitle>
                            <PackageX className="h-4 w-4 text-red-600" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold text-red-600">
                                {stockSummary.out_of_stock_count}
                            </div>
                        </CardContent>
                    </Card>
                </div>

                {/* Charts and Recent Activity */}
                <div className="grid gap-6 lg:grid-cols-3">
                    {/* Monthly Chart Placeholder */}
                    <Card className="lg:col-span-2">
                        <CardHeader>
                            <CardTitle className="flex items-center gap-2">
                                <BarChart3 className="h-5 w-5" />
                                Tren Bulanan
                            </CardTitle>
                            <CardDescription>
                                Pergerakan barang masuk dan keluar per bulan
                            </CardDescription>
                        </CardHeader>
                        <CardContent>
                            <MonthlyChart data={monthlyChart} />
                        </CardContent>
                    </Card>

                    {/* Recent Transactions */}
                    <Card>
                        <CardHeader>
                            <CardTitle>Aktivitas Terbaru</CardTitle>
                            <CardDescription>
                                Transaksi terakhir dalam sistem
                            </CardDescription>
                        </CardHeader>
                        <CardContent>
                            <div className="space-y-4">
                                {recentTransactions.map(
                                    (transaction, index) => (
                                        <div
                                            key={index}
                                            className="flex items-start gap-3"
                                        >
                                            <div className="mt-1">
                                                {getTransactionIcon(
                                                    transaction.type,
                                                )}
                                            </div>
                                            <div className="flex-1 space-y-1">
                                                <div className="flex items-center gap-2">
                                                    <p className="min-w-0 text-sm font-medium">
                                                        {transaction.product}
                                                    </p>
                                                    {getTransactionBadge(
                                                        transaction.type,
                                                    )}
                                                </div>
                                                <p className="text-xs text-muted-foreground">
                                                    {transaction.type ===
                                                    'mutation'
                                                        ? `${transaction.from_warehouse} → ${transaction.to_warehouse}`
                                                        : transaction.warehouse}
                                                </p>
                                                <p className="text-xs text-muted-foreground">
                                                    {transaction.code} • Qty:{' '}
                                                    {formatQuantity(
                                                        transaction.quantity,
                                                    )}
                                                </p>
                                            </div>
                                        </div>
                                    ),
                                )}
                            </div>
                        </CardContent>
                    </Card>
                </div>

                {/* Stock Alerts */}
                {stockAlerts.length > 0 && (
                    <Card>
                        <CardHeader>
                            <CardTitle className="flex items-center gap-2">
                                <Bell className="h-5 w-5 text-orange-600" />
                                Peringatan Stok
                            </CardTitle>
                            <CardDescription>
                                Item yang perlu perhatian segera
                            </CardDescription>
                        </CardHeader>
                        <CardContent>
                            <div className="space-y-3">
                                {stockAlerts.slice(0, 3).map((alert, index) => (
                                    <div
                                        key={index}
                                        className="flex items-center justify-between gap-2 rounded-lg border p-3"
                                    >
                                        <div className="flex min-w-0 flex-1 items-center gap-3">
                                            {alert.type === 'low_stock' ? (
                                                <AlertTriangle className="h-4 w-4 text-yellow-600" />
                                            ) : (
                                                <PackageX className="h-4 w-4 text-red-600" />
                                            )}
                                            <div>
                                                <p className="text-sm font-medium">
                                                    {alert.message}
                                                </p>
                                                <p className="text-xs text-muted-foreground">
                                                    Stok: {alert.current_qty}{' '}
                                                    {alert.unit} (Min:{' '}
                                                    {alert.min_stock})
                                                </p>
                                            </div>
                                        </div>
                                        <Badge
                                            variant={
                                                alert.type === 'low_stock'
                                                    ? 'secondary'
                                                    : 'destructive'
                                            }
                                        >
                                            {alert.type === 'low_stock'
                                                ? 'Rendah'
                                                : 'Habis'}
                                        </Badge>
                                    </div>
                                ))}
                                {stockAlerts.length > 3 && (
                                    <div className="pt-2 text-center">
                                        <Button
                                            variant="outline"
                                            size="sm"
                                            asChild
                                        >
                                            <Link href="/dashboard/reports/alerts">
                                                Lihat Semua (
                                                {stockAlerts.length})
                                            </Link>
                                        </Button>
                                    </div>
                                )}
                            </div>
                        </CardContent>
                    </Card>
                )}

                {/* Products and Employees Grid */}
                <div className="grid gap-6 lg:grid-cols-2">
                    <ProductsList products={products} />
                    {isSuperAdmin && <EmployeesList employees={employees} />}
                </div>
            </div>
        </AppLayout>
    );
}
