export interface Supplier {
    id: number;
    code: string;
    name: string;
    contact_person: string;
    phone: string;
    email: string;
    address: string;
    tax_id: string;
    is_active: boolean;
    created_at: string;
    updated_at: string;
    deleted_at?: string | null;
}

export interface Filters {
    search?: string;
    active_only?: boolean;
}

export interface PageProps {
    data: Supplier[];
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
}
