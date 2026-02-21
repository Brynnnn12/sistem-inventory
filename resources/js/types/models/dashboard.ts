interface MonthlyChartEntry {
    month: string;
    inbound: number;
    outbound: number;
}

interface DashboardProps {
    products: Array<{
        id: number;
        code: string;
        name: string;
        unit: string;
        price: number;
        is_active: boolean;
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
    stockSummary: {
        total_products: number;
        total_warehouses: number;
        total_stock_value: number;
        low_stock_count: number;
        out_of_stock_count: number;
    };
    recentTransactions: Array<{
        type: 'inbound' | 'outbound' | 'mutation';
        code: string;
        date: string;
        product: string;
        warehouse?: string;
        supplier?: string;
        customer?: string;
        from_warehouse?: string;
        to_warehouse?: string;
        quantity: number;
        status?: string;
    }>;
    stockAlerts: Array<{
        type: 'low_stock' | 'out_of_stock';
        message: string;
        current_qty: number;
        min_stock: number;
        unit: string;
    }>;
    monthlyChart: MonthlyChartEntry[];
}

export type { DashboardProps, MonthlyChartEntry };
