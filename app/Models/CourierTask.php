<?php

namespace App\Models;

use App\Models\Concerns\BelongsToBranch;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourierTask extends Model
{
    use BelongsToBranch, SoftDeletes;

    protected $fillable = [
        'branch_id',
        'customer_id',
        'order_id',
        'courier_id',
        'task_type',
        'status',
        'customer_name',
        'customer_phone',
        'address',
        'maps_url',
        'scheduled_date',
        'time_slot',
        'delivery_fee',
        'estimated_kg',
        'notes',
        'completed_at',
    ];

    protected $casts = [
        'scheduled_date' => 'date',
        'completed_at'   => 'datetime',
        'delivery_fee'   => 'integer',
        'estimated_kg'   => 'decimal:2',
    ];

    const TYPE_JEMPUT = 'JEMPUT';
    const TYPE_ANTAR  = 'ANTAR';

    const STATUS_MENUNGGU = 'MENUNGGU';
    const STATUS_DIPROSES = 'DIPROSES';
    const STATUS_SELESAI  = 'SELESAI';
    const STATUS_BATAL    = 'BATAL';

    // ─── Relasi ────────────────────────────────────────────────────

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function courier(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'courier_id');
    }

    // ─── Scopes ────────────────────────────────────────────────────

    public function scopeJemput($query)
    {
        return $query->where('task_type', self::TYPE_JEMPUT);
    }

    public function scopeAntar($query)
    {
        return $query->where('task_type', self::TYPE_ANTAR);
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', [self::STATUS_MENUNGGU, self::STATUS_DIPROSES]);
    }
}
