<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── 1. Tabel Master Cabang / Toko ──────────────────────────────
        Schema::create('branches', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();           // KEE-MAIN-01
            $table->string('name');                     // Kee Laundry - Pusat
            $table->string('phone');
            $table->text('address');
            $table->string('receipt_footer')->nullable();
            $table->integer('target_hpp_per_kg')->default(1850);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // ── 2. Tabel Pelanggan (CRM) ────────────────────────────────────
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

            $table->index('branch_id');
            $table->index('last_order_date');
        });

        // ── 3. Tabel Master Layanan & Resep BOM ─────────────────────────
        Schema::create('service_packages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->default(1)->constrained('branches');
            $table->string('name');
            $table->enum('category', ['KILOAN', 'SATUAN', 'EXPRESS']);
            $table->integer('price_per_unit');
            $table->string('unit')->default('Kg');      // Kg, Pcs, Meter
            $table->integer('estimated_hours')->default(48);
            $table->decimal('detergent_ml_per_unit', 8, 2)->default(15.00);
            $table->decimal('softener_ml_per_unit', 8, 2)->default(10.00);
            $table->decimal('perfume_ml_per_unit', 8, 2)->default(5.00);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index('branch_id');
        });

        // ── 4. Tabel Karyawan (HR, Absensi & Komisi) ────────────────────
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->default(1)->constrained('branches');
            $table->string('name');
            $table->string('phone');
            $table->enum('role', ['KASIR', 'OPERATOR_CUCI', 'SETRIKA', 'KURIR']);
            $table->integer('attendance_days')->default(25);
            $table->integer('base_salary');
            $table->integer('daily_allowance')->default(25000);
            $table->integer('commission_rate');         // Rp per Kg atau per Trip
            $table->decimal('total_output_kg', 10, 2)->default(0);
            $table->integer('cash_advance')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index('branch_id');
            $table->index('role');
        });

        // ── 5. Tabel Transaksi / Order Cucian ───────────────────────────
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

            $table->index('branch_id');
            $table->index('status');
            $table->index('payment_status');
            $table->index('created_at');
        });

        // ── 6. Tabel Inventaris Bahan Baku ──────────────────────────────
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

            $table->index('branch_id');
            $table->index('category');
        });

        // ── 7. Tabel Mutasi Stok ────────────────────────────────────────
        Schema::create('stock_mutations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->default(1)->constrained('branches');
            $table->foreignId('inventory_item_id')->constrained()->cascadeOnDelete();
            $table->enum('mutation_type', [
                'RESTOCK_IN', 'AUTO_DEDUCT_ORDER', 'VOID_ROLLBACK', 'OPNAME_ADJUSTMENT'
            ]);
            $table->decimal('amount', 10, 2);           // Positif atau Negatif
            $table->string('reference_invoice')->nullable();
            $table->string('notes')->nullable();
            $table->timestamps();

            $table->index('branch_id');
            $table->index('inventory_item_id');
            $table->index('reference_invoice');
        });

        // ── 8. Tabel Log Utilitas (Listrik, Air, Gas) ────────────────────
        Schema::create('utility_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->default(1)->constrained('branches');
            $table->enum('utility_type', ['LISTRIK_PLN', 'AIR_PDAM', 'GAS_LPG']);
            $table->date('period_date');
            $table->decimal('meter_start', 10, 2)->default(0);
            $table->decimal('meter_end', 10, 2)->default(0);
            $table->decimal('usage_volume', 10, 2);
            $table->string('unit');                     // kWh, m3, Tabung
            $table->integer('total_cost');
            $table->string('recorded_by')->default('Admin');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('branch_id');
            $table->index('utility_type');
            $table->index('period_date');
        });

        // ── 9. Tabel Komplain Pelanggan ─────────────────────────────────
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

            $table->index('branch_id');
            $table->index('status');
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
