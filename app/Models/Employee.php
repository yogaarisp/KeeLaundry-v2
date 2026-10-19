<?php

namespace App\Models;

use App\Models\Concerns\BelongsToBranch;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employee extends Model
{
    use BelongsToBranch, SoftDeletes;

    protected $fillable = [
        'branch_id',
        'name',
        'phone',
        'role',
        'attendance_days',
        'base_salary',
        'daily_allowance',
        'commission_rate',
        'total_output_kg',
        'cash_advance',
        'is_active',
    ];

    protected $casts = [
        'attendance_days'  => 'integer',
        'base_salary'      => 'integer',
        'daily_allowance'  => 'integer',
        'commission_rate'  => 'integer',
        'total_output_kg'  => 'decimal:2',
        'cash_advance'     => 'integer',
        'is_active'        => 'boolean',
    ];

    // ─── Relasi ────────────────────────────────────────────────────

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    /** Order yang dicuci oleh karyawan ini */
    public function washedOrders(): HasMany
    {
        return $this->hasMany(Order::class, 'washed_by_id');
    }

    /** Order yang disetrika oleh karyawan ini */
    public function ironedOrders(): HasMany
    {
        return $this->hasMany(Order::class, 'ironed_by_id');
    }

    /** Order yang di-packing oleh karyawan ini */
    public function packedOrders(): HasMany
    {
        return $this->hasMany(Order::class, 'packed_by_id');
    }

    /** Tugas antar-jemput kurir ini */
    public function courierTasks(): HasMany
    {
        return $this->hasMany(CourierTask::class, 'courier_id');
    }

    // ─── Helpers: Kalkulasi Take-Home Pay ──────────────────────────

    /**
     * Total komisi berdasarkan output Kg yang sudah diklaim.
     */
    public function getTotalCommissionAttribute(): int
    {
        return (int) round((float) $this->total_output_kg * $this->commission_rate);
    }

    /**
     * Total uang makan berdasarkan jumlah hari hadir.
     */
    public function getTotalAllowanceAttribute(): int
    {
        return $this->attendance_days * $this->daily_allowance;
    }

    /**
     * Take-home pay = Gaji Pokok + Uang Makan + Komisi - Kasbon.
     */
    public function getTakeHomePayAttribute(): int
    {
        return $this->base_salary
             + $this->total_allowance
             + $this->total_commission
             - $this->cash_advance;
    }
}
