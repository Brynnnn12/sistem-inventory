export type Warehouse = {
    id: number;
    code: string;
    name: string;
    address: string;
    phone: string | null;
    is_active: boolean;
    created_at: string;
    updated_at: string;
    deleted_at: string | null;
};

export type WarehouseFormData = {
    code: string;
    name: string;
    address: string;
    phone: string;
    is_active: boolean;
};

export type WarehouseFilters = {
    search?: string;
    is_active?: boolean;
    sort?: string;
    direction?: 'asc' | 'desc';
};

export type WarehouseIndexProps = {
    warehouses: {
        data: Warehouse[];
        current_page: number;
        last_page: number;
        per_page: number;
        total: number;
        from: number;
        to: number;
    };
    filters: WarehouseFilters;
};

export type WarehouseShowProps = {
    warehouse: Warehouse;
};

export type WarehouseFormProps = {
    warehouse?: Warehouse;
    isEditing?: boolean;
};

export type PageProps = {
    data: Warehouse[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    from: number;
    to: number;
    links: Array<{
        url: string | null;
        label: string;
        active: boolean;
    }>;
};
