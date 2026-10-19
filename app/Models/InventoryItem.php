<?php

namespace App\Models;

use App\Models\Concerns\BelongsToBranch;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InventoryItem extends Model
{
    use BelongsToBranch, SoftDeletes;

    protected $fillable = [
        'branch_id',
        'sku',
        'name',
        'category',
        'system_stock',
        'actual_stock',
        'unit',
        'min_threshold',
        'usage_per_kg',
        'cost_per_unit',
    ];

    protected $casts = [
        'system_stock'  => 'decimal:2',
        'actual_stock'  => 'decimal:2',
        'min_threshold' => 'decimal:2',
        'usage_per_kg'  => 'decimal:2',
        'cost_per_unit' => 'decimal:2',
    ];

    // ─── Relasi ────────────────────────────────────────────────────

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function stockMutations(): HasMany
    {
        return $this->hasMany(StockMutation::class);
    }

    // ─── Helpers ───────────────────────────────────────────────────

    /**
     * Apakah stok sistem di bawah ambang minimum (perlu restock).
     */
    public function getIsLowStockAttribute(): bool
    {
        return (float) $this->system_stock <= (float) $this->min_threshold;
    }

    /**
     * Selisih opname: actual_stock - system_stock.
     * Negatif = ada pemborosan/selisih kurang.
     */
    public function getVarianceAttribute(): float
    {
        return (float) $this->actual_stock - (float) $this->system_stock;
    }

    /**
     * Persentase sisa stok terhadap minimum threshold (untuk progress bar).
     * Return value 0–100 (lebih dari 100 berarti aman).
     */
    public function getStockPercentageAttribute(): float
    {
        if ((float) $this->min_threshold <= 0) {
            return 100.0;
        }

        return min(100.0, round(((float) $this->system_stock / ((float) $this->min_threshold * 3)) * 100, 1));
    }
}
