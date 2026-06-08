<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OutboundTransaction extends Model
{
    /** @use HasFactory<\Database\Factories\OutboundTransactionFactory> */
    use HasFactory;

    protected $fillable = [
        'code',
        'customer_id',
        'warehouse_id',
        'product_id',
        'quantity',
        'unit_price',
        'sale_date',
        'notes',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:2',
            'unit_price' => 'decimal:2',
            'total_price' => 'decimal:2',
            'sale_date' => 'datetime',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function stockHistories(): HasMany
    {
        return $this->hasMany(StockHistory::class, 'reference_id')
            ->where('reference_type', 'outbound');
    }

    public function scopeByCustomer(Builder $query, int $customerId): Builder
    {
        return $query->where('customer_id', $customerId);
    }

    public function scopeByWarehouse(Builder $query, int $warehouseId): Builder
    {
        return $query->where('warehouse_id', $warehouseId);
    }

    public function scopeByProduct(Builder $query, int $productId): Builder
    {
        return $query->where('product_id', $productId);
    }

    public function scopeByDateRange(Builder $query, string $startDate, string $endDate): Builder
    {
        return $query->whereBetween('sale_date', [$startDate, $endDate]);
    }
}
