export function formatCurrency(amount: number): string {
    const isWhole = Math.round(amount) === amount;
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: isWhole ? 0 : 2,
        maximumFractionDigits: 2,
    }).format(amount);
}

//cara pakai: formatCurrency(1500000) => "Rp 1.500.000"
