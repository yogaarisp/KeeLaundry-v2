<?php

namespace App\Models;

use App\Models\Concerns\BelongsToBranch;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ServicePackage extends Model
{
    use BelongsToBranch, SoftDeletes;

    protected $fillable = [
        'branch_id',
        'name',
        'category',
        'price_per_unit',
        'unit',
        'estimated_hours',
        'detergent_ml_per_unit',
        'softener_ml_per_unit',
        'perfume_ml_per_unit',
        'is_active',
    ];

    protected $casts = [
        'price_per_unit'        => 'integer',
        'estimated_hours'       => 'integer',
        'detergent_ml_per_unit' => 'decimal:2',
        'softener_ml_per_unit'  => 'decimal:2',
        'perfume_ml_per_unit'   => 'decimal:2',
        'is_active'             => 'boolean',
    ];

    // ─── Relasi ────────────────────────────────────────────────────

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    // ─── Helpers ───────────────────────────────────────────────────

    /**
     * Hitung total biaya bahan baku per Kg berdasarkan BOM.
     * Membutuhkan data harga per ml dari InventoryItem.
     */
    public function estimateBomCostPerKg(float $detergentCostPerMl, float $softenerCostPerMl, float $perfumeCostPerMl): float
    {
        return (float) $this->detergent_ml_per_unit * $detergentCostPerMl
             + (float) $this->softener_ml_per_unit  * $softenerCostPerMl
             + (float) $this->perfume_ml_per_unit   * $perfumeCostPerMl;
    }
}
