export function formatCurrency(amount: number): string {
    // if the amount is a whole number, don't show decimal places
    const isWhole = Math.round(amount) === amount;
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: isWhole ? 0 : 2,
        maximumFractionDigits: 2,
    }).format(amount);
}
