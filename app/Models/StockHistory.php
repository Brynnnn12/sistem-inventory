<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockHistory extends Model
{
    /** @use HasFactory<\Database\Factories\StockHistoryFactory> */
    use HasFactory;

    protected $fillable = [
        'stock_id',
        'warehouse_id',
        'product_id',
        'previous_qty',
        'new_qty',
        'change_qty',
        'reference_type',
        'reference_id',
        'reference_code',
        'notes',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'stock_id' => 'integer',
            'warehouse_id' => 'integer',
            'product_id' => 'integer',
            'reference_id' => 'integer',
            'created_by' => 'integer',
            'previous_qty' => 'decimal:2',
            'new_qty' => 'decimal:2',
            'change_qty' => 'decimal:2',
        ];
    }

    public function stock(): BelongsTo
    {
        return $this->belongsTo(Stock::class);
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

    public function scopeByStock(Builder $query, int $stockId): Builder
    {
        return $query->where('stock_id', $stockId);
    }

    public function scopeByWarehouse(Builder $query, int $warehouseId): Builder
    {
        return $query->where('warehouse_id', $warehouseId);
    }

    public function scopeByProduct(Builder $query, int $productId): Builder
    {
        return $query->where('product_id', $productId);
    }

    public function scopeByReference(Builder $query, string $type, int $id): Builder
    {
        return $query->where('reference_type', $type)->where('reference_id', $id);
    }

    public function scopeByDateRange(Builder $query, string $startDate, string $endDate): Builder
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    public function scopeByReferenceType(Builder $query, string $type): Builder
    {
        return $query->where('reference_type', $type);
    }
}
