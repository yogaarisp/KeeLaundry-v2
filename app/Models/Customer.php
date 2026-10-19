<?php

namespace App\Models;

use App\Models\Concerns\BelongsToBranch;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    use BelongsToBranch, SoftDeletes;

    protected $fillable = [
        'branch_id',
        'name',
        'phone',
        'address',
        'maps_url',
        'tier',
        'deposit_kg',
        'loyalty_points',
        'preferences',
        'last_order_date',
    ];

    protected $casts = [
        'deposit_kg'      => 'decimal:2',
        'loyalty_points'  => 'integer',
        'last_order_date' => 'datetime',
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

    public function complaints(): HasMany
    {
        return $this->hasMany(Complaint::class);
    }

    public function courierTasks(): HasMany
    {
        return $this->hasMany(CourierTask::class);
    }

    // ─── Helpers ───────────────────────────────────────────────────

    /**
     * Hitung jumlah hari sejak order terakhir (untuk Churn Alert > 14 hari).
     */
    public function getDaysSinceLastOrderAttribute(): ?int
    {
        return $this->last_order_date
            ? (int) floor($this->last_order_date->diffInDays(now()))
            : null;
    }

    /**
     * Teks ramah untuk tampilan terakhir order (misal: "Hari ini", "Kemarin", "3 hari lalu").
     */
    public function getLastOrderHumanAttribute(): string
    {
        if (! $this->last_order_date) {
            return 'Belum pernah';
        }

        $days = (int) floor($this->last_order_date->diffInDays(now()));

        if ($days <= 0) {
            return 'Hari ini';
        }

        if ($days === 1) {
            return 'Kemarin';
        }

        return "{$days} hari lalu";
    }

    /**
     * Apakah pelanggan ini termasuk churn (> 14 hari tidak order).
     */
    public function getIsChurnAttribute(): bool
    {
        return $this->last_order_date === null
            || (int) floor($this->last_order_date->diffInDays(now())) > 14;
    }
}
