import { Head } from '@inertiajs/react';
import { AlertTriangle, PackageX, CheckCircle2 } from 'lucide-react';
import { Badge } from '@/components/ui/badge';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { formatQuantity } from '@/utils/format';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Reports', href: '/dashboard/reports' },
    { title: 'Stock Alerts', href: '/dashboard/reports/alerts' },
];

interface Alert {
    type: 'low_stock' | 'out_of_stock';
    message: string;
    current_qty: number;
    min_stock: number;
    unit: string;
}

interface Props {
    alerts: {
        low_stock: Alert[];
        out_of_stock: Alert[];
        total_alerts: number;
    };
}

// Konfigurasi visual untuk setiap tipe alert agar kode lebih clean
const ALERT_CONFIG = {
    low_stock: {
        icon: AlertTriangle,
        iconColor: 'text-yellow-600 dark:text-yellow-500',
        iconBg: 'bg-yellow-100 dark:bg-yellow-900/20',
        badgeLabel: 'Stok Rendah',
        badgeClass: 'bg-yellow-100 text-yellow-800 hover:bg-yellow-100 border-transparent dark:bg-yellow-900/50 dark:text-yellow-400',
    },
    out_of_stock: {
        icon: PackageX,
        iconColor: 'text-red-600 dark:text-red-500',
        iconBg: 'bg-red-100 dark:bg-red-900/20',
        badgeLabel: 'Stok Habis',
        badgeClass: 'bg-red-100 text-red-800 hover:bg-red-100 border-transparent dark:bg-red-900/50 dark:text-red-400',
    },
} as const;

export default function StockAlerts({ alerts }: Props) {
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Stock Alerts" />

            <div className="flex h-full flex-1 flex-col gap-5 rounded-xl p-4 lg:p-6 bg-slate-50/50 dark:bg-transparent">
                {/* Header Section */}
                <div className="flex flex-col gap-1 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <h1 className="text-2xl font-bold tracking-tight text-foreground sm:text-3xl">
                            Notifikasi Stok
                        </h1>
                        <p className="text-sm text-muted-foreground mt-1">
                            Memerlukan perhatian: <span className="font-medium text-foreground">{alerts.total_alerts} item</span>
                        </p>
                    </div>
                </div>

                {/* Summary Cards */}
                <div className="grid gap-4 md:grid-cols-2">
                    <Card className="transition-all hover:shadow-md hover:border-yellow-500/20">
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium text-muted-foreground">
                                Total Stok Rendah
                            </CardTitle>
                            <div className="rounded-full bg-yellow-100 p-2 dark:bg-yellow-900/20">
                                <AlertTriangle className="h-4 w-4 text-yellow-600 dark:text-yellow-500" />
                            </div>
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold text-yellow-600 dark:text-yellow-500">
                                {alerts.low_stock.length}
                            </div>
                            <p className="text-xs text-muted-foreground mt-1">
                                Item mendekati batas minimum
                            </p>
                        </CardContent>
                    </Card>

                    <Card className="transition-all hover:shadow-md hover:border-red-500/20">
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium text-muted-foreground">
                                Total Stok Habis
                            </CardTitle>
                            <div className="rounded-full bg-red-100 p-2 dark:bg-red-900/20">
                                <PackageX className="h-4 w-4 text-red-600 dark:text-red-500" />
                            </div>
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold text-red-600 dark:text-red-500">
                                {alerts.out_of_stock.length}
                            </div>
                            <p className="text-xs text-muted-foreground mt-1">
                                Item kosong dan perlu segera dipesan
                            </p>
                        </CardContent>
                    </Card>
                </div>

                {/* Lists Section */}
                <div className="flex flex-col gap-6">
                    {/* Out of Stock Alerts (Diprioritaskan di atas karena lebih kritikal) */}
                    {alerts.out_of_stock.length > 0 && (
                        <Card className="border-red-100 shadow-sm dark:border-red-900/20">
                            <CardHeader className="pb-3 bg-red-50/50 dark:bg-red-900/10 rounded-t-xl">
                                <CardTitle className="flex items-center gap-2 text-lg text-red-700 dark:text-red-400">
                                    <PackageX className="h-5 w-5" />
                                    Kritis: Stok Habis
                                </CardTitle>
                                <CardDescription>
                                    Item berikut sudah tidak tersedia di gudang manapun
                                </CardDescription>
                            </CardHeader>
                            <CardContent className="pt-4">
                                <div className="space-y-3">
                                    {alerts.out_of_stock.map((alert, index) => {
                                        const config = ALERT_CONFIG[alert.type];
                                        const Icon = config.icon;
                                        return (
                                            <div key={index} className="flex flex-col sm:flex-row sm:items-center justify-between gap-4 rounded-lg border bg-card p-4 transition-all hover:bg-muted/50 hover:shadow-sm">
                                                <div className="flex items-center gap-4">
                                                    <div className={`rounded-full p-2.5 ${config.iconBg}`}>
                                                        <Icon className={`h-5 w-5 ${config.iconColor}`} />
                                                    </div>
                                                    <div>
                                                        <p className="font-semibold text-foreground">{alert.message}</p>
                                                        <p className="text-sm text-muted-foreground mt-0.5">
                                                            Sisa: <span className="font-medium text-foreground">{alert.current_qty}</span> {alert.unit}
                                                            <span className="mx-2 text-muted-foreground/40">•</span>
                                                            Min: {alert.min_stock} {alert.unit}
                                                        </p>
                                                    </div>
                                                </div>
                                                <Badge variant="secondary" className={`w-fit shrink-0 ${config.badgeClass}`}>
                                                    {config.badgeLabel}
                                                </Badge>
                                            </div>
                                        );
                                    })}
                                </div>
                            </CardContent>
                        </Card>
                    )}

                    {/* Low Stock Alerts */}
                    {alerts.low_stock.length > 0 && (
                        <Card className="shadow-sm">
                            <CardHeader className="pb-3">
                                <CardTitle className="flex items-center gap-2 text-lg">
                                    <AlertTriangle className="h-5 w-5 text-yellow-600" />
                                    Peringatan: Stok Rendah
                                </CardTitle>
                                <CardDescription>
                                    Item yang perlu diisi ulang sebelum habis
                                </CardDescription>
                            </CardHeader>
                            <CardContent>
                                <div className="space-y-3">
                                    {alerts.low_stock.map((alert, index) => {
                                        const config = ALERT_CONFIG[alert.type];
                                        const Icon = config.icon;
                                        return (
                                            <div key={index} className="flex flex-col sm:flex-row sm:items-center justify-between gap-4 rounded-lg border bg-card p-4 transition-all hover:bg-muted/50 hover:shadow-sm">
                                                <div className="flex items-center gap-4">
                                                    <div className={`rounded-full p-2.5 ${config.iconBg}`}>
                                                        <Icon className={`h-5 w-5 ${config.iconColor}`} />
                                                    </div>
                                                    <div>
                                                        <p className="font-semibold text-foreground">{alert.message}</p>
                                                        <p className="text-sm text-muted-foreground mt-0.5">
                                                            Sisa: <span className="font-medium text-foreground">{formatQuantity(alert.current_qty)}</span> {alert.unit}
                                                            <span className="mx-2 text-muted-foreground/40">•</span>
                                                            Min: {formatQuantity(alert.min_stock)} {alert.unit}
                                                        </p>
                                                    </div>
                                                </div>
                                                <Badge variant="secondary" className={`w-fit shrink-0 ${config.badgeClass}`}>
                                                    {config.badgeLabel}
                                                </Badge>
                                            </div>
                                        );
                                    })}
                                </div>
                            </CardContent>
                        </Card>
                    )}

                    {/* No Alerts State */}
                    {alerts.total_alerts === 0 && (
                        <Card className="border-dashed border-2">
                            <CardContent className="flex flex-col items-center justify-center py-16 text-center">
                                <div className="rounded-full bg-green-100 p-4 dark:bg-green-900/20 mb-4">
                                    <CheckCircle2 className="h-8 w-8 text-green-600 dark:text-green-500" />
                                </div>
                                <h3 className="text-xl font-semibold text-foreground">
                                    Semua Stok Aman
                                </h3>
                                <p className="mt-2 text-sm text-muted-foreground max-w-sm">
                                    Hebat! Saat ini tidak ada produk yang kehabisan stok ataupun berada di bawah batas minimum.
                                </p>
                            </CardContent>
                        </Card>
                    )}
                </div>
            </div>
        </AppLayout>
    );
}
