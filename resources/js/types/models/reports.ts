export interface Warehouse {
    id: number;
    name: string;
}

export interface StockItem {
    warehouse_name: string;
    product_code: string;
    product_name: string;
    unit: string;
    quantity: number;
    available_qty: number;
    min_stock: number;
    max_stock: number;
    cost: number;
    value: number;
    status: 'normal' | 'low_stock' | 'out_of_stock';
}

export interface StockReportFilters {
    warehouse_id?: string;
    start_date?: string;
    end_date?: string;
}

export interface StockReportSummary {
    total_items: number;
    total_value: number;
    low_stock_items: number;
    out_of_stock_items: number;
    warehouses_count: number;
}

export interface StockReportData {
    data: StockItem[] | Record<string, StockItem[]>;
    summary: StockReportSummary;
    period: {
        start_date: string;
        end_date: string;
    };
}

export interface StockReportPageProps {
    stockReport: StockReportData;
    warehouses: Warehouse[];
    filters: StockReportFilters;
}
