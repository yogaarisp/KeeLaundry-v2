<?php

namespace App\Livewire\Public;

use App\Models\Branch;
use App\Models\Order;
use Illuminate\Support\Carbon;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;

class MobileOrderTracker extends Component
{
    /** Invoice number dari URL ?invoice=INV-... */
    #[Url(as: 'invoice', except: '')]
    public string $invoiceInput = '';

    /** Yang sedang di-track (setelah search) */
    public string $trackedInvoice = '';

    /** Apakah sudah pernah search (untuk show state) */
    public bool $searched = false;

    public function mount(): void
    {
        // Jika ada ?invoice= di URL, langsung track
        if ($this->invoiceInput) {
            $this->trackedInvoice = strtoupper(trim($this->invoiceInput));
            $this->searched = true;
        }
    }

    // ── Computed: Tracked Order ────────────────────────────────────

    #[Computed]
    public function order(): ?Order
    {
        if (! $this->trackedInvoice) {
            return null;
        }

        return Order::withoutGlobalScope('branch')
            ->withoutGlobalScopes()
            ->with(['customer:id,name,phone,tier', 'servicePackage:id,name,category,estimated_hours'])
            ->where('invoice_number', $this->trackedInvoice)
            ->whereNotIn('status', ['VOID'])
            ->first();
    }

    #[Computed]
    public function branch(): ?Branch
    {
        $order = $this->order;
        return $order ? Branch::find($order->branch_id) : Branch::first();
    }

    // ── Status progression data ────────────────────────────────────

    #[Computed]
    public function progressSteps(): array
    {
        $steps = [
            ['key' => 'ANTREAN',      'icon' => '📥', 'label' => 'Order Diterima',  'desc' => 'Cucian sedang dalam antrian'],
            ['key' => 'CUCI',         'icon' => '🫧', 'label' => 'Proses Cuci',     'desc' => 'Sedang dicuci dan dikeringkan'],
            ['key' => 'SETRIKA',      'icon' => '♨️', 'label' => 'Proses Setrika',  'desc' => 'Sedang disetrika dengan rapi'],
            ['key' => 'PACKING',      'icon' => '📦', 'label' => 'QC & Packing',    'desc' => 'Pengecekan kualitas & pengemasan'],
            ['key' => 'SIAP_DIAMBIL', 'icon' => '✅', 'label' => 'Siap Diambil',    'desc' => 'Cucian siap diambil di toko'],
            ['key' => 'SELESAI',      'icon' => '🎉', 'label' => 'Selesai',          'desc' => 'Cucian sudah diambil'],
        ];

        $order = $this->order;
        if (! $order) {
            return $steps;
        }

        $statusOrder = ['ANTREAN', 'CUCI', 'SETRIKA', 'PACKING', 'SIAP_DIAMBIL', 'SELESAI'];
        $currentIndex = array_search($order->status, $statusOrder);
        if ($currentIndex === false) {
            $currentIndex = -1;
        }

        foreach ($steps as $i => &$step) {
            $stepIndex = array_search($step['key'], $statusOrder);
            $step['done']    = $stepIndex !== false && $stepIndex < $currentIndex;
            $step['active']  = $step['key'] === $order->status;
            $step['pending'] = $stepIndex !== false && $stepIndex > $currentIndex;
        }

        return $steps;
    }

    #[Computed]
    public function estimatedReady(): ?string
    {
        $order = $this->order;
        if (! $order || ! $order->servicePackage) {
            return null;
        }

        $hours = $order->servicePackage->estimated_hours ?? 48;
        $eta   = $order->created_at->addHours($hours);

        if ($eta->isPast()) {
            return null; // sudah lewat, tidak tampilkan
        }

        return $eta->locale('id')->isoFormat('dddd, D MMMM YYYY [pukul] HH:mm');
    }

    #[Computed]
    public function timeElapsed(): string
    {
        $order = $this->order;
        if (! $order) {
            return '';
        }

        return $order->created_at->locale('id')->diffForHumans();
    }

    // ── Actions ───────────────────────────────────────────────────

    public function track(): void
    {
        $this->trackedInvoice = strtoupper(trim($this->invoiceInput));
        $this->searched       = true;
        unset($this->order, $this->progressSteps, $this->estimatedReady);
    }

    public function resetSearch(): void
    {
        $this->invoiceInput    = '';
        $this->trackedInvoice  = '';
        $this->searched        = false;
        $this->resetValidation();
        unset($this->order);
    }

    public function render()
    {
        return view('livewire.public.mobile-order-tracker')
            ->layout('layouts.public', [
                'title' => 'Lacak Cucian – ' . (Branch::first()?->name ?? 'Kee Laundry'),
            ]);
    }
}
