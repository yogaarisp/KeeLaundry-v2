<?php

namespace App\Livewire\Pos;

use App\Models\Customer;
use App\Models\Employee;
use App\Models\InventoryItem;
use App\Models\Order;
use App\Models\ServicePackage;
use App\Services\LaundryOrderService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

class PosOrderCrud extends Component
{
    // ── Tabs ──────────────────────────────────────────────────────
    public string $activeTab = 'pos'; // 'pos' | 'history'

    // ── POS Form ──────────────────────────────────────────────────
    public string $customerSearch    = '';
    public ?int   $selectedCustomerId = null;
    public string $quickCustomerName  = '';
    public string $quickCustomerPhone = '';
    public bool   $showQuickCustomer  = false;

    public ?int   $selectedPackageId  = null;
    public float  $quantity           = 1.0;
    public string $perfumeVariant     = 'Sakura';
    public string $paymentStatus      = 'BELUM_LUNAS';
    public string $paymentMethod      = 'TUNAI';
    public string $notes              = '';

    // ── Receipt preview ───────────────────────────────────────────
    public bool   $showReceipt        = false;
    public ?int   $receiptOrderId     = null;

    // ── Order history filters ─────────────────────────────────────
    public string $historySearch      = '';
    public string $historyStatus      = '';
    public string $historyPayment     = '';
    public string $historyDate        = '';

    // ── Edit Order modal ──────────────────────────────────────────
    public bool   $showEditOrder      = false;
    public ?int   $editingOrderId     = null;
    public float  $editQuantity       = 1.0;
    public ?int   $editPackageId      = null;
    public string $editPaymentStatus  = 'BELUM_LUNAS';
    public string $editPaymentMethod  = 'TUNAI';
    public string $editNotes          = '';

    // ── Void confirm ──────────────────────────────────────────────
    public bool   $showVoidConfirm    = false;
    public ?int   $voidOrderId        = null;
    public string $voidReason         = '';

    protected LaundryOrderService $orderService;

    public function boot(LaundryOrderService $orderService): void
    {
        $this->orderService = $orderService;
    }

    // ── Computed: Customer search results ────────────────────────

    #[Computed]
    public function customerResults(): Collection
    {
        if (mb_strlen(trim($this->customerSearch)) < 2) {
            return collect();
        }
        return Customer::where(function ($q) {
            $q->where('name', 'like', "%{$this->customerSearch}%")
              ->orWhere('phone', 'like', "%{$this->customerSearch}%");
        })->limit(6)->get(['id', 'name', 'phone', 'tier', 'deposit_kg', 'preferences']);
    }

    #[Computed]
    public function selectedCustomer(): ?Customer
    {
        return $this->selectedCustomerId
            ? Customer::find($this->selectedCustomerId, ['id', 'name', 'phone', 'tier', 'deposit_kg', 'preferences', 'loyalty_points'])
            : null;
    }

    // ── Computed: Service packages ────────────────────────────────

    #[Computed]
    public function activePackages(): Collection
    {
        return ServicePackage::where('is_active', true)
            ->orderBy('category')
            ->orderBy('price_per_unit')
            ->get();
    }

    #[Computed]
    public function selectedPackage(): ?ServicePackage
    {
        return $this->selectedPackageId
            ? ServicePackage::find($this->selectedPackageId)
            : null;
    }

    // ── Computed: BOM & pricing preview ──────────────────────────

    #[Computed]
    public function orderPreview(): array
    {
        $pkg = $this->selectedPackage;
        if (! $pkg) {
            return [];
        }

        $qty        = max(0.1, $this->quantity);
        $totalPrice = (int) round($pkg->price_per_unit * $qty);

        $detId = InventoryItem::where('category', 'CHEMICAL')->where('name', 'like', '%Deterjen%')->value('id');
        $sofId = InventoryItem::where('category', 'CHEMICAL')->where('name', 'like', '%Softener%')->value('id');

        return [
            'package_name'   => $pkg->name,
            'unit'           => $pkg->unit,
            'price_per_unit' => $pkg->price_per_unit,
            'quantity'       => $qty,
            'total_price'    => $totalPrice,
            'detergent_ml'   => round((float) $pkg->detergent_ml_per_unit * $qty, 1),
            'softener_ml'    => round((float) $pkg->softener_ml_per_unit  * $qty, 1),
            'perfume_ml'     => round((float) $pkg->perfume_ml_per_unit   * $qty, 1),
            'detergent_id'   => $detId,
            'softener_id'    => $sofId,
        ];
    }

    // ── Computed: Inventory items for BOM ─────────────────────────

    #[Computed]
    public function perfumeItems(): Collection
    {
        return InventoryItem::where('category', 'PARFUM')->orderBy('name')->get(['id', 'name', 'system_stock', 'unit']);
    }

    #[Computed]
    public function selectedPerfumeItem(): ?InventoryItem
    {
        return InventoryItem::where('category', 'PARFUM')
            ->where('name', 'like', "%{$this->perfumeVariant}%")
            ->first();
    }

    // ── Computed: Order history ───────────────────────────────────

    #[Computed]
    public function orders(): Collection
    {
        return Order::withoutGlobalScope('branch')
            ->where('branch_id', session('active_branch_id', 1))
            ->with(['customer:id,name,phone', 'servicePackage:id,name,category'])
            ->when($this->historySearch, fn ($q) =>
                $q->where(fn ($q) =>
                    $q->where('invoice_number', 'like', "%{$this->historySearch}%")
                      ->orWhereHas('customer', fn ($q) =>
                          $q->where('name', 'like', "%{$this->historySearch}%")
                            ->orWhere('phone', 'like', "%{$this->historySearch}%")
                      )
                )
            )
            ->when($this->historyStatus, fn ($q) => $q->where('status', $this->historyStatus))
            ->when($this->historyPayment, fn ($q) => $q->where('payment_status', $this->historyPayment))
            ->when($this->historyDate, fn ($q) => $q->whereDate('created_at', $this->historyDate))
            ->orderByDesc('created_at')
            ->limit(50)
            ->get();
    }

    #[Computed]
    public function receiptOrder(): ?Order
    {
        return $this->receiptOrderId
            ? Order::withoutGlobalScope('branch')->with(['customer', 'servicePackage'])->find($this->receiptOrderId)
            : null;
    }

    // ── POS: Select customer ──────────────────────────────────────

    public function selectCustomer(int $id): void
    {
        $this->selectedCustomerId = $id;
        $customer = Customer::find($id);
        $this->customerSearch = $customer?->name ?? '';
        unset($this->customerResults, $this->selectedCustomer);
    }

    public function clearCustomer(): void
    {
        $this->selectedCustomerId = null;
        $this->customerSearch     = '';
        unset($this->selectedCustomer);
    }

    public function saveQuickCustomer(): void
    {
        $this->validate([
            'quickCustomerName'  => ['required', 'string', 'min:2'],
            'quickCustomerPhone' => ['required', 'string', 'min:9', 'unique:customers,phone'],
        ], [], [
            'quickCustomerName'  => 'Nama pelanggan',
            'quickCustomerPhone' => 'Nomor HP',
        ]);

        $customer = Customer::create([
            'name'  => trim($this->quickCustomerName),
            'phone' => trim($this->quickCustomerPhone),
            'tier'  => 'REGULAR',
        ]);

        $this->selectCustomer($customer->id);
        $this->showQuickCustomer  = false;
        $this->quickCustomerName  = '';
        $this->quickCustomerPhone = '';
        $this->dispatch('notify', ['type' => 'success', 'title' => 'Pelanggan Baru', 'message' => "{$customer->name} berhasil didaftarkan."]);
    }

    // ── POS: Select package ────────────────────────────────────────

    public function selectPackage(int $id): void
    {
        $this->selectedPackageId = $id;
        unset($this->selectedPackage, $this->orderPreview);
    }

    // ── POS: Submit Order ─────────────────────────────────────────

    public function submitOrder(): void
    {
        $this->validate([
            'selectedCustomerId' => ['required', 'integer', 'exists:customers,id'],
            'selectedPackageId'  => ['required', 'integer', 'exists:service_packages,id'],
            'quantity'           => ['required', 'numeric', 'min:0.1'],
            'perfumeVariant'     => ['required', 'string'],
            'paymentStatus'      => ['required', Rule::in(['LUNAS', 'BELUM_LUNAS'])],
            'paymentMethod'      => ['required', Rule::in(['TUNAI', 'QRIS', 'TRANSFER', 'DEPOSIT_KG'])],
        ], [], [
            'selectedCustomerId' => 'Pelanggan',
            'selectedPackageId'  => 'Paket layanan',
            'quantity'           => 'Berat/jumlah',
        ]);

        try {
            $perfumeItem = $this->selectedPerfumeItem;
            $preview     = $this->orderPreview;

            $order = $this->orderService->createOrder([
                'customer_id'        => $this->selectedCustomerId,
                'service_package_id' => $this->selectedPackageId,
                'quantity'           => $this->quantity,
                'perfume_variant'    => $this->perfumeVariant,
                'notes'              => $this->notes,
                'payment_status'     => $this->paymentStatus,
                'payment_method'     => $this->paymentMethod,
                'detergent_item_id'  => $preview['detergent_id'] ?? null,
                'softener_item_id'   => $preview['softener_id'] ?? null,
                'perfume_item_id'    => $perfumeItem?->id,
            ]);

            // Show receipt
            $this->receiptOrderId = $order->id;
            $this->showReceipt    = true;

            $this->dispatch('notify', [
                'type'    => 'success',
                'title'   => 'Order Dibuat!',
                'message' => "Invoice {$order->invoice_number} — Rp " . number_format($order->total_price),
            ]);

            $this->resetPosForm();
            unset($this->orders, $this->receiptOrder);

        } catch (ValidationException $e) {
            $errors = collect($e->errors())->flatten()->first();
            $this->dispatch('notify', ['type' => 'error', 'title' => 'Gagal', 'message' => $errors]);
        }
    }

    private function resetPosForm(): void
    {
        $this->selectedCustomerId  = null;
        $this->customerSearch      = '';
        $this->selectedPackageId   = null;
        $this->quantity            = 1.0;
        $this->perfumeVariant      = 'Sakura';
        $this->paymentStatus       = 'BELUM_LUNAS';
        $this->paymentMethod       = 'TUNAI';
        $this->notes               = '';
        $this->resetValidation();
        unset($this->selectedCustomer, $this->selectedPackage, $this->orderPreview);
    }

    // ── Receipt ───────────────────────────────────────────────────

    public function openReceipt(int $orderId): void
    {
        $this->receiptOrderId = $orderId;
        $this->showReceipt    = true;
        unset($this->receiptOrder);
    }

    public function closeReceipt(): void
    {
        $this->showReceipt    = false;
        $this->receiptOrderId = null;
    }

    // ── Edit Order ────────────────────────────────────────────────

    public function openEditOrder(int $orderId): void
    {
        $order = Order::withoutGlobalScope('branch')->findOrFail($orderId);
        $this->editingOrderId    = $order->id;
        $this->editQuantity      = (float) $order->quantity;
        $this->editPackageId     = $order->service_package_id;
        $this->editPaymentStatus = $order->payment_status;
        $this->editPaymentMethod = $order->payment_method;
        $this->editNotes         = $order->notes ?? '';
        $this->showEditOrder     = true;
        unset($this->orders);
    }

    public function saveEditOrder(): void
    {
        $this->validate([
            'editQuantity'      => ['required', 'numeric', 'min:0.1'],
            'editPackageId'     => ['required', 'integer', 'exists:service_packages,id'],
            'editPaymentStatus' => ['required', Rule::in(['LUNAS', 'BELUM_LUNAS'])],
            'editPaymentMethod' => ['required', Rule::in(['TUNAI', 'QRIS', 'TRANSFER', 'DEPOSIT_KG'])],
        ]);

        try {
            $order = Order::withoutGlobalScope('branch')->findOrFail($this->editingOrderId);

            $preview = [];
            if ((int) $this->editPackageId !== (int) $order->service_package_id) {
                // pakage changed — find chemical items
                $preview['detergent_item_id'] = InventoryItem::where('category', 'CHEMICAL')->where('name', 'like', '%Deterjen%')->value('id');
                $preview['softener_item_id']  = InventoryItem::where('category', 'CHEMICAL')->where('name', 'like', '%Softener%')->value('id');
                $preview['perfume_item_id']   = InventoryItem::where('category', 'PARFUM')->first()?->id;
            }

            $this->orderService->updateOrder($order, array_merge([
                'quantity'           => $this->editQuantity,
                'service_package_id' => $this->editPackageId,
                'payment_status'     => $this->editPaymentStatus,
                'payment_method'     => $this->editPaymentMethod,
                'notes'              => $this->editNotes,
            ], $preview));

            $this->dispatch('notify', ['type' => 'success', 'title' => 'Order Diperbarui', 'message' => "Order #{$order->invoice_number} berhasil diedit."]);
            $this->showEditOrder = false;
            unset($this->orders);

        } catch (ValidationException $e) {
            $this->dispatch('notify', ['type' => 'error', 'title' => 'Gagal Edit', 'message' => collect($e->errors())->flatten()->first()]);
        }
    }

    // ── Void / Cancel ─────────────────────────────────────────────

    public function confirmVoid(int $orderId): void
    {
        $this->voidOrderId   = $orderId;
        $this->voidReason    = '';
        $this->showVoidConfirm = true;
    }

    public function voidOrder(): void
    {
        if (! $this->voidOrderId) {
            return;
        }

        $this->validate(['voidReason' => ['required', 'string', 'min:5']], [], ['voidReason' => 'Alasan pembatalan']);

        try {
            $order = Order::withoutGlobalScope('branch')->findOrFail($this->voidOrderId);
            $this->orderService->voidOrder($order, $this->voidReason);
            $this->dispatch('notify', ['type' => 'warning', 'title' => 'Order di-VOID', 'message' => "Invoice {$order->invoice_number} berhasil dibatalkan. Stok sabun dikembalikan."]);
            $this->showVoidConfirm = false;
            $this->voidOrderId     = null;
            unset($this->orders);
        } catch (ValidationException $e) {
            $this->dispatch('notify', ['type' => 'error', 'title' => 'Gagal Void', 'message' => collect($e->errors())->flatten()->first()]);
        }
    }

    // ── Pelunasan cepat ───────────────────────────────────────────

    public function markAsPaid(int $orderId): void
    {
        DB::transaction(function () use ($orderId) {
            Order::withoutGlobalScope('branch')
                ->where('id', $orderId)
                ->where('payment_status', 'BELUM_LUNAS')
                ->update(['payment_status' => 'LUNAS']);
        });
        $this->dispatch('notify', ['type' => 'success', 'title' => 'Lunas!', 'message' => 'Pembayaran berhasil dicatat.']);
        unset($this->orders);
    }

    public function render()
    {
        return view('livewire.pos.pos-order-crud', [
            'selectedCustomer' => $this->selectedCustomer,
            'selectedPackage'  => $this->selectedPackage,
        ])->layout('layouts.app', ['pageTitle' => 'Kasir POS']);
    }
}
