# System Architecture & Technical Specification: Kee Laundry (Laravel Full CRUD & Multi-Branch Ready)

## 1. Tech Stack & Arsitektur Dasar
- **Backend Framework:** Laravel 11 / 12 (PHP 8.2+)
- **Reactive Fullstack UI:** Laravel Livewire 3 (`wire:navigate` SPA Mode) + Alpine.js
- **Styling & Mobile UI:** Tailwind CSS + Blade Responsive Components + Lucide Icons
- **Database:** MySQL 8.0+ (Eloquent ORM dengan `SoftDeletes` & `DB::transaction`)
- **Multi-Branch Strategy:** Single-Database Multi-Tenancy menggunakan kolom `branch_id` pada setiap tabel operasional. Untuk tahap awal (1 Toko), sistem menggunakan *Default Active Branch* (`branch_id = 1`), namun sudah siap ditambahkan cabang baru kapan saja.

---

## 2. Struktur Direktori Project (Modular Full CRUD)

```text
kee-laundry/
├── app/
│   ├── Models/
│   │   ├── Concerns/
│   │   │   └── BelongsToBranch.php       # Trait otomatis filter & isi branch_id
│   │   ├── Branch.php                    # Master Toko / Cabang
│   │   ├── Customer.php                  # CRM Pelanggan (SoftDeletes)
│   │   ├── ServicePackage.php            # Katalog Layanan & Resep BOM (SoftDeletes)
│   │   ├── Order.php                     # Transaksi POS & Produksi (SoftDeletes)
│   │   ├── Employee.php                  # HR & Komisi Karyawan (SoftDeletes)
│   │   ├── InventoryItem.php             # Stok Sabun, Parfum, Plastik, Gas
│   │   ├── StockMutation.php             # Log Restock, Auto-Deduct & Opname
│   │   ├── UtilityLog.php                # Log Meteran Listrik, Air, Gas
│   │   └── Complaint.php                 # Tiket Komplain CRM
│   ├── Livewire/
│   │   ├── Dashboard/
│   │   │   └── ExecutiveDashboard.php    # KPI, Grafik & Kalkulator HPP/Kg
│   │   ├── Pos/
│   │   │   ├── CashierPos.php            # Create Order, Quick Create Customer
│   │   │   └── OrderHistoryCrud.php      # Read, Update Pelunasan/Berat, Void/Delete Order
│   │   ├── Production/
│   │   │   └── KanbanBoard.php           # Update Status, Assign PIC, Assign Smart Rack
│   │   ├── Crm/
│   │   │   └── CustomerCrud.php          # Full CRUD Pelanggan, Top-Up Deposit, CRUD Komplain
│   │   ├── Hr/
│   │   │   └── EmployeePayrollCrud.php   # Full CRUD Staf, Absensi, Kasbon & Slip Gaji
│   │   ├── Inventory/
│   │   │   └── InventoryUtilityCrud.php  # Full CRUD Sabun, Restock, Opname & CRUD Listrik/Air
│   │   ├── Settings/
│   │   │   └── BranchServiceCrud.php     # Full CRUD Profil Toko & Master Paket Layanan
│   │   └── Public/
│   │       └── MobileOrderTracker.php    # Halaman Tracking Pelanggan
│   └── Services/
│       ├── OrderTransactionService.php   # Create, Update, & Void Order (Auto-Rollback Sabun)
│       ├── CommissionService.php         # Hitung & koreksi komisi Kg staf otomatis
│       └── HppCalculatorService.php      # Kalkulasi HPP (Listrik+Air+Gas+Sabun / Total Kg)
└── database/
    ├── migrations/
    │   └── 2026_01_01_000001_create_kee_laundry_v2_schema.php
    └── seeders/
        └── KeeLaundrySeeder.php
```

---

## 3. Skema Database Lengkap (Multi-Branch Ready + SoftDeletes)

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tabel Master Cabang / Toko (Fokus 1 Toko Utama dulu, siap tambah cabang)
        Schema::create('branches', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // KEE-MAIN-01
            $table->string('name');           // Kee Laundry - Cabang Utama
            $table->string('phone');
            $table->text('address');
            $table->string('receipt_footer')->nullable();
            $table->integer('target_hpp_per_kg')->default(1850);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 2. Tabel Pelanggan (CRM)
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->default(1)->constrained('branches');
            $table->string('name');
            $table->string('phone')->unique();
            $table->text('address')->nullable();
            $table->enum('tier', ['REGULAR', 'SILVER', 'VIP'])->default('REGULAR');
            $table->decimal('deposit_kg', 8, 2)->default(0);
            $table->integer('loyalty_points')->default(0);
            $table->text('preferences')->nullable();
            $table->timestamp('last_order_date')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // 3. Tabel Master Layanan & Resep BOM Sabun
        Schema::create('service_packages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->default(1)->constrained('branches');
            $table->string('name');
            $table->enum('category', ['KILOAN', 'SATUAN', 'EXPRESS']);
            $table->integer('price_per_unit');
            $table->string('unit')->default('Kg'); // Kg, Pcs, Meter
            $table->integer('estimated_hours')->default(48);
            $table->decimal('detergent_ml_per_unit', 8, 2)->default(15.00);
            $table->decimal('softener_ml_per_unit', 8, 2)->default(10.00);
            $table->decimal('perfume_ml_per_unit', 8, 2)->default(5.00);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        // 4. Tabel Karyawan (HR, Absensi & Komisi)
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->default(1)->constrained('branches');
            $table->string('name');
            $table->string('phone');
            $table->enum('role', ['KASIR', 'OPERATOR_CUCI', 'SETRIKA', 'KURIR']);
            $table->integer('attendance_days')->default(25);
            $table->integer('base_salary');
            $table->integer('daily_allowance')->default(25000);
            $table->integer('commission_rate'); // Rp per Kg atau per Trip
            $table->decimal('total_output_kg', 10, 2)->default(0);
            $table->integer('cash_advance')->default(0); // Kasbon
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        // 5. Tabel Transaksi / Order Cucian
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->default(1)->constrained('branches');
            $table->string('invoice_number')->unique();
            $table->foreignId('customer_id')->constrained();
            $table->foreignId('service_package_id')->constrained();
            $table->decimal('quantity', 8, 2);
            $table->string('unit')->default('Kg');
            $table->string('perfume_variant')->default('Sakura');
            $table->text('notes')->nullable();
            $table->enum('status', [
                'ANTREAN', 'CUCI', 'SETRIKA', 'PACKING', 'SIAP_DIAMBIL', 'SELESAI', 'VOID'
            ])->default('ANTREAN');
            $table->string('rack_location')->nullable();
            $table->enum('payment_status', ['LUNAS', 'BELUM_LUNAS', 'REFUND'])->default('BELUM_LUNAS');
            $table->enum('payment_method', ['TUNAI', 'QRIS', 'TRANSFER', 'DEPOSIT_KG'])->default('TUNAI');
            $table->integer('total_price');
            $table->foreignId('washed_by_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->foreignId('ironed_by_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->foreignId('packed_by_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        // 6. Tabel Inventaris Bahan Baku (Sabun, Parfum, Plastik, Gas)
        Schema::create('inventory_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->default(1)->constrained('branches');
            $table->string('sku')->unique();
            $table->string('name');
            $table->enum('category', ['CHEMICAL', 'PARFUM', 'PACKAGING', 'GAS']);
            $table->decimal('system_stock', 10, 2);
            $table->decimal('actual_stock', 10, 2);
            $table->string('unit')->default('ml');
            $table->decimal('min_threshold', 10, 2);
            $table->decimal('usage_per_kg', 8, 2)->default(0);
            $table->decimal('cost_per_unit', 10, 2);
            $table->timestamps();
            $table->softDeletes();
        });

        // 7. Tabel Mutasi Stok (Log Restock, Auto-Deduct Order, Void Rollback, & Opname)
        Schema::create('stock_mutations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->default(1)->constrained('branches');
            $table->foreignId('inventory_item_id')->constrained()->cascadeOnDelete();
            $table->enum('mutation_type', ['RESTOCK_IN', 'AUTO_DEDUCT_ORDER', 'VOID_ROLLBACK', 'OPNAME_ADJUSTMENT']);
            $table->decimal('amount', 10, 2); // Positif atau Negatif
            $table->string('reference_invoice')->nullable();
            $table->string('notes')->nullable();
            $table->timestamps();
        });

        // 8. Tabel Log Utilitas (Listrik PLN, Air PDAM, Gas LPG)
        Schema::create('utility_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->default(1)->constrained('branches');
            $table->enum('utility_type', ['LISTRIK_PLN', 'AIR_PDAM', 'GAS_LPG']);
            $table->date('period_date');
            $table->decimal('meter_start', 10, 2)->default(0);
            $table->decimal('meter_end', 10, 2)->default(0);
            $table->decimal('usage_volume', 10, 2);
            $table->string('unit'); // kWh, m3, Tabung
            $table->integer('total_cost');
            $table->string('recorded_by')->default('Admin');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // 9. Tabel Komplain Pelanggan
        Schema::create('complaints', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->default(1)->constrained('branches');
            $table->string('invoice_number');
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->string('issue_type');
            $table->text('description');
            $table->enum('status', ['OPEN', 'IN_PROGRESS', 'RESOLVED'])->default('OPEN');
            $table->string('compensation')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('complaints');
        Schema::dropIfExists('utility_logs');
        Schema::dropIfExists('stock_mutations');
        Schema::dropIfExists('inventory_items');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('employees');
        Schema::dropIfExists('service_packages');
        Schema::dropIfExists('customers');
        Schema::dropIfExists('branches');
    }
};
```

---

## 4. Arsitektur Multi-Branch Scope & CRUD Integrity

### A. Trait `BelongsToBranch.php` (Siap Multi-Cabang Tanpa Ubah Query)
Seluruh model utama menggunakan trait ini. Saat ini secara default mengarah ke `branch_id = 1` (Toko Utama), namun saat ditambahkan dropdown cabang baru, seluruh data otomatis terfilter per cabang:

```php
namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;

trait BelongsToBranch
{
    protected static function bootBelongsToBranch(): void
    {
        static::creating(function ($model) {
            $model->branch_id = session('active_branch_id', 1);
        });

        static::addGlobalScope('branch', function (Builder $builder) {
            $builder->where('branch_id', session('active_branch_id', 1));
        });
    }
}
```

### B. `OrderTransactionService.php` (Create, Update Berat, & Delete/Void dengan Auto-Rollback)
Menjamin keakuratan stok sabun dan deposit pelanggan saat terjadi perubahan atau pembatalan nota:

1. **Create Order:** Memotong `system_stock` deterjen, softener, dan parfum sesuai rumus $\text{Berat} \times \text{Takaran per Unit}$, serta mencatatnya ke `stock_mutations`.
2. **Update Order (Revisi Berat):** Jika berat cucian diedit dari $W_{\text{old}}$ ke $W_{\text{new}}$, sistem menghitung selisih $\Delta W = W_{\text{new}} - W_{\text{old}}$ lalu menyesuaikan stok sabun dan tagihan secara presisi.
3. **Delete / Void Order (Auto-Rollback):**
   Saat nota dihapus/dibatalkan:
   - Mengembalikan `system_stock` deterjen, softener, dan parfum sebesar jumlah yang tadinya terpotong.
   - Mengembalikan saldo `deposit_kg` pelanggan jika metode bayarnya menggunakan deposit.
   - Mengurangi `total_output_kg` karyawan jika order tersebut sudah sempat diklaim di Kanban.