import { Head, router } from '@inertiajs/react';
import { useState } from 'react';
import AppLayout from '@/layouts/app-layout';
import { formatDate } from '@/lib/utils';
import { type BreadcrumbItem } from '@/types';
import type {
    StockReportData,
    StockReportFilters,
    Warehouse,
} from '@/types/models/reports';
import { StockReportTable } from './components/StockReportTable';
import { StockReportToolbar } from './components/StockReportToolbar';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Reports', href: '/dashboard/reports' },
    { title: 'Stock Report', href: '/dashboard/reports/stock' },
];

interface Props {
    stockReport: StockReportData;
    warehouses: Warehouse[];
    filters: StockReportFilters;
}

export default function StockReport({
    stockReport,
    warehouses,
    filters,
}: Props) {
    const [selectedWarehouse, setSelectedWarehouse] = useState(
        filters.warehouse_id || 'all',
    );
    const [startDate, setStartDate] = useState(
        filters.start_date || stockReport.period.start_date,
    );
    const [endDate, setEndDate] = useState(
        filters.end_date || stockReport.period.end_date,
    );

    const handleFilter = () => {
        router.get(
            '/dashboard/reports/stock',
            {
                warehouse_id:
                    selectedWarehouse !== 'all'
                        ? selectedWarehouse
                        : undefined,
                start_date: startDate,
                end_date: endDate,
            },
            {
                preserveState: true,
                preserveScroll: true,
                replace: true,
            },
        );
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Stock Report" />

            <div className="p-6">
                <div className="mb-6 flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
                    <div>
                        <h1 className="text-3xl font-bold tracking-tight">
                            Laporan Stok
                        </h1>
                        <p className="mt-1 text-sm text-muted-foreground">
                            Periode: {formatDate(stockReport.period.start_date)}{' '}
                            - {formatDate(stockReport.period.end_date)}
                        </p>
                    </div>
                </div>

                <div className="mb-6">
                    <StockReportToolbar
                        selectedWarehouse={selectedWarehouse}
                        onWarehouseChange={setSelectedWarehouse}
                        startDate={startDate}
                        onStartDateChange={setStartDate}
                        endDate={endDate}
                        onEndDateChange={setEndDate}
                        onApplyFilter={handleFilter}
                        warehouses={warehouses}
                        filters={filters}
                    />
                </div>

                <StockReportTable
                    data={stockReport.data}
                    selectedWarehouse={selectedWarehouse}
                    isLoading={false}
                />
            </div>
        </AppLayout>
    );
}
