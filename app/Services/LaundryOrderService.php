<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Employee;
use App\Models\InventoryItem;
use App\Models\Order;
use App\Models\ServicePackage;
use App\Models\StockMutation;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class LaundryOrderService
{
    /**
     * CREATE ORDER
     * ─────────────────────────────────────────────────────────────
     * 1. Hitung total harga.
     * 2. Potong stok deterjen, softener, dan parfum sesuai BOM.
     * 3. Catat StockMutation untuk setiap bahan.
     * 4. Kurangi deposit_kg pelanggan jika metode DEPOSIT_KG.
     * 5. Update last_order_date pelanggan.
     *
     * @param  array{
     *   customer_id: int,
     *   service_package_id: int,
     *   quantity: float,
     *   perfume_variant: string,
     *   notes?: string,
     *   payment_status: string,
     *   payment_method: string,
     *   detergent_item_id?: int,
     *   softener_item_id?: int,
     *   perfume_item_id?: int,
     * } $data
     */
    public function createOrder(array $data): Order
    {
        return DB::transaction(function () use ($data) {
            /** @var ServicePackage $package */
            $package  = ServicePackage::findOrFail($data['service_package_id']);
            $quantity = (float) $data['quantity'];

            // Hitung harga total
            $totalPrice = (int) round($package->price_per_unit * $quantity);

            // Buat order
            $order = Order::create([
                'invoice_number'     => Order::generateInvoiceNumber(),
                'customer_id'        => $data['customer_id'],
                'service_package_id' => $data['service_package_id'],
                'quantity'           => $quantity,
                'unit'               => $package->unit,
                'perfume_variant'    => $data['perfume_variant'],
                'notes'              => $data['notes'] ?? null,
                'status'             => Order::STATUS_ANTREAN,
                'payment_status'     => $data['payment_status'],
                'payment_method'     => $data['payment_method'],
                'total_price'        => $totalPrice,
            ]);

            // Auto-deduct stok bahan baku
            $this->deductStock(
                order: $order,
                package: $package,
                quantity: $quantity,
                detergentItemId: $data['detergent_item_id'] ?? null,
                softenerItemId: $data['softener_item_id'] ?? null,
                perfumeItemId: $data['perfume_item_id'] ?? null,
            );

            // Kurangi deposit Kg jika bayar pakai deposit
            if ($data['payment_method'] === 'DEPOSIT_KG') {
                /** @var Customer $customer */
                $customer = Customer::find($data['customer_id']);
                if ($customer && (float) $customer->deposit_kg < $quantity) {
                    throw ValidationException::withMessages([
                        'payment_method' => "Saldo Deposit Kg tidak cukup. Sisa: {$customer->deposit_kg} Kg, dibutuhkan: {$quantity} Kg.",
                    ]);
                }
                $customer?->decrement('deposit_kg', $quantity);
            }

            // Update last_order_date pelanggan
            Customer::where('id', $data['customer_id'])
                    ->update(['last_order_date' => now()]);

            return $order;
        });
    }

    /**
     * UPDATE ORDER (Revisi Berat / Layanan)
     * ─────────────────────────────────────────────────────────────
     * Hitung selisih ΔW = berat_baru - berat_lama, lalu sesuaikan:
     * - Stok bahan baku (deduct jika bertambah, rollback jika berkurang).
     * - Total tagihan.
     * - Deposit Kg pelanggan jika bayar via deposit.
     */
    public function updateOrder(Order $order, array $data): Order
    {
        return DB::transaction(function () use ($order, $data) {
            $oldQuantity = (float) $order->quantity;
            $newQuantity = (float) ($data['quantity'] ?? $order->quantity);
            $deltaQty    = $newQuantity - $oldQuantity;

            // Jika paket layanan berubah, rollback stok lama dulu
            $packageChanged = isset($data['service_package_id'])
                              && (int) $data['service_package_id'] !== (int) $order->service_package_id;

            if ($packageChanged) {
                $this->rollbackStock(
                    order: $order,
                    package: $order->servicePackage,
                    quantity: $oldQuantity,
                    mutationType: StockMutation::MUTATION_OPNAME,
                    notes: "Revisi paket layanan dari order {$order->invoice_number}",
                );
            }

            $servicePackageId = $data['service_package_id'] ?? $order->service_package_id;
            $package          = ServicePackage::findOrFail($servicePackageId);

            // Hitung harga baru
            $totalPrice = (int) round($package->price_per_unit * $newQuantity);

            // Update record order
            $order->update(array_merge($data, [
                'quantity'           => $newQuantity,
                'service_package_id' => $servicePackageId,
                'total_price'        => $totalPrice,
            ]));

            // Sesuaikan stok bahan baku berdasarkan selisih
            if ($packageChanged) {
                // Paket baru: deduct penuh
                $this->deductStock(
                    order: $order,
                    package: $package,
                    quantity: $newQuantity,
                    detergentItemId: $data['detergent_item_id'] ?? null,
                    softenerItemId: $data['softener_item_id'] ?? null,
                    perfumeItemId: $data['perfume_item_id'] ?? null,
                );
            } elseif (abs($deltaQty) > 0.001) {
                // Paket sama, hanya berat berubah: sesuaikan selisih
                if ($deltaQty > 0) {
                    $this->deductStock(
                        order: $order,
                        package: $package,
                        quantity: $deltaQty,
                        detergentItemId: $data['detergent_item_id'] ?? null,
                        softenerItemId: $data['softener_item_id'] ?? null,
                        perfumeItemId: $data['perfume_item_id'] ?? null,
                    );
                } else {
                    // Rollback sebagian (berat dikurangi)
                    $this->rollbackStock(
                        order: $order,
                        package: $package,
                        quantity: abs($deltaQty),
                        mutationType: 'VOID_ROLLBACK',
                        notes: "Koreksi berat order {$order->invoice_number}",
                    );
                }
            }

            // Sesuaikan deposit Kg jika bayar via deposit
            if (($data['payment_method'] ?? $order->payment_method) === 'DEPOSIT_KG' && abs($deltaQty) > 0.001) {
                $customer = Customer::find($order->customer_id);
                if ($deltaQty > 0) {
                    $customer?->decrement('deposit_kg', $deltaQty);
                } else {
                    $customer?->increment('deposit_kg', abs($deltaQty));
                }
            }

            return $order->fresh();
        });
    }

    /**
     * VOID ORDER (Auto-Rollback Stok & Deposit)
     * ─────────────────────────────────────────────────────────────
     * 1. Ubah status order menjadi VOID.
     * 2. Kembalikan stok bahan baku.
     * 3. Kembalikan deposit Kg jika metode bayar DEPOSIT_KG.
     * 4. Kurangi total_output_kg karyawan yang sudah klaim produksi.
     */
    public function voidOrder(Order $order, string $reason = ''): Order
    {
        return DB::transaction(function () use ($order, $reason) {
            if ($order->status === Order::STATUS_VOID) {
                throw ValidationException::withMessages([
                    'status' => 'Order ini sudah berstatus VOID.',
                ]);
            }

            // Rollback stok bahan baku
            $this->rollbackStock(
                order: $order,
                package: $order->servicePackage,
                quantity: (float) $order->quantity,
                mutationType: 'VOID_ROLLBACK',
                notes: "VOID order {$order->invoice_number}: {$reason}",
            );

            // Kembalikan deposit Kg
            if ($order->payment_method === 'DEPOSIT_KG' && $order->payment_status !== Order::PAYMENT_REFUND) {
                Customer::where('id', $order->customer_id)
                        ->increment('deposit_kg', (float) $order->quantity);
            }

            // Kurangi output Kg karyawan yang sudah klaim
            $qty = (float) $order->quantity;
            if ($order->washed_by_id) {
                Employee::where('id', $order->washed_by_id)->decrement('total_output_kg', $qty);
            }
            if ($order->ironed_by_id) {
                Employee::where('id', $order->ironed_by_id)->decrement('total_output_kg', $qty);
            }
            if ($order->packed_by_id) {
                Employee::where('id', $order->packed_by_id)->decrement('total_output_kg', $qty);
            }

            // Update status order
            $order->update([
                'status'         => Order::STATUS_VOID,
                'payment_status' => Order::PAYMENT_REFUND,
                'notes'          => trim(($order->notes ?? '') . "\n[VOID] {$reason}"),
            ]);

            return $order->fresh();
        });
    }

    /**
     * UPDATE KANBAN STATUS + ASSIGN PIC KARYAWAN
     * ─────────────────────────────────────────────────────────────
     * Saat karyawan klaim tahap produksi, tambahkan output_kg ke karyawan.
     * Saat PIC dikoreksi, kurangi output_kg karyawan lama, tambah karyawan baru.
     */
    public function updateProductionStatus(Order $order, string $newStatus, array $picData = []): Order
    {
        return DB::transaction(function () use ($order, $newStatus, $picData) {
            $qty = (float) $order->quantity;

            // Koreksi PIC: jika ada perubahan washed_by_id
            if (array_key_exists('washed_by_id', $picData)) {
                if ($order->washed_by_id && $order->washed_by_id !== $picData['washed_by_id']) {
                    Employee::where('id', $order->washed_by_id)->decrement('total_output_kg', $qty);
                }
                if ($picData['washed_by_id'] && $order->washed_by_id !== $picData['washed_by_id']) {
                    Employee::where('id', $picData['washed_by_id'])->increment('total_output_kg', $qty);
                }
            }

            // Koreksi PIC: ironed_by_id
            if (array_key_exists('ironed_by_id', $picData)) {
                if ($order->ironed_by_id && $order->ironed_by_id !== $picData['ironed_by_id']) {
                    Employee::where('id', $order->ironed_by_id)->decrement('total_output_kg', $qty);
                }
                if ($picData['ironed_by_id'] && $order->ironed_by_id !== $picData['ironed_by_id']) {
                    Employee::where('id', $picData['ironed_by_id'])->increment('total_output_kg', $qty);
                }
            }

            // Koreksi PIC: packed_by_id
            if (array_key_exists('packed_by_id', $picData)) {
                if ($order->packed_by_id && $order->packed_by_id !== $picData['packed_by_id']) {
                    Employee::where('id', $order->packed_by_id)->decrement('total_output_kg', $qty);
                }
                if ($picData['packed_by_id'] && $order->packed_by_id !== $picData['packed_by_id']) {
                    Employee::where('id', $picData['packed_by_id'])->increment('total_output_kg', $qty);
                }
            }

            $order->update(array_merge(['status' => $newStatus], $picData));

            return $order->fresh();
        });
    }

    // ─── Private Helpers ────────────────────────────────────────────

    /**
     * Potong stok sistem bahan baku sesuai formula BOM.
     * Hanya memotong item yang ID-nya disuplai (tidak null).
     */
    private function deductStock(
        Order $order,
        ServicePackage $package,
        float $quantity,
        ?int $detergentItemId,
        ?int $softenerItemId,
        ?int $perfumeItemId,
    ): void {
        $mutations = [
            [$detergentItemId, (float) $package->detergent_ml_per_unit * $quantity, 'Deterjen'],
            [$softenerItemId,  (float) $package->softener_ml_per_unit  * $quantity, 'Softener'],
            [$perfumeItemId,   (float) $package->perfume_ml_per_unit   * $quantity, 'Parfum'],
        ];

        foreach ($mutations as [$itemId, $amount, $label]) {
            if (!$itemId || $amount <= 0) {
                continue;
            }

            InventoryItem::where('id', $itemId)
                         ->decrement('system_stock', $amount);

            StockMutation::create([
                'inventory_item_id' => $itemId,
                'mutation_type'     => 'AUTO_DEDUCT_ORDER',
                'amount'            => -$amount,
                'reference_invoice' => $order->invoice_number,
                'notes'             => "Auto-deduct {$label}: {$order->invoice_number}",
            ]);
        }
    }

    /**
     * Kembalikan stok sistem bahan baku (rollback/opname).
     */
    private function rollbackStock(
        Order $order,
        ServicePackage $package,
        float $quantity,
        string $mutationType,
        string $notes,
    ): void {
        // Cari ID item dari log mutasi sebelumnya milik order ini
        $previousMutations = StockMutation::withoutGlobalScope('branch')
            ->where('reference_invoice', $order->invoice_number)
            ->where('mutation_type', 'AUTO_DEDUCT_ORDER')
            ->get();

        if ($previousMutations->isEmpty()) {
            // Jika tidak ada log (order lama), rollback berdasarkan BOM saja
            // tanpa bisa mengidentifikasi item spesifik — skip gracefully.
            return;
        }

        foreach ($previousMutations as $mutation) {
            // Hitung proporsi rollback berdasarkan persentase quantity
            $originalQty = $order->quantity > 0
                ? (abs((float) $mutation->amount) / max((float) $order->quantity, 0.001))
                : 0;
            $rollbackAmount = $originalQty * $quantity;

            if ($rollbackAmount <= 0) {
                continue;
            }

            InventoryItem::where('id', $mutation->inventory_item_id)
                         ->increment('system_stock', $rollbackAmount);

            StockMutation::create([
                'inventory_item_id' => $mutation->inventory_item_id,
                'mutation_type'     => $mutationType,
                'amount'            => $rollbackAmount,
                'reference_invoice' => $order->invoice_number,
                'notes'             => $notes,
            ]);
        }
    }
}
