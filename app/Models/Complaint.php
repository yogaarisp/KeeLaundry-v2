<?php

namespace App\Models;

use App\Models\Concerns\BelongsToBranch;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Complaint extends Model
{
    use BelongsToBranch;

    protected $fillable = [
        'branch_id',
        'invoice_number',
        'customer_id',
        'issue_type',
        'description',
        'status',
        'compensation',
    ];

    // ─── Relasi ────────────────────────────────────────────────────

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    // ─── Helpers ───────────────────────────────────────────────────

    public function getIsOpenAttribute(): bool
    {
        return $this->status === 'OPEN';
    }

    public function getIsResolvedAttribute(): bool
    {
        return $this->status === 'RESOLVED';
    }
}
