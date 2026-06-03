function toNumber(value: number | string): number {
    return typeof value === 'string' ? parseFloat(value) : value;
}

export function formatCurrency(amount: number | string): string {
    const num = toNumber(amount);
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: Number.isInteger(num) ? 0 : 2,
        maximumFractionDigits: 2,
    }).format(num);
}

export function formatQuantity(qty: number | string): string {
    const num = toNumber(qty);
    return Number.isInteger(num) ? num.toString() : num.toFixed(2);
}
