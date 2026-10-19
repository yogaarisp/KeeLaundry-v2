<?php

namespace App\Models;

use App\Models\Concerns\BelongsToBranch;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UtilityLog extends Model
{
    use BelongsToBranch;

    protected $fillable = [
        'branch_id',
        'utility_type',
        'period_date',
        'meter_start',
        'meter_end',
        'usage_volume',
        'unit',
        'total_cost',
        'recorded_by',
        'notes',
    ];

    protected $casts = [
        'period_date'  => 'date',
        'meter_start'  => 'decimal:2',
        'meter_end'    => 'decimal:2',
        'usage_volume' => 'decimal:2',
        'total_cost'   => 'integer',
    ];

    // ─── Relasi ────────────────────────────────────────────────────

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    // ─── Helpers ───────────────────────────────────────────────────

    /**
     * Biaya per satuan volume (misal Rp/kWh, Rp/m3, Rp/Tabung).
     */
    public function getCostPerUnitAttribute(): float
    {
        if ((float) $this->usage_volume <= 0) {
            return 0.0;
        }

        return round($this->total_cost / (float) $this->usage_volume, 2);
    }
}
