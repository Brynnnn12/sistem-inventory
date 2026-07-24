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
    Activity,
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

// Refactor: Ekstrak konfigurasi transaksi ke luar komponen agar tidak di-recreate setiap render
const TRANSACTION_CONFIG = {
    inbound: {
        icon: ArrowDownLeft,
        iconClass: 'text-green-600',
        badgeLabel: 'Masuk',
        badgeClass: 'bg-green-100 text-green-800 border-transparent',
    },
    outbound: {
        icon: ArrowUpRight,
        iconClass: 'text-red-600',
        badgeLabel: 'Keluar',
        badgeClass: 'bg-red-100 text-red-800 border-transparent',
    },
    mutation: {
        icon: ArrowRightLeft,
        iconClass: 'text-blue-600',
        badgeLabel: 'Mutasi',
        badgeClass: 'bg-blue-100 text-blue-800 border-transparent',
    },
} as const;

export default function Dashboard({
    products,
    employees,
    stockSummary,
    recentTransactions,
    stockAlerts,
    monthlyChart,
}: DashboardProps) {
    const { isSuperAdmin } = useAuth();

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Dashboard" />
            <div className="flex h-full flex-1 flex-col gap-5 rounded-xl p-4 lg:gap-6 lg:p-6 bg-slate-50/50 dark:bg-transparent">

                {/* Header Section */}
                <div className="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h1 className="text-2xl font-bold tracking-tight sm:text-3xl text-foreground">
                            Dashboard GudangKu
                        </h1>
                        <p className="text-sm text-muted-foreground mt-1 sm:text-base">
                            Ringkasan inventory dan aktivitas terbaru
                        </p>
                    </div>
                    <div className="flex flex-wrap gap-2">
                        <Button variant="outline" size="sm" className="h-9" asChild>
                            <Link href="/dashboard/reports/stock">
                                <FileText className="h-4 w-4 sm:mr-2" />
                                <span className="hidden sm:inline">Laporan Stok</span>
                            </Link>
                        </Button>
                        <Button variant="outline" size="sm" className="h-9 relative" asChild>
                            <Link href="/dashboard/reports/alerts">
                                <Bell className="h-4 w-4 sm:mr-2" />
                                <span className="hidden sm:inline">Notifikasi</span>
                                {stockAlerts.length > 0 && (
                                    <span className="absolute -top-1 -right-1 flex h-3 w-3">
                                        <span className="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                                        <span className="relative inline-flex rounded-full h-3 w-3 bg-red-500"></span>
                                    </span>
                                )}
                            </Link>
                        </Button>
                    </div>
                </div>

                {/* Stock Summary Cards */}
                <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <Card className="transition-all hover:shadow-md hover:border-primary/20">
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium text-muted-foreground">
                                Total Produk
                            </CardTitle>
                            <div className="rounded-full bg-primary/10 p-2">
                                <Package className="h-4 w-4 text-primary" />
                            </div>
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold">{stockSummary.total_products}</div>
                        </CardContent>
                    </Card>

                    <Card className="transition-all hover:shadow-md hover:border-primary/20">
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium text-muted-foreground">
                                Total Gudang
                            </CardTitle>
                            <div className="rounded-full bg-primary/10 p-2">
                                <Warehouse className="h-4 w-4 text-primary" />
                            </div>
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold">{stockSummary.total_warehouses}</div>
                        </CardContent>
                    </Card>

                    <Card className="transition-all hover:shadow-md hover:border-yellow-500/20">
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium text-muted-foreground">
                                Stok Rendah
                            </CardTitle>
                            <div className="rounded-full bg-yellow-100 p-2 dark:bg-yellow-900/20">
                                <AlertTriangle className="h-4 w-4 text-yellow-600 dark:text-yellow-500" />
                            </div>
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold text-yellow-600 dark:text-yellow-500">
                                {stockSummary.low_stock_count}
                            </div>
                        </CardContent>
                    </Card>

                    <Card className="transition-all hover:shadow-md hover:border-red-500/20">
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium text-muted-foreground">
                                Stok Habis
                            </CardTitle>
                            <div className="rounded-full bg-red-100 p-2 dark:bg-red-900/20">
                                <PackageX className="h-4 w-4 text-red-600 dark:text-red-500" />
                            </div>
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold text-red-600 dark:text-red-500">
                                {stockSummary.out_of_stock_count}
                            </div>
                        </CardContent>
                    </Card>
                </div>

                {/* Main Content Layout: Chart (Left) & Sidebars (Right) */}
                <div className="grid gap-4 lg:grid-cols-3 lg:gap-6 items-start">

                    {/* Monthly Chart */}
                    <Card className="lg:col-span-2 shadow-sm">
                        <CardHeader>
                            <CardTitle className="flex items-center gap-2 text-lg">
                                <BarChart3 className="h-5 w-5 text-primary" />
                                Tren Bulanan
                            </CardTitle>
                            <CardDescription>
                                Pergerakan barang masuk dan keluar per bulan
                            </CardDescription>
                        </CardHeader>
                        <CardContent className="min-h-[300px]">
                            <MonthlyChart data={monthlyChart} />
                        </CardContent>
                    </Card>

                    {/* Right Sidebar: Alerts & Transactions */}
                    <div className="flex flex-col gap-4 lg:gap-6">

                        {/* Stock Alerts (Dipindah ke samping agar lebih terlihat) */}
                        {stockAlerts.length > 0 && (
                            <Card className="border-orange-200 shadow-sm dark:border-orange-900/50">
                                <CardHeader className="pb-3">
                                    <CardTitle className="flex items-center gap-2 text-base">
                                        <Bell className="h-4 w-4 text-orange-600" />
                                        Perlu Perhatian
                                    </CardTitle>
                                </CardHeader>
                                <CardContent>
                                    <div className="space-y-3">
                                        {stockAlerts.slice(0, 3).map((alert, index) => (
                                            <div key={index} className="flex items-center justify-between gap-3 rounded-md bg-muted/50 p-3 transition-colors hover:bg-muted">
                                                <div className="flex min-w-0 flex-1 items-center gap-3">
                                                    {alert.type === 'low_stock' ? (
                                                        <AlertTriangle className="h-4 w-4 shrink-0 text-yellow-600" />
                                                    ) : (
                                                        <PackageX className="h-4 w-4 shrink-0 text-red-600" />
                                                    )}
                                                    <div className="min-w-0">
                                                        <p className="truncate text-sm font-medium text-foreground">
                                                            {alert.message}
                                                        </p>
                                                        <p className="text-xs text-muted-foreground">
                                                            Sisa: <span className="font-medium text-foreground">{alert.current_qty}</span> {alert.unit}
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        ))}
                                        {stockAlerts.length > 3 && (
                                            <Button variant="ghost" size="sm" className="w-full text-xs text-muted-foreground hover:text-foreground mt-2" asChild>
                                                <Link href="/dashboard/reports/alerts">
                                                    Lihat {stockAlerts.length - 3} lainnya
                                                </Link>
                                            </Button>
                                        )}
                                    </div>
                                </CardContent>
                            </Card>
                        )}

                        {/* Recent Transactions */}
                        <Card className="shadow-sm">
                            <CardHeader className="pb-3">
                                <CardTitle className="text-base">Aktivitas Terbaru</CardTitle>
                                <CardDescription className="text-xs">
                                    Transaksi terakhir dalam sistem
                                </CardDescription>
                            </CardHeader>
                            <CardContent>
                                <div className="space-y-4">
                                    {recentTransactions.map((transaction, index) => {
                                        const config = TRANSACTION_CONFIG[transaction.type as keyof typeof TRANSACTION_CONFIG];
                                        const TxIcon = config?.icon || Activity;

                                        return (
                                            <div key={index} className="flex items-start gap-3 border-b last:border-0 pb-3 last:pb-0">
                                                <div className={`mt-0.5 rounded-full p-1.5 bg-muted`}>
                                                    <TxIcon className={`h-3.5 w-3.5 ${config?.iconClass || 'text-muted-foreground'}`} />
                                                </div>
                                                <div className="flex-1 space-y-1 overflow-hidden">
                                                    <div className="flex items-center justify-between gap-2">
                                                        <p className="truncate text-sm font-medium text-foreground">
                                                            {transaction.product}
                                                        </p>
                                                        {config ? (
                                                            <Badge variant="secondary" className={`text-[10px] px-1.5 py-0 ${config.badgeClass}`}>
                                                                {config.badgeLabel}
                                                            </Badge>
                                                        ) : (
                                                            <Badge variant="outline" className="text-[10px] px-1.5 py-0">{transaction.type}</Badge>
                                                        )}
                                                    </div>
                                                    <div className="flex items-center justify-between text-xs text-muted-foreground">
                                                        <p className="truncate pr-2">
                                                            {transaction.type === 'mutation'
                                                                ? `${transaction.from_warehouse} → ${transaction.to_warehouse}`
                                                                : transaction.warehouse}
                                                        </p>
                                                        <p className="shrink-0 font-medium text-foreground">
                                                            {formatQuantity(transaction.quantity)}
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        );
                                    })}
                                </div>
                            </CardContent>
                        </Card>
                    </div>
                </div>

                {/* Products and Employees Grid */}
                <div className="grid gap-4 lg:grid-cols-2 lg:gap-6">
                    <ProductsList products={products} />
                    {isSuperAdmin && <EmployeesList employees={employees} />}
                </div>
            </div>
        </AppLayout>
    );
}
