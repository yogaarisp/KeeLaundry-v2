<?php

namespace App\Models;

use App\Models\Concerns\BelongsToBranch;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    use BelongsToBranch, SoftDeletes;

    protected $fillable = [
        'branch_id',
        'invoice_number',
        'customer_id',
        'service_package_id',
        'quantity',
        'unit',
        'perfume_variant',
        'include_wash',
        'include_iron',
        'notes',
        'status',
        'rack_location',
        'payment_status',
        'payment_method',
        'total_price',
        'washed_by_id',
        'ironed_by_id',
        'packed_by_id',
    ];

    protected $casts = [
        'quantity'     => 'decimal:2',
        'total_price'  => 'integer',
        'include_wash' => 'boolean',
        'include_iron' => 'boolean',
    ];

    // ─── Status Constants ──────────────────────────────────────────

    const STATUS_ANTREAN      = 'ANTREAN';
    const STATUS_CUCI         = 'CUCI';
    const STATUS_SETRIKA      = 'SETRIKA';
    const STATUS_PACKING      = 'PACKING';
    const STATUS_SIAP_DIAMBIL = 'SIAP_DIAMBIL';
    const STATUS_SELESAI      = 'SELESAI';
    const STATUS_VOID         = 'VOID';

    const PAYMENT_LUNAS       = 'LUNAS';
    const PAYMENT_BELUM_LUNAS = 'BELUM_LUNAS';
    const PAYMENT_REFUND      = 'REFUND';

    // ─── Relasi ────────────────────────────────────────────────────

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function servicePackage(): BelongsTo
    {
        return $this->belongsTo(ServicePackage::class);
    }

    public function washedBy(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'washed_by_id');
    }

    public function ironedBy(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'ironed_by_id');
    }

    public function packedBy(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'packed_by_id');
    }

    public function courierTasks(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(CourierTask::class);
    }

    // ─── Helpers ───────────────────────────────────────────────────

    /**
     * Tentukan status produksi berikutnya berdasarkan include_wash / include_iron.
     *
     * Contoh:
     *   - Cuci+Setrika : ANTREAN → CUCI → SETRIKA → PACKING → SIAP_DIAMBIL
     *   - Cuci saja    : ANTREAN → CUCI → PACKING → SIAP_DIAMBIL  (skip SETRIKA)
     *   - Setrika saja : ANTREAN → SETRIKA → PACKING → SIAP_DIAMBIL (skip CUCI)
     *
     * @return string|null Status berikutnya, atau null jika sudah di akhir
     */
    public function nextProductionStatus(): ?string
    {
        return match ($this->status) {
            self::STATUS_ANTREAN => $this->include_wash ? self::STATUS_CUCI : self::STATUS_SETRIKA,
            self::STATUS_CUCI    => $this->include_iron ? self::STATUS_SETRIKA : self::STATUS_PACKING,
            self::STATUS_SETRIKA => self::STATUS_PACKING,
            self::STATUS_PACKING => self::STATUS_SIAP_DIAMBIL,
            default              => null,
        };
    }

    /**
     * Label ringkas jenis layanan berdasarkan flag.
     */
    public function serviceSummaryLabel(): string
    {
        if ($this->include_wash && $this->include_iron) {
            return 'Cuci + Setrika';
        }
        if ($this->include_wash) {
            return 'Cuci Saja';
        }
        if ($this->include_iron) {
            return 'Setrika Saja';
        }
        return 'Custom';
    }

    /**
     * Cek apakah order ini sudah diklaim di tahap produksi mana pun.
     */
    public function getIsClaimedInProductionAttribute(): bool
    {
        return $this->washed_by_id || $this->ironed_by_id || $this->packed_by_id;
    }

    /**
     * Generate nomor invoice otomatis: INV-YYYYMMDD-XXXX
     */
    public static function generateInvoiceNumber(): string
    {
        $date  = now()->format('Ymd');
        $count = static::withoutGlobalScope('branch')
                        ->whereDate('created_at', today())
                        ->count() + 1;

        return 'INV-' . $date . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
    }
}
