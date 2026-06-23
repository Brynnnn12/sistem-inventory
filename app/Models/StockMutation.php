<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
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

    protected $appends = ['status_display'];

    protected function casts(): array
    {
        return [
            'from_warehouse' => 'integer',
            'to_warehouse' => 'integer',
            'product_id' => 'integer',
            'created_by' => 'integer',
            'received_by' => 'integer',
            'quantity' => 'decimal:2',
            'received_qty' => 'decimal:2',
            'damaged_qty' => 'decimal:2',
            'sent_at' => 'datetime',
            'received_at' => 'datetime',
        ];
    }

    public const STATUS_MAPPING = [
        'dikirim' => 'sent',
        'diterima' => 'received',
        'ditolak' => 'rejected',
        'selesai' => 'completed',
    ];

    public const STATUS_REVERSE_MAPPING = [
        'sent' => 'dikirim',
        'received' => 'diterima',
        'rejected' => 'ditolak',
        'completed' => 'selesai',
    ];

    public function getStatusDisplayAttribute(): string
    {
        return self::STATUS_MAPPING[$this->status] ?? $this->status;
    }

    public function setStatusAttribute(string $value): void
    {
        if (array_key_exists($value, self::STATUS_REVERSE_MAPPING)) {
            $this->attributes['status'] = self::STATUS_REVERSE_MAPPING[$value];
        } else {
            $this->attributes['status'] = $value;
        }
    }

    public function fromWarehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class, 'from_warehouse');
    }

    public function toWarehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class, 'to_warehouse');
    }

    public function toArray()
    {
        $array = parent::toArray();

        if ($this->relationLoaded('fromWarehouse')) {
            $array['from_warehouse'] = $this->fromWarehouse;
        }
        if ($this->relationLoaded('toWarehouse')) {
            $array['to_warehouse'] = $this->toWarehouse;
        }

        return $array;
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
            ->whereIn('reference_type', ['mutation_sent', 'mutation_received', 'mutation_rejected']);
    }

    public function scopeByFromWarehouse(Builder $query, int $warehouseId): Builder
    {
        return $query->where('from_warehouse', $warehouseId);
    }

    public function scopeByToWarehouse(Builder $query, int $warehouseId): Builder
    {
        return $query->where('to_warehouse', $warehouseId);
    }

    public function scopeByProduct(Builder $query, int $productId): Builder
    {
        return $query->where('product_id', $productId);
    }

    public function scopeByStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->whereIn('status', ['dikirim', 'diterima']);
    }

    public function scopeCompleted(Builder $query): Builder
    {
        return $query->where('status', 'selesai');
    }
}
