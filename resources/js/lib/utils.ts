import type { InertiaLinkProps } from '@inertiajs/react';
import { type ClassValue, clsx } from 'clsx';
import { twMerge } from 'tailwind-merge';

export function cn(...inputs: ClassValue[]) {
    return twMerge(clsx(inputs));
}

export function toUrl(url: NonNullable<InertiaLinkProps['href']>): string {
    return typeof url === 'string' ? url : url.url;
}

export function formatCurrency(value: string | number): string {
    const formatted = new Intl.NumberFormat('id-ID', {
        style: 'decimal',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
    }).format(Number(value));
    return `Rp ${formatted}`;
}

export function formatDate(dateString: string): string {
    return new Date(dateString).toLocaleDateString('id-ID', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
    });
}

export const formatQuantity = (quantity: number | string): string => {
    const num = Number(quantity);
    if (isNaN(num)) return '0';

    return num.toLocaleString('id-ID', {
        maximumFractionDigits: num % 1 === 0 ? 0 : 2, // 0 desimal jika bulat, 2 jika pecah
    });
};

export function formatDateTime(dateString: string): string {
    const date = new Date(dateString);
    if (isNaN(date.getTime())) return '-';
    const day = date.toLocaleDateString('id-ID', { day: '2-digit', month: '2-digit', year: 'numeric' });
    const time = date.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
    return `${day} ${time}`;
}

export function calculateMargin(costPrice: string | number, sellingPrice: string | number): string {
    const cost = Number(costPrice);
    const selling = Number(sellingPrice);
    const margin = ((selling - cost) / cost) * 100;
    return margin.toFixed(1) + '%';
}

export function generateBatchNumber(): string {
    const timestamp = Date.now().toString().slice(-8);
    const random = Math.random().toString(36).substring(2, 6).toUpperCase();
    return `BATCH-${timestamp}-${random}`;
}

export const formatIDR = (amount: number | string | null) => {
    if (amount === null || amount === '') return '-';

    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
    }).format(Number(amount));
};
