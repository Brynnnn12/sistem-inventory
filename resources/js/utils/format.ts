function toNumber(value: number | string): number {
    return typeof value === 'string' ? parseFloat(value) : value;
}

export function formatCurrency(amount: number | string): string {
    const num = toNumber(amount);
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
    }).format(num);
}

export function formatQuantity(qty: number | string): string {
    const num = toNumber(qty);
    if (isNaN(num)) return '0';
    return new Intl.NumberFormat('id-ID', {
        maximumFractionDigits: num % 1 === 0 ? 0 : 2,
    }).format(num);
}
