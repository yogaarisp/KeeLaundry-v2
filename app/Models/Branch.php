<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Branch extends Model
{
    protected $fillable = [
        'code',
        'name',
        'phone',
        'address',
        'operational_hours',
        'receipt_footer',
        'printer_type',
        'printer_paper_size',
        'printer_auto_print',
        'printer_device_name',
        'wa_template_ready',
        'wa_template_done',
        'wa_template_promo',
        'target_hpp_per_kg',
        'is_active',
    ];

    protected $casts = [
        'is_active'          => 'boolean',
        'printer_auto_print' => 'boolean',
        'target_hpp_per_kg'  => 'integer',
    ];

    // ─── Relasi ────────────────────────────────────────────────────

    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class);
    }

    public function servicePackages(): HasMany
    {
        return $this->hasMany(ServicePackage::class);
    }

    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function inventoryItems(): HasMany
    {
        return $this->hasMany(InventoryItem::class);
    }

    public function stockMutations(): HasMany
    {
        return $this->hasMany(StockMutation::class);
    }

    public function utilityLogs(): HasMany
    {
        return $this->hasMany(UtilityLog::class);
    }

    public function complaints(): HasMany
    {
        return $this->hasMany(Complaint::class);
    }

    public function courierTasks(): HasMany
    {
        return $this->hasMany(CourierTask::class);
    }
}
