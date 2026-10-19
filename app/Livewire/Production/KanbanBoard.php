<?php

namespace App\Livewire\Production;

use App\Models\Employee;
use App\Models\Order;
use App\Services\LaundryOrderService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Computed;
use Livewire\Component;

class KanbanBoard extends Component
{
    // ── Mobile: active stage pill ──────────────────────────────────
    public string $mobileStage = 'ANTREAN';

    // ── Update status drawer ──────────────────────────────────────
    public bool   $showUpdateDrawer = false;
    public ?int   $updatingOrderId  = null;
    public string $newStatus        = '';
    public ?int   $picEmployeeId    = null;
    public string $rackLocation     = '';

    // ── Selesai confirm ───────────────────────────────────────────
    public bool   $showFinishConfirm = false;
    public ?int   $finishOrderId     = null;

    protected LaundryOrderService $orderService;

    public function boot(LaundryOrderService $orderService): void
    {
        $this->orderService = $orderService;
    }

    // ── Computed: all active orders grouped by status ─────────────

    #[Computed]
    public function boardData(): array
    {
        $branchId = session('active_branch_id', 1);

        $orders = Order::withoutGlobalScope('branch')
            ->where('branch_id', $branchId)
            ->whereNotIn('status', ['SELESAI', 'VOID'])
            ->with([
                'customer:id,name,phone,preferences',
                'servicePackage:id,name,category,unit',
                'washedBy:id,name',
                'ironedBy:id,name',
                'packedBy:id,name',
            ])
            ->orderBy('created_at')
            ->get();

        $statuses = ['ANTREAN', 'CUCI', 'SETRIKA', 'PACKING', 'SIAP_DIAMBIL'];
        $grouped  = [];
        foreach ($statuses as $status) {
            $grouped[$status] = $orders->where('status', $status)->values();
        }

        return $grouped;
    }

    #[Computed]
    public function stageCounts(): array
    {
        $counts = [];
        foreach ($this->boardData as $status => $orders) {
            $counts[$status] = $orders->count();
        }
        return $counts;
    }

    #[Computed]
    public function totalActive(): int
    {
        return array_sum($this->stageCounts);
    }

    // ── Computed: recent SELESAI (last 10) ────────────────────────

    #[Computed]
    public function recentCompleted(): Collection
    {
        return Order::withoutGlobalScope('branch')
            ->where('branch_id', session('active_branch_id', 1))
            ->where('status', 'SELESAI')
            ->with(['customer:id,name', 'servicePackage:id,name'])
            ->orderByDesc('updated_at')
            ->limit(5)
            ->get();
    }

    // ── Computed: employees by role for PIC selectors ─────────────

    #[Computed]
    public function washers(): Collection
    {
        return Employee::where('is_active', true)
            ->whereIn('role', ['OPERATOR_CUCI', 'KASIR'])
            ->orderBy('name')
            ->get(['id', 'name', 'role', 'total_output_kg']);
    }

    #[Computed]
    public function ironists(): Collection
    {
        return Employee::where('is_active', true)
            ->whereIn('role', ['SETRIKA', 'KASIR'])
            ->orderBy('name')
            ->get(['id', 'name', 'role', 'total_output_kg']);
    }

    #[Computed]
    public function allStaff(): Collection
    {
        return Employee::where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'role', 'total_output_kg']);
    }

    // ── Open drawer to advance/update a card ──────────────────────

    public function openUpdateDrawer(int $orderId, string $targetStatus): void
    {
        $order = Order::withoutGlobalScope('branch')
            ->with(['washedBy:id,name', 'ironedBy:id,name', 'packedBy:id,name'])
            ->findOrFail($orderId);

        $this->updatingOrderId = $orderId;
        $this->newStatus       = $targetStatus;
        $this->rackLocation    = $order->rack_location ?? '';

        // Pre-fill current PIC
        $this->picEmployeeId = match ($targetStatus) {
            'CUCI'    => $order->washed_by_id,
            'SETRIKA' => $order->ironed_by_id,
            'PACKING', 'SIAP_DIAMBIL' => $order->packed_by_id,
            default   => null,
        };

        $this->showUpdateDrawer = true;
        unset($this->boardData, $this->stageCounts);
    }

    public function closeUpdateDrawer(): void
    {
        $this->showUpdateDrawer = false;
        $this->updatingOrderId  = null;
        $this->picEmployeeId    = null;
        $this->rackLocation     = '';
    }

    // ── Commit status change ──────────────────────────────────────

    public function commitUpdate(): void
    {
        if (! $this->updatingOrderId || ! $this->newStatus) {
            return;
        }

        $order = Order::withoutGlobalScope('branch')->findOrFail($this->updatingOrderId);

        // Build picData based on which stage we are moving to
        $picData = [];
        if ($this->newStatus === 'CUCI' && $this->picEmployeeId) {
            $picData['washed_by_id'] = $this->picEmployeeId;
        } elseif ($this->newStatus === 'SETRIKA' && $this->picEmployeeId) {
            $picData['ironed_by_id'] = $this->picEmployeeId;
        } elseif (in_array($this->newStatus, ['PACKING', 'SIAP_DIAMBIL']) && $this->picEmployeeId) {
            $picData['packed_by_id'] = $this->picEmployeeId;
        }

        if ($this->rackLocation) {
            $picData['rack_location'] = $this->rackLocation;
        }

        try {
            $this->orderService->updateProductionStatus($order, $this->newStatus, $picData);

            $labels = [
                'CUCI'         => 'Cuci & Kering',
                'SETRIKA'      => 'Setrika',
                'PACKING'      => 'QC & Packing',
                'SIAP_DIAMBIL' => 'Siap Diambil',
            ];

            $msg = "Order {$order->invoice_number} dipindah ke: " . ($labels[$this->newStatus] ?? $this->newStatus);
            if ($this->picEmployeeId) {
                $emp = Employee::find($this->picEmployeeId);
                $msg .= " · PIC: {$emp?->name}";
            }

            $this->dispatch('notify', ['type' => 'success', 'title' => 'Status Diperbarui', 'message' => $msg]);
            $this->closeUpdateDrawer();
            unset($this->boardData, $this->stageCounts, $this->totalActive);

        } catch (ValidationException $e) {
            $this->dispatch('notify', ['type' => 'error', 'title' => 'Gagal', 'message' => collect($e->errors())->flatten()->first()]);
        }
    }

    // ── Quick advance without drawer (ANTREAN → CUCI, etc.) ───────

    public function quickAdvance(int $orderId): void
    {
        $order = Order::withoutGlobalScope('branch')->findOrFail($orderId);

        $next = match ($order->status) {
            'ANTREAN'      => 'CUCI',
            'CUCI'         => 'SETRIKA',
            'SETRIKA'      => 'PACKING',
            'PACKING'      => 'SIAP_DIAMBIL',
            default        => null,
        };

        if (! $next) {
            return;
        }

        // Open drawer with next status pre-filled
        $this->openUpdateDrawer($orderId, $next);
    }

    // ── Mark as SELESAI (customer picked up) ──────────────────────

    public function confirmFinish(int $orderId): void
    {
        $this->finishOrderId     = $orderId;
        $this->showFinishConfirm = true;
    }

    public function markSelesai(): void
    {
        if (! $this->finishOrderId) {
            return;
        }

        DB::transaction(function () {
            Order::withoutGlobalScope('branch')
                ->where('id', $this->finishOrderId)
                ->update(['status' => 'SELESAI']);
        });

        $order = Order::withoutGlobalScope('branch')->find($this->finishOrderId);
        $this->dispatch('notify', ['type' => 'success', 'title' => 'Selesai! 🎉', 'message' => "Order {$order?->invoice_number} sudah diambil pelanggan."]);
        $this->showFinishConfirm = false;
        $this->finishOrderId     = null;
        unset($this->boardData, $this->stageCounts, $this->recentCompleted);
    }

    // ── Reset to ANTREAN (re-wash) ────────────────────────────────

    public function resetToAntrean(int $orderId): void
    {
        $order = Order::withoutGlobalScope('branch')->findOrFail($orderId);
        $qty   = (float) $order->quantity;

        DB::transaction(function () use ($order, $qty) {
            // Kurangi output_kg karyawan yang sudah klaim
            if ($order->washed_by_id) {
                Employee::where('id', $order->washed_by_id)->decrement('total_output_kg', $qty);
            }
            if ($order->ironed_by_id) {
                Employee::where('id', $order->ironed_by_id)->decrement('total_output_kg', $qty);
            }
            if ($order->packed_by_id) {
                Employee::where('id', $order->packed_by_id)->decrement('total_output_kg', $qty);
            }

            $order->update([
                'status'       => 'ANTREAN',
                'washed_by_id' => null,
                'ironed_by_id' => null,
                'packed_by_id' => null,
                'rack_location' => null,
            ]);
        });

        $this->dispatch('notify', ['type' => 'warning', 'title' => 'Reset ke Antrean', 'message' => "Order {$order->invoice_number} dikembalikan ke antrean (cuci ulang)."]);
        unset($this->boardData, $this->stageCounts);
    }

    public function render()
    {
        return view('livewire.production.kanban-board')
            ->layout('layouts.app', ['pageTitle' => 'Kanban Produksi']);
    }
}
