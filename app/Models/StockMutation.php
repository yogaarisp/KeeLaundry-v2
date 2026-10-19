<?php

namespace App\Models;

use App\Models\Concerns\BelongsToBranch;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockMutation extends Model
{
    use BelongsToBranch;

    protected $fillable = [
        'branch_id',
        'inventory_item_id',
        'mutation_type',
        'amount',
        'reference_invoice',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    // ─── Mutation Type Constants ───────────────────────────────────
    const MUTATION_RESTOCK    = 'RESTOCK_IN';
    const MUTATION_DEDUCT     = 'AUTO_DEDUCT_ORDER';
    const MUTATION_ROLLBACK   = 'VOID_ROLLBACK';
    const MUTATION_OPNAME     = 'OPNAME_ADJUSTMENT';

    // ─── Relasi ────────────────────────────────────────────────────

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function inventoryItem(): BelongsTo
    {
        return $this->belongsTo(InventoryItem::class);
    }
}
