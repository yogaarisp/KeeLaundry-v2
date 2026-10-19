<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Complaint;
use App\Models\Customer;
use App\Models\Employee;
use App\Models\InventoryItem;
use App\Models\Order;
use App\Models\ServicePackage;
use App\Models\StockMutation;
use App\Models\UtilityLog;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class KeeLaundrySeeder extends Seeder
{
    public function run(): void
    {
        // Nonaktifkan global scope BelongsToBranch saat seeding
        // karena session belum ada di CLI
        $this->seedBranch();
        $this->seedServicePackages();
        $this->seedInventoryItems();
        $this->seedEmployees();
        $this->seedCustomers();
        $this->seedOrders();
        $this->seedUtilityLogs();
        $this->seedComplaints();
    }

    // ─── 1. Branch ─────────────────────────────────────────────────

    private function seedBranch(): void
    {
        Branch::create([
            'id'               => 1,
            'code'             => 'KEE-MAIN-01',
            'name'             => 'Kee Laundry - Pusat',
            'phone'            => '081234567890',
            'address'          => 'Jl. Melati No. 12, Kel. Sukorejo, Kec. Gunungpati, Semarang 50228',
            'receipt_footer'   => 'Terima kasih telah mempercayakan cucian Anda. Komplain maks. 24 jam setelah diambil. WA: 081234567890',
            'target_hpp_per_kg'=> 1850,
            'is_active'        => true,
        ]);
    }

    // ─── 2. Service Packages ────────────────────────────────────────

    private function seedServicePackages(): void
    {
        $packages = [
            [
                'name'                  => 'Cuci Kiloan Reguler',
                'category'              => 'KILOAN',
                'price_per_unit'        => 7000,
                'unit'                  => 'Kg',
                'estimated_hours'       => 48,
                'detergent_ml_per_unit' => 15.00,
                'softener_ml_per_unit'  => 10.00,
                'perfume_ml_per_unit'   => 5.00,
            ],
            [
                'name'                  => 'Cuci Express (8 Jam)',
                'category'              => 'EXPRESS',
                'price_per_unit'        => 12000,
                'unit'                  => 'Kg',
                'estimated_hours'       => 8,
                'detergent_ml_per_unit' => 18.00,
                'softener_ml_per_unit'  => 12.00,
                'perfume_ml_per_unit'   => 7.00,
            ],
            [
                'name'                  => 'Cuci + Setrika Kiloan',
                'category'              => 'KILOAN',
                'price_per_unit'        => 10000,
                'unit'                  => 'Kg',
                'estimated_hours'       => 72,
                'detergent_ml_per_unit' => 15.00,
                'softener_ml_per_unit'  => 10.00,
                'perfume_ml_per_unit'   => 5.00,
            ],
            [
                'name'                  => 'Cuci Selimut / Bed Cover',
                'category'              => 'SATUAN',
                'price_per_unit'        => 30000,
                'unit'                  => 'Pcs',
                'estimated_hours'       => 48,
                'detergent_ml_per_unit' => 50.00,
                'softener_ml_per_unit'  => 30.00,
                'perfume_ml_per_unit'   => 15.00,
            ],
            [
                'name'                  => 'Cuci Jas / Blazer (Satuan)',
                'category'              => 'SATUAN',
                'price_per_unit'        => 25000,
                'unit'                  => 'Pcs',
                'estimated_hours'       => 72,
                'detergent_ml_per_unit' => 30.00,
                'softener_ml_per_unit'  => 20.00,
                'perfume_ml_per_unit'   => 10.00,
            ],
            [
                'name'                  => 'Cuci Karpet (per Meter)',
                'category'              => 'SATUAN',
                'price_per_unit'        => 15000,
                'unit'                  => 'Meter',
                'estimated_hours'       => 96,
                'detergent_ml_per_unit' => 40.00,
                'softener_ml_per_unit'  => 20.00,
                'perfume_ml_per_unit'   => 10.00,
            ],
        ];

        foreach ($packages as $pkg) {
            ServicePackage::create(array_merge($pkg, ['branch_id' => 1, 'is_active' => true]));
        }
    }

    // ─── 3. Inventory Items ─────────────────────────────────────────

    private function seedInventoryItems(): void
    {
        $items = [
            // CHEMICAL
            [
                'sku'           => 'CHM-DET-001',
                'name'          => 'Deterjen Cair Attack Pro',
                'category'      => 'CHEMICAL',
                'system_stock'  => 18500.00,
                'actual_stock'  => 18500.00,
                'unit'          => 'ml',
                'min_threshold' => 5000.00,
                'usage_per_kg'  => 15.00,
                'cost_per_unit' => 0.08,  // Rp 0.08/ml
            ],
            [
                'sku'           => 'CHM-SOF-001',
                'name'          => 'Softener Molto Ultra',
                'category'      => 'CHEMICAL',
                'system_stock'  => 12000.00,
                'actual_stock'  => 12000.00,
                'unit'          => 'ml',
                'min_threshold' => 3000.00,
                'usage_per_kg'  => 10.00,
                'cost_per_unit' => 0.06,
            ],
            [
                'sku'           => 'CHM-BLT-001',
                'name'          => 'Pemutih Bayclin Regular',
                'category'      => 'CHEMICAL',
                'system_stock'  => 5000.00,
                'actual_stock'  => 5000.00,
                'unit'          => 'ml',
                'min_threshold' => 1000.00,
                'usage_per_kg'  => 3.00,
                'cost_per_unit' => 0.05,
            ],
            // PARFUM
            [
                'sku'           => 'PRF-SKR-001',
                'name'          => 'Parfum Laundry Sakura',
                'category'      => 'PARFUM',
                'system_stock'  => 8500.00,
                'actual_stock'  => 8500.00,
                'unit'          => 'ml',
                'min_threshold' => 2000.00,
                'usage_per_kg'  => 5.00,
                'cost_per_unit' => 0.15,
            ],
            [
                'sku'           => 'PRF-LVN-001',
                'name'          => 'Parfum Laundry Lavender',
                'category'      => 'PARFUM',
                'system_stock'  => 7200.00,
                'actual_stock'  => 7200.00,
                'unit'          => 'ml',
                'min_threshold' => 2000.00,
                'usage_per_kg'  => 5.00,
                'cost_per_unit' => 0.15,
            ],
            [
                'sku'           => 'PRF-FRS-001',
                'name'          => 'Parfum Laundry Fresh Ocean',
                'category'      => 'PARFUM',
                'system_stock'  => 4800.00,
                'actual_stock'  => 4800.00,
                'unit'          => 'ml',
                'min_threshold' => 2000.00,
                'usage_per_kg'  => 5.00,
                'cost_per_unit' => 0.18,
            ],
            // PACKAGING
            [
                'sku'           => 'PKG-PLT-001',
                'name'          => 'Plastik Packing Laundry 50x70cm',
                'category'      => 'PACKAGING',
                'system_stock'  => 500.00,
                'actual_stock'  => 500.00,
                'unit'          => 'Pcs',
                'min_threshold' => 100.00,
                'usage_per_kg'  => 0.20,
                'cost_per_unit' => 350.00,
            ],
            [
                'sku'           => 'PKG-HNG-001',
                'name'          => 'Hanger Kawat',
                'category'      => 'PACKAGING',
                'system_stock'  => 200.00,
                'actual_stock'  => 200.00,
                'unit'          => 'Pcs',
                'min_threshold' => 50.00,
                'usage_per_kg'  => 0.10,
                'cost_per_unit' => 500.00,
            ],
            // GAS
            [
                'sku'           => 'GAS-LPG-003',
                'name'          => 'Gas LPG 3Kg',
                'category'      => 'GAS',
                'system_stock'  => 8.00,
                'actual_stock'  => 8.00,
                'unit'          => 'Tabung',
                'min_threshold' => 2.00,
                'usage_per_kg'  => 0.05,
                'cost_per_unit' => 22000.00,
            ],
        ];

        foreach ($items as $item) {
            InventoryItem::create(array_merge($item, ['branch_id' => 1]));
        }

        // Tambahkan log restock awal untuk setiap item
        $inventoryItems = InventoryItem::withoutGlobalScope('branch')->where('branch_id', 1)->get();
        foreach ($inventoryItems as $item) {
            StockMutation::create([
                'branch_id'         => 1,
                'inventory_item_id' => $item->id,
                'mutation_type'     => 'RESTOCK_IN',
                'amount'            => $item->system_stock,
                'reference_invoice' => null,
                'notes'             => 'Stok awal pembukaan toko',
            ]);
        }
    }

    // ─── 4. Employees ──────────────────────────────────────────────

    private function seedEmployees(): void
    {
        $employees = [
            [
                'name'             => 'Siti Rahayu',
                'phone'            => '082111000001',
                'role'             => 'KASIR',
                'attendance_days'  => 26,
                'base_salary'      => 2200000,
                'daily_allowance'  => 25000,
                'commission_rate'  => 0,
                'total_output_kg'  => 0,
                'cash_advance'     => 0,
                'is_active'        => true,
            ],
            [
                'name'             => 'Budi Santoso',
                'phone'            => '082111000002',
                'role'             => 'OPERATOR_CUCI',
                'attendance_days'  => 25,
                'base_salary'      => 1800000,
                'daily_allowance'  => 25000,
                'commission_rate'  => 500,  // Rp 500/Kg
                'total_output_kg'  => 312.5,
                'cash_advance'     => 200000,
                'is_active'        => true,
            ],
            [
                'name'             => 'Ani Lestari',
                'phone'            => '082111000003',
                'role'             => 'SETRIKA',
                'attendance_days'  => 25,
                'base_salary'      => 1700000,
                'daily_allowance'  => 25000,
                'commission_rate'  => 400,  // Rp 400/Kg
                'total_output_kg'  => 298.0,
                'cash_advance'     => 0,
                'is_active'        => true,
            ],
            [
                'name'             => 'Joko Widodo',
                'phone'            => '082111000004',
                'role'             => 'OPERATOR_CUCI',
                'attendance_days'  => 24,
                'base_salary'      => 1800000,
                'daily_allowance'  => 25000,
                'commission_rate'  => 500,
                'total_output_kg'  => 278.0,
                'cash_advance'     => 500000,
                'is_active'        => true,
            ],
            [
                'name'             => 'Rina Handayani',
                'phone'            => '082111000005',
                'role'             => 'KURIR',
                'attendance_days'  => 25,
                'base_salary'      => 1600000,
                'daily_allowance'  => 25000,
                'commission_rate'  => 5000,  // Rp 5000/Trip
                'total_output_kg'  => 42.0,  // trip dihitung sebagai kg di sini
                'cash_advance'     => 0,
                'is_active'        => true,
            ],
        ];

        foreach ($employees as $emp) {
            Employee::create(array_merge($emp, ['branch_id' => 1]));
        }
    }

    // ─── 5. Customers ──────────────────────────────────────────────

    private function seedCustomers(): void
    {
        $customers = [
            [
                'name'            => 'Dewi Kusuma',
                'phone'           => '081300000001',
                'address'         => 'Jl. Anggrek No. 5, Tembalang, Semarang',
                'tier'            => 'VIP',
                'deposit_kg'      => 25.50,
                'loyalty_points'  => 1250,
                'preferences'     => 'Parfum Sakura, jangan dilipat, plastik tebal',
                'last_order_date' => now()->subDays(3),
            ],
            [
                'name'            => 'Agus Prasetyo',
                'phone'           => '081300000002',
                'address'         => 'Jl. Kenanga No. 8, Banyumanik, Semarang',
                'tier'            => 'SILVER',
                'deposit_kg'      => 10.00,
                'loyalty_points'  => 480,
                'preferences'     => 'Parfum Lavender, pakaian tipis harap dipisah',
                'last_order_date' => now()->subDays(5),
            ],
            [
                'name'            => 'Sri Wulandari',
                'phone'           => '081300000003',
                'address'         => 'Jl. Mawar No. 3, Pedurungan, Semarang',
                'tier'            => 'REGULAR',
                'deposit_kg'      => 0,
                'loyalty_points'  => 90,
                'preferences'     => null,
                'last_order_date' => now()->subDays(2),
            ],
            [
                'name'            => 'Hendra Wijaya',
                'phone'           => '081300000004',
                'address'         => 'Perum Graha Indah Blok D-12, Ungaran',
                'tier'            => 'SILVER',
                'deposit_kg'      => 8.00,
                'loyalty_points'  => 310,
                'preferences'     => 'Parfum Fresh Ocean, baju seragam jangan dicampur',
                'last_order_date' => now()->subDays(7),
            ],
            [
                'name'            => 'Lia Paramita',
                'phone'           => '081300000005',
                'address'         => 'Jl. Dahlia No. 15, Gajahmungkur, Semarang',
                'tier'            => 'VIP',
                'deposit_kg'      => 50.00,
                'loyalty_points'  => 2100,
                'preferences'     => 'Parfum Sakura, semua baju harus disetrika, taruh hanger',
                'last_order_date' => now()->subDays(1),
            ],
            [
                'name'            => 'Bambang Sutejo',
                'phone'           => '081300000006',
                'address'         => 'Jl. Cempaka No. 7, Candisari, Semarang',
                'tier'            => 'REGULAR',
                'deposit_kg'      => 0,
                'loyalty_points'  => 50,
                'preferences'     => null,
                'last_order_date' => now()->subDays(20),  // CHURN ALERT!
            ],
            [
                'name'            => 'Nurul Aini',
                'phone'           => '081300000007',
                'address'         => 'Jl. Flamboyan No. 22, Ngaliyan, Semarang',
                'tier'            => 'REGULAR',
                'deposit_kg'      => 3.50,
                'loyalty_points'  => 150,
                'preferences'     => 'Parfum Lavender',
                'last_order_date' => now()->subDays(4),
            ],
            [
                'name'            => 'Sigit Purnomo',
                'phone'           => '081300000008',
                'address'         => 'Komplek Bumi Indah No. 9, Mijen, Semarang',
                'tier'            => 'REGULAR',
                'deposit_kg'      => 0,
                'loyalty_points'  => 20,
                'preferences'     => null,
                'last_order_date' => now()->subDays(30),  // CHURN ALERT!
            ],
            [
                'name'            => 'Fitri Handayani',
                'phone'           => '081300000009',
                'address'         => 'Jl. Melati No. 11, Tembalang, Semarang',
                'tier'            => 'SILVER',
                'deposit_kg'      => 15.00,
                'loyalty_points'  => 620,
                'preferences'     => 'Tidak suka wewangian, gunakan yang unscented',
                'last_order_date' => now()->subDays(6),
            ],
            [
                'name'            => 'Rudi Hartono',
                'phone'           => '081300000010',
                'address'         => 'Jl. Setiabudi No. 45, Semarang Utara',
                'tier'            => 'REGULAR',
                'deposit_kg'      => 0,
                'loyalty_points'  => 0,
                'preferences'     => null,
                'last_order_date' => null,  // Pelanggan baru, belum pernah order
            ],
        ];

        foreach ($customers as $cust) {
            Customer::create(array_merge($cust, ['branch_id' => 1]));
        }
    }

    // ─── 6. Orders (Transaksi Aktif) ───────────────────────────────

    private function seedOrders(): void
    {
        // Ambil referensi data
        $customers = Customer::withoutGlobalScope('branch')->where('branch_id', 1)->pluck('id', 'name');
        $packages  = ServicePackage::withoutGlobalScope('branch')->where('branch_id', 1)->pluck('id', 'name');
        $employees = Employee::withoutGlobalScope('branch')->where('branch_id', 1)->pluck('id', 'name');

        $detId  = InventoryItem::withoutGlobalScope('branch')->where('sku', 'CHM-DET-001')->value('id');
        $sofId  = InventoryItem::withoutGlobalScope('branch')->where('sku', 'CHM-SOF-001')->value('id');
        $skrId  = InventoryItem::withoutGlobalScope('branch')->where('sku', 'PRF-SKR-001')->value('id');
        $lvnId  = InventoryItem::withoutGlobalScope('branch')->where('sku', 'PRF-LVN-001')->value('id');
        $frsId  = InventoryItem::withoutGlobalScope('branch')->where('sku', 'PRF-FRS-001')->value('id');

        $pkgKiloan   = $packages['Cuci Kiloan Reguler']           ?? 1;
        $pkgExpress  = $packages['Cuci Express (8 Jam)']          ?? 2;
        $pkgCuciSetrika = $packages['Cuci + Setrika Kiloan']      ?? 3;
        $pkgBedcover = $packages['Cuci Selimut / Bed Cover']      ?? 4;

        $opWasher1 = $employees['Budi Santoso'] ?? null;
        $opWasher2 = $employees['Joko Widodo']  ?? null;
        $opIron    = $employees['Ani Lestari']  ?? null;

        $orders = [
            // Order SELESAI (historis 7 hari lalu)
            [
                'invoice'       => 'INV-20261012-0001',
                'customer_id'   => $customers['Dewi Kusuma'],
                'package_id'    => $pkgCuciSetrika,
                'quantity'      => 5.5,
                'perfume'       => 'Sakura',
                'status'        => 'SELESAI',
                'pay_status'    => 'LUNAS',
                'pay_method'    => 'DEPOSIT_KG',
                'washed_by'     => $opWasher1,
                'ironed_by'     => $opIron,
                'packed_by'     => $opIron,
                'created_at'    => now()->subDays(7),
            ],
            // Order SELESAI (5 hari lalu)
            [
                'invoice'       => 'INV-20261014-0001',
                'customer_id'   => $customers['Lia Paramita'],
                'package_id'    => $pkgCuciSetrika,
                'quantity'      => 7.0,
                'perfume'       => 'Sakura',
                'status'        => 'SELESAI',
                'pay_status'    => 'LUNAS',
                'pay_method'    => 'DEPOSIT_KG',
                'washed_by'     => $opWasher2,
                'ironed_by'     => $opIron,
                'packed_by'     => $opIron,
                'created_at'    => now()->subDays(5),
            ],
            // Order SIAP_DIAMBIL (belum lunas)
            [
                'invoice'       => 'INV-20261017-0001',
                'customer_id'   => $customers['Agus Prasetyo'],
                'package_id'    => $pkgKiloan,
                'quantity'      => 4.0,
                'perfume'       => 'Lavender',
                'status'        => 'SIAP_DIAMBIL',
                'pay_status'    => 'BELUM_LUNAS',
                'pay_method'    => 'TUNAI',
                'rack_location' => 'Rak B-02',
                'washed_by'     => $opWasher1,
                'ironed_by'     => null,
                'packed_by'     => null,
                'created_at'    => now()->subDays(2),
            ],
            // Order PACKING (aktif hari ini)
            [
                'invoice'       => 'INV-20261018-0001',
                'customer_id'   => $customers['Sri Wulandari'],
                'package_id'    => $pkgKiloan,
                'quantity'      => 3.5,
                'perfume'       => 'Sakura',
                'status'        => 'PACKING',
                'pay_status'    => 'LUNAS',
                'pay_method'    => 'QRIS',
                'washed_by'     => $opWasher2,
                'ironed_by'     => $opIron,
                'packed_by'     => null,
                'created_at'    => now()->subDays(1),
            ],
            // Order SETRIKA (aktif)
            [
                'invoice'       => 'INV-20261018-0002',
                'customer_id'   => $customers['Hendra Wijaya'],
                'package_id'    => $pkgCuciSetrika,
                'quantity'      => 6.0,
                'perfume'       => 'Fresh Ocean',
                'status'        => 'SETRIKA',
                'pay_status'    => 'BELUM_LUNAS',
                'pay_method'    => 'TRANSFER',
                'washed_by'     => $opWasher1,
                'ironed_by'     => null,
                'packed_by'     => null,
                'created_at'    => now()->subDays(1),
            ],
            // Order EXPRESS (masuk hari ini, CUCI)
            [
                'invoice'       => 'INV-20261019-0001',
                'customer_id'   => $customers['Nurul Aini'],
                'package_id'    => $pkgExpress,
                'quantity'      => 2.5,
                'perfume'       => 'Lavender',
                'status'        => 'CUCI',
                'pay_status'    => 'LUNAS',
                'pay_method'    => 'QRIS',
                'notes'         => 'Pakaian anak kecil, harap lembut',
                'washed_by'     => $opWasher2,
                'ironed_by'     => null,
                'packed_by'     => null,
                'created_at'    => now()->subHours(3),
            ],
            // Order ANTREAN baru (baru masuk)
            [
                'invoice'       => 'INV-20261019-0002',
                'customer_id'   => $customers['Fitri Handayani'],
                'package_id'    => $pkgKiloan,
                'quantity'      => 5.0,
                'perfume'       => 'Sakura',
                'status'        => 'ANTREAN',
                'pay_status'    => 'BELUM_LUNAS',
                'pay_method'    => 'TUNAI',
                'washed_by'     => null,
                'ironed_by'     => null,
                'packed_by'     => null,
                'created_at'    => now()->subHours(1),
            ],
            // Order Bed Cover (ANTREAN)
            [
                'invoice'       => 'INV-20261019-0003',
                'customer_id'   => $customers['Dewi Kusuma'],
                'package_id'    => $pkgBedcover,
                'quantity'      => 2.0,
                'perfume'       => 'Sakura',
                'status'        => 'ANTREAN',
                'pay_status'    => 'BELUM_LUNAS',
                'pay_method'    => 'DEPOSIT_KG',
                'notes'         => '2 buah selimut tebal, warna biru dan putih',
                'washed_by'     => null,
                'ironed_by'     => null,
                'packed_by'     => null,
                'created_at'    => now()->subMinutes(30),
            ],
        ];

        foreach ($orders as $data) {
            $package = ServicePackage::withoutGlobalScope('branch')->find($data['package_id']);
            $qty     = (float) $data['quantity'];
            $price   = (int) round($package->price_per_unit * $qty);

            $order = Order::withoutGlobalScope('branch')->create([
                'branch_id'          => 1,
                'invoice_number'     => $data['invoice'],
                'customer_id'        => $data['customer_id'],
                'service_package_id' => $data['package_id'],
                'quantity'           => $qty,
                'unit'               => $package->unit,
                'perfume_variant'    => $data['perfume'],
                'notes'              => $data['notes'] ?? null,
                'status'             => $data['status'],
                'rack_location'      => $data['rack_location'] ?? null,
                'payment_status'     => $data['pay_status'],
                'payment_method'     => $data['pay_method'],
                'total_price'        => $price,
                'washed_by_id'       => $data['washed_by'] ?? null,
                'ironed_by_id'       => $data['ironed_by'] ?? null,
                'packed_by_id'       => $data['packed_by'] ?? null,
                'created_at'         => $data['created_at'],
                'updated_at'         => $data['created_at'],
            ]);

            // Catat StockMutation AUTO_DEDUCT untuk setiap order
            $deductions = [
                [$detId, (float) $package->detergent_ml_per_unit * $qty, 'Deterjen'],
                [$sofId,  (float) $package->softener_ml_per_unit  * $qty, 'Softener'],
                [$skrId,  (float) $package->perfume_ml_per_unit   * $qty, 'Parfum Sakura'],
            ];

            foreach ($deductions as [$itemId, $amount, $label]) {
                if (!$itemId || $amount <= 0) {
                    continue;
                }

                // Potong system_stock
                InventoryItem::withoutGlobalScope('branch')
                             ->where('id', $itemId)
                             ->decrement('system_stock', $amount);

                StockMutation::create([
                    'branch_id'         => 1,
                    'inventory_item_id' => $itemId,
                    'mutation_type'     => 'AUTO_DEDUCT_ORDER',
                    'amount'            => -$amount,
                    'reference_invoice' => $data['invoice'],
                    'notes'             => "Auto-deduct {$label}: {$data['invoice']}",
                ]);
            }
        }
    }

    // ─── 7. Utility Logs ───────────────────────────────────────────

    private function seedUtilityLogs(): void
    {
        $logs = [
            // Listrik Juli
            [
                'utility_type' => 'LISTRIK_PLN',
                'period_date'  => Carbon::create(2026, 7, 31),
                'meter_start'  => 12450.00,
                'meter_end'    => 12913.00,
                'usage_volume' => 463.00,
                'unit'         => 'kWh',
                'total_cost'   => 693450,
                'recorded_by'  => 'Siti Rahayu',
                'notes'        => 'Token PLN bulan Juli 2026',
            ],
            // Listrik Agustus
            [
                'utility_type' => 'LISTRIK_PLN',
                'period_date'  => Carbon::create(2026, 8, 31),
                'meter_start'  => 12913.00,
                'meter_end'    => 13390.00,
                'usage_volume' => 477.00,
                'unit'         => 'kWh',
                'total_cost'   => 715500,
                'recorded_by'  => 'Siti Rahayu',
                'notes'        => 'Token PLN bulan Agustus 2026',
            ],
            // Listrik September
            [
                'utility_type' => 'LISTRIK_PLN',
                'period_date'  => Carbon::create(2026, 9, 30),
                'meter_start'  => 13390.00,
                'meter_end'    => 13862.00,
                'usage_volume' => 472.00,
                'unit'         => 'kWh',
                'total_cost'   => 708000,
                'recorded_by'  => 'Siti Rahayu',
            ],
            // Air PDAM Juli
            [
                'utility_type' => 'AIR_PDAM',
                'period_date'  => Carbon::create(2026, 7, 31),
                'meter_start'  => 4210.00,
                'meter_end'    => 4378.00,
                'usage_volume' => 168.00,
                'unit'         => 'm3',
                'total_cost'   => 294000,
                'recorded_by'  => 'Siti Rahayu',
                'notes'        => 'Tagihan PDAM Juli 2026',
            ],
            // Air PDAM Agustus
            [
                'utility_type' => 'AIR_PDAM',
                'period_date'  => Carbon::create(2026, 8, 31),
                'meter_start'  => 4378.00,
                'meter_end'    => 4549.00,
                'usage_volume' => 171.00,
                'unit'         => 'm3',
                'total_cost'   => 299250,
                'recorded_by'  => 'Siti Rahayu',
            ],
            // Air PDAM September
            [
                'utility_type' => 'AIR_PDAM',
                'period_date'  => Carbon::create(2026, 9, 30),
                'meter_start'  => 4549.00,
                'meter_end'    => 4718.00,
                'usage_volume' => 169.00,
                'unit'         => 'm3',
                'total_cost'   => 295750,
                'recorded_by'  => 'Siti Rahayu',
            ],
            // Gas LPG Juli
            [
                'utility_type' => 'GAS_LPG',
                'period_date'  => Carbon::create(2026, 7, 31),
                'meter_start'  => 0,
                'meter_end'    => 0,
                'usage_volume' => 16.00,
                'unit'         => 'Tabung',
                'total_cost'   => 352000,
                'recorded_by'  => 'Budi Santoso',
                'notes'        => '16 tabung 3Kg untuk mesin pengering Juli',
            ],
            // Gas LPG Agustus
            [
                'utility_type' => 'GAS_LPG',
                'period_date'  => Carbon::create(2026, 8, 31),
                'meter_start'  => 0,
                'meter_end'    => 0,
                'usage_volume' => 18.00,
                'unit'         => 'Tabung',
                'total_cost'   => 396000,
                'recorded_by'  => 'Budi Santoso',
            ],
            // Gas LPG September
            [
                'utility_type' => 'GAS_LPG',
                'period_date'  => Carbon::create(2026, 9, 30),
                'meter_start'  => 0,
                'meter_end'    => 0,
                'usage_volume' => 17.00,
                'unit'         => 'Tabung',
                'total_cost'   => 374000,
                'recorded_by'  => 'Budi Santoso',
            ],
        ];

        foreach ($logs as $log) {
            UtilityLog::create(array_merge($log, ['branch_id' => 1]));
        }
    }

    // ─── 8. Complaints ─────────────────────────────────────────────

    private function seedComplaints(): void
    {
        $customers = Customer::withoutGlobalScope('branch')->where('branch_id', 1)->pluck('id', 'name');

        $complaints = [
            [
                'invoice_number' => 'INV-20261012-0001',
                'customer_id'    => $customers['Dewi Kusuma'],
                'issue_type'     => 'Pakaian Rusak / Luntur',
                'description'    => 'Baju batik warna merah muda luntur ke kemeja putih. Pelanggan meminta ganti rugi.',
                'status'         => 'RESOLVED',
                'compensation'   => 'Gratis cuci 3 Kg berikutnya + minta maaf langsung',
            ],
            [
                'invoice_number' => 'INV-20261017-0001',
                'customer_id'    => $customers['Agus Prasetyo'],
                'issue_type'     => 'Barang Kurang / Hilang',
                'description'    => 'Pelanggan mengklaim ada 1 kaos polos tidak ada di bungkusan.',
                'status'         => 'IN_PROGRESS',
                'compensation'   => null,
            ],
            [
                'invoice_number' => 'INV-20261018-0002',
                'customer_id'    => $customers['Hendra Wijaya'],
                'issue_type'     => 'Parfum Tidak Sesuai',
                'description'    => 'Diminta parfum Fresh Ocean tapi tercium bau Sakura. Pelanggan tidak suka.',
                'status'         => 'OPEN',
                'compensation'   => null,
            ],
        ];

        foreach ($complaints as $complaint) {
            Complaint::create(array_merge($complaint, ['branch_id' => 1]));
        }
    }
}
