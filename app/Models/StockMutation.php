<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StockMutation extends Model
{
    /** @use HasFactory<\Database\Factories\StockMutationFactory> */
    use HasFactory;

    protected $fillable = [
        'code',
        'from_warehouse',
        'to_warehouse',
        'product_id',
        'quantity',
        'received_qty',
        'damaged_qty',
        'status',
        'sent_at',
        'received_at',
        'created_by',
        'received_by',
        'notes',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'received_qty' => 'decimal:2',
        'damaged_qty' => 'decimal:2',
        'sent_at' => 'datetime',
        'received_at' => 'datetime',
    ];

    public function fromWarehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class, 'from_warehouse');
    }

    public function toWarehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class, 'to_warehouse');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    public function stockHistories(): HasMany
    {
        return $this->hasMany(StockHistory::class, 'reference_id')
            ->where('reference_type', 'stock_mutation');
    }

    public function scopeByFromWarehouse($query, $warehouseId)
    {
        return $query->where('from_warehouse', $warehouseId);
    }

    public function scopeByToWarehouse($query, $warehouseId)
    {
        return $query->where('to_warehouse', $warehouseId);
    }

    public function scopeByProduct($query, $productId)
    {
        return $query->where('product_id', $productId);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopePending($query)
    {
        return $query->whereIn('status', ['dikirim', 'diterima']);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'selesai');
    }
}
