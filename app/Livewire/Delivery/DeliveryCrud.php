<?php

namespace App\Livewire\Delivery;

use App\Models\CourierTask;
use App\Models\Customer;
use App\Models\Employee;
use App\Models\Order;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Component;

class DeliveryCrud extends Component
{
    // ── Tabs ───────────────────────────────────────────────────────
    public string $activeTab = 'jemput'; // jemput | antar | semua | riwayat

    // ── Filter & Search ────────────────────────────────────────────
    public string $search        = '';
    public string $filterStatus  = '';
    public string $filterCourier = '';
    public string $filterDate    = 'all'; // all | today | tomorrow

    // ── Form Modal ─────────────────────────────────────────────────
    public bool    $showForm       = false;
    public ?int    $editingId      = null;
    public string  $taskType       = 'JEMPUT';
    public ?int    $customerId     = null;
    public ?int    $orderId        = null;
    public ?int    $courierId      = null;
    public string  $customerName   = '';
    public string  $customerPhone  = '';
    public string  $address        = '';
    public string  $mapsUrl        = '';
    public string  $scheduledDate  = '';
    public string  $timeSlot       = 'PAGI';
    public int     $deliveryFee    = 0;
    public ?float  $estimatedKg    = null;
    public string  $notes          = '';

    // ── Delete Confirm ─────────────────────────────────────────────
    public bool $showDeleteConfirm = false;
    public ?int $deletingId        = null;

    public function mount(): void
    {
        $this->scheduledDate = today()->toDateString();
    }

    // ─── Computed Properties ───────────────────────────────────────

    #[Computed]
    public function couriers(): Collection
    {
        return Employee::where('role', 'KURIR')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
    }

    #[Computed]
    public function allStaff(): Collection
    {
        return Employee::where('is_active', true)
            ->orderBy('name')
            ->get();
    }

    #[Computed]
    public function readyOrders(): Collection
    {
        return Order::with('customer')
            ->whereIn('status', [Order::STATUS_SIAP_DIAMBIL, Order::STATUS_PACKING])
            ->latest()
            ->limit(20)
            ->get();
    }

    #[Computed]
    public function customers(): Collection
    {
        return Customer::orderBy('name')->limit(50)->get();
    }

    #[Computed]
    public function counts(): array
    {
        $base = CourierTask::query();
        return [
            'jemput_pending' => (clone $base)->where('task_type', CourierTask::TYPE_JEMPUT)->whereIn('status', [CourierTask::STATUS_MENUNGGU, CourierTask::STATUS_DIPROSES])->count(),
            'antar_pending'  => (clone $base)->where('task_type', CourierTask::TYPE_ANTAR)->whereIn('status', [CourierTask::STATUS_MENUNGGU, CourierTask::STATUS_DIPROSES])->count(),
            'today_total'    => (clone $base)->whereDate('scheduled_date', today())->count(),
            'completed'      => (clone $base)->where('status', CourierTask::STATUS_SELESAI)->count(),
        ];
    }

    #[Computed]
    public function tasks()
    {
        $query = CourierTask::with(['courier', 'customer', 'order'])
            ->latest('scheduled_date')
            ->latest('id');

        // Tab filter
        if ($this->activeTab === 'jemput') {
            $query->where('task_type', CourierTask::TYPE_JEMPUT)
                  ->whereIn('status', [CourierTask::STATUS_MENUNGGU, CourierTask::STATUS_DIPROSES]);
        } elseif ($this->activeTab === 'antar') {
            $query->where('task_type', CourierTask::TYPE_ANTAR)
                  ->whereIn('status', [CourierTask::STATUS_MENUNGGU, CourierTask::STATUS_DIPROSES]);
        } elseif ($this->activeTab === 'riwayat') {
            $query->whereIn('status', [CourierTask::STATUS_SELESAI, CourierTask::STATUS_BATAL]);
        }

        // Status filter
        if ($this->filterStatus !== '') {
            $query->where('status', $this->filterStatus);
        }

        // Courier filter
        if ($this->filterCourier !== '') {
            $query->where('courier_id', $this->filterCourier);
        }

        // Date filter
        if ($this->filterDate === 'today') {
            $query->whereDate('scheduled_date', today());
        } elseif ($this->filterDate === 'tomorrow') {
            $query->whereDate('scheduled_date', today()->addDay());
        }

        // Search
        if ($this->search !== '') {
            $s = '%' . $this->search . '%';
            $query->where(function ($q) use ($s) {
                $q->where('customer_name', 'like', $s)
                  ->orWhere('customer_phone', 'like', $s)
                  ->orWhere('address', 'like', $s)
                  ->orWhereHas('order', fn ($oq) => $oq->where('invoice_number', 'like', $s));
            });
        }

        return $query->paginate(15);
    }

    // ─── Actions ───────────────────────────────────────────────────

    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
    }

    public function openCreate(string $type = 'JEMPUT'): void
    {
        $this->resetForm();
        $this->taskType = $type;
        $this->scheduledDate = today()->toDateString();
        
        // Auto assign courier if only one
        if ($this->couriers->count() === 1) {
            $this->courierId = $this->couriers->first()->id;
        }

        $this->showForm = true;
    }

    public function openEdit(int $id): void
    {
        $task = CourierTask::findOrFail($id);
        $this->editingId     = $task->id;
        $this->taskType      = $task->task_type;
        $this->customerId    = $task->customer_id;
        $this->orderId       = $task->order_id;
        $this->courierId     = $task->courier_id;
        $this->customerName  = $task->customer_name;
        $this->customerPhone = $task->customer_phone;
        $this->address       = $task->address;
        $this->mapsUrl       = $task->maps_url ?? '';
        $this->scheduledDate = $task->scheduled_date->toDateString();
        $this->timeSlot      = $task->time_slot;
        $this->deliveryFee   = $task->delivery_fee;
        $this->estimatedKg   = $task->estimated_kg;
        $this->notes         = $task->notes ?? '';

        $this->showForm = true;
    }

    public function closeForm(): void
    {
        $this->showForm = false;
        $this->resetForm();
    }

    public function onCustomerSelected(?int $id): void
    {
        if (!$id) return;
        $c = Customer::find($id);
        if ($c) {
            $this->customerId    = $c->id;
            $this->customerName  = $c->name;
            $this->customerPhone = $c->phone ?? '';
            $this->address       = $c->address ?? '';
        }
    }

    public function onOrderSelected(?int $id): void
    {
        if (!$id) return;
        $order = Order::with('customer')->find($id);
        if ($order) {
            $this->orderId = $order->id;
            if ($order->customer) {
                $this->customerId    = $order->customer->id;
                $this->customerName  = $order->customer->name;
                $this->customerPhone = $order->customer->phone ?? '';
                $this->address       = $order->customer->address ?? '';
            }
            if ($order->quantity) {
                $this->estimatedKg = (float) $order->quantity;
            }
        }
    }

    public function save(): void
    {
        $this->validate([
            'taskType'      => 'required|in:JEMPUT,ANTAR',
            'customerName'  => 'required|string|max:100',
            'customerPhone' => 'required|string|max:30',
            'address'       => 'required|string',
            'scheduledDate' => 'required|date',
            'timeSlot'      => 'required|in:PAGI,SIANG,SORE,MALAM',
            'deliveryFee'   => 'numeric|min:0',
            'estimatedKg'   => 'nullable|numeric|min:0',
        ]);

        $data = [
            'task_type'      => $this->taskType,
            'customer_id'    => $this->customerId,
            'order_id'       => $this->orderId,
            'courier_id'     => $this->courierId,
            'customer_name'  => $this->customerName,
            'customer_phone' => $this->customerPhone,
            'address'        => $this->address,
            'maps_url'       => $this->mapsUrl ?: null,
            'scheduled_date' => $this->scheduledDate,
            'time_slot'      => $this->timeSlot,
            'delivery_fee'   => (int) $this->deliveryFee,
            'estimated_kg'   => $this->estimatedKg,
            'notes'          => $this->notes ?: null,
        ];

        DB::transaction(function () use ($data) {
            if ($this->editingId) {
                $task = CourierTask::findOrFail($this->editingId);
                $task->update($data);
            } else {
                $data['status'] = CourierTask::STATUS_MENUNGGU;
                CourierTask::create($data);
            }
        });

        $this->dispatch('notify', [
            'type'    => 'success',
            'title'   => 'Berhasil Disimpan',
            'message' => 'Tugas ' . ($this->taskType === 'JEMPUT' ? 'Penjemputan' : 'Pengantaran') . ' telah diperbarui.',
        ]);

        $this->closeForm();
    }

    public function updateStatus(int $id, string $newStatus): void
    {
        $task = CourierTask::findOrFail($id);

        $data = ['status' => $newStatus];
        if ($newStatus === CourierTask::STATUS_SELESAI) {
            $data['completed_at'] = now();

            // If Antar completed and has order, mark order as SELESAI
            if ($task->task_type === CourierTask::TYPE_ANTAR && $task->order_id) {
                $task->order?->update(['status' => Order::STATUS_SELESAI]);
            }
        }

        $task->update($data);

        $statusLabels = [
            CourierTask::STATUS_DIPROSES => 'sedang diproses oleh kurir',
            CourierTask::STATUS_SELESAI  => 'telah diselesaikan',
            CourierTask::STATUS_BATAL    => 'telah dibatalkan',
            CourierTask::STATUS_MENUNGGU => 'kembali menunggu penjemputan/antar',
        ];

        $this->dispatch('notify', [
            'type'    => $newStatus === CourierTask::STATUS_BATAL ? 'warning' : 'success',
            'title'   => 'Status Diperbarui',
            'message' => 'Tugas ' . ($statusLabels[$newStatus] ?? $newStatus) . '.',
        ]);
    }

    public function assignCourier(int $id, ?int $courierId): void
    {
        $task = CourierTask::findOrFail($id);
        $task->update(['courier_id' => $courierId]);

        $this->dispatch('notify', [
            'type'    => 'info',
            'title'   => 'Kurir Ditugaskan',
            'message' => 'Tugas telah dialokasikan ke kurir terpilih.',
        ]);
    }

    public function confirmDelete(int $id): void
    {
        $this->deletingId        = $id;
        $this->showDeleteConfirm = true;
    }

    public function deleteConfirmed(): void
    {
        if ($this->deletingId) {
            CourierTask::destroy($this->deletingId);
            $this->dispatch('notify', [
                'type'    => 'success',
                'title'   => 'Tugas Dihapus',
                'message' => 'Data antar-jemput berhasil dihapus.',
            ]);
        }
        $this->showDeleteConfirm = false;
        $this->deletingId        = null;
    }

    public function getWaLink(CourierTask $task): string
    {
        $phone = preg_replace('/[^0-9]/', '', $task->customer_phone);
        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        }

        $type = $task->task_type === 'JEMPUT' ? 'penjemputan cucian kotor' : 'pengantaran cucian bersih';
        $msg = "Halo Kak {$task->customer_name},\nKurir Kee Laundry siap melakukan {$type} ke alamat:\n📍 {$task->address}\n\nJadwal: " . $task->scheduled_date->format('d/m/Y') . " ({$task->time_slot})\nMohon konfirmasinya ya Kak. Terima kasih! 🙏";

        return "https://wa.me/{$phone}?text=" . rawurlencode($msg);
    }

    public function getMapsLink(CourierTask $task): string
    {
        if ($task->maps_url) {
            return $task->maps_url;
        }
        return 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode($task->address);
    }

    protected function resetForm(): void
    {
        $this->editingId     = null;
        $this->customerId    = null;
        $this->orderId       = null;
        $this->courierId     = null;
        $this->customerName  = '';
        $this->customerPhone = '';
        $this->address       = '';
        $this->mapsUrl       = '';
        $this->scheduledDate = today()->toDateString();
        $this->timeSlot      = 'PAGI';
        $this->deliveryFee   = 0;
        $this->estimatedKg   = null;
        $this->notes         = '';
    }

    public function render()
    {
        return view('livewire.delivery.delivery-crud', [
            'tasks' => $this->tasks,
        ])->layout('layouts.app', ['pageTitle' => 'Antar Jemput Laundry']);
    }
}
