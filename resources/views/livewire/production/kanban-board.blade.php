<div x-data="{
    showDrawer:  @entangle('showUpdateDrawer'),
    showFinish:  @entangle('showFinishConfirm'),
}" class="space-y-4">

{{-- ══ HEADER ══════════════════════════════════════════════════════ --}}
<div class="flex flex-col sm:flex-row sm:items-center gap-3">
    <div>
        <h2 class="text-lg font-bold text-slate-800">Kanban Produksi</h2>
        <p class="text-xs text-slate-400 mt-0.5">
            {{ $this->totalActive }} order aktif · Hari ini
        </p>
    </div>
    <div class="flex items-center gap-2 sm:ml-auto">
        <button wire:click="$refresh" class="btn-icon" wire:loading.class="animate-spin" wire:target="$refresh" title="Refresh">
            <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99"/></svg>
        </button>
        <a href="/pos" wire:navigate class="btn-primary text-sm py-2">
            <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
            Order Baru
        </a>
    </div>
</div>

{{-- ══ MOBILE: Swipeable Stage Pills ════════════════════════════════ --}}
@php
$stageConfig = [
    'ANTREAN'      => ['label' => 'Antrean',    'color' => 'text-slate-600',   'dot' => 'bg-slate-400',   'ring' => 'ring-slate-300'],
    'CUCI'         => ['label' => 'Cuci',       'color' => 'text-sky-700',     'dot' => 'bg-sky-500',     'ring' => 'ring-sky-300'],
    'SETRIKA'      => ['label' => 'Setrika',    'color' => 'text-cyan-700',    'dot' => 'bg-cyan-500',    'ring' => 'ring-cyan-300'],
    'PACKING'      => ['label' => 'Packing',    'color' => 'text-amber-700',   'dot' => 'bg-amber-500',   'ring' => 'ring-amber-300'],
    'SIAP_DIAMBIL' => ['label' => 'Siap Ambil', 'color' => 'text-emerald-700', 'dot' => 'bg-emerald-500', 'ring' => 'ring-emerald-300'],
];
@endphp

<div class="md:hidden overflow-x-auto -mx-4 px-4 pb-2">
    <div class="flex gap-2 w-max">
        @foreach ($stageConfig as $status => $cfg)
        <button wire:click="$set('mobileStage', '{{ $status }}')"
                @class([
                    'flex items-center gap-1.5 px-3.5 py-2 rounded-full text-sm font-semibold whitespace-nowrap transition-all',
                    'bg-white shadow-sm ring-2 ' . $cfg['ring'] . ' ' . $cfg['color'] => $mobileStage === $status,
                    'bg-slate-100 text-slate-500' => $mobileStage !== $status,
                ])>
            <span @class(['size-2 rounded-full shrink-0', $cfg['dot']])></span>
            {{ $cfg['label'] }}
            <span @class(['text-[11px] font-bold px-1.5 py-0.5 rounded-full',
                $mobileStage === $status ? 'bg-white/60' : 'bg-slate-200'])>
                {{ $this->stageCounts[$status] ?? 0 }}
            </span>
        </button>
        @endforeach
    </div>
</div>

{{-- ══ MOBILE: Stage Card List ════════════════════════════════════ --}}
<div class="md:hidden space-y-3">
    @foreach ($this->boardData[$mobileStage] ?? [] as $order)
    @include('livewire.production._kanban-card', ['order' => $order, 'mobile' => true])
    @endforeach
    @if (($this->boardData[$mobileStage] ?? collect())->isEmpty())
    <div class="card p-10 text-center text-slate-400">
        <p class="text-3xl mb-2">✨</p>
        <p class="font-medium">Tidak ada order di tahap ini</p>
    </div>
    @endif
</div>

{{-- ══ DESKTOP: 5-Column Kanban Board ════════════════════════════ --}}
<div class="hidden md:grid md:grid-cols-5 gap-3">
    @foreach ($stageConfig as $status => $cfg)
    @php $columnOrders = $this->boardData[$status] ?? collect(); @endphp
    <div class="flex flex-col gap-2">
        {{-- Column header --}}
        <div class="flex items-center gap-2 px-3 py-2 rounded-xl bg-white border border-slate-200/80">
            <span @class(['size-2 rounded-full shrink-0', $cfg['dot']])></span>
            <span class="text-xs font-bold {{ $cfg['color'] }} flex-1">{{ $cfg['label'] }}</span>
            <span class="text-xs font-bold text-slate-500 bg-slate-100 rounded-full px-2 py-0.5">{{ $columnOrders->count() }}</span>
        </div>

        {{-- Cards --}}
        <div class="space-y-2 min-h-[120px]">
            @foreach ($columnOrders as $order)
            @include('livewire.production._kanban-card', ['order' => $order, 'mobile' => false])
            @endforeach
        </div>
    </div>
    @endforeach
</div>

{{-- ══ RECENTLY COMPLETED ════════════════════════════════════════ --}}
@if ($this->recentCompleted->isNotEmpty())
<div class="card p-4 mt-2">
    <div class="flex items-center justify-between mb-3">
        <p class="text-sm font-bold text-slate-800">Selesai Terbaru</p>
        <span class="badge badge-green">{{ $this->recentCompleted->count() }}</span>
    </div>
    <div class="space-y-2">
        @foreach ($this->recentCompleted as $order)
        <div class="flex items-center gap-3 py-2 border-b border-slate-100 last:border-0">
            <div class="size-8 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold text-slate-800 truncate">{{ $order->customer?->name }}</p>
                <p class="text-xs text-slate-400">{{ $order->invoice_number }} · {{ $order->servicePackage?->name }}</p>
            </div>
            <p class="text-xs text-slate-400 shrink-0">{{ $order->updated_at->diffForHumans() }}</p>
        </div>
        @endforeach
    </div>
</div>
@endif

{{-- ══ UPDATE DRAWER ═══════════════════════════════════════════════ --}}
<div x-show="showDrawer" x-cloak style="display:none">
    <div class="bottom-sheet-overlay" @click="showDrawer = false"></div>

    <div x-show="showDrawer"
         x-transition:enter="transition ease-out duration-300" x-transition:enter-start="translate-y-full" x-transition:enter-end="translate-y-0"
         x-transition:leave="transition ease-in duration-200" x-transition:leave-start="translate-y-0" x-transition:leave-end="translate-y-full"
         class="bottom-sheet md:max-w-md md:mx-auto">
        <div class="bottom-sheet-handle"></div>

        @if ($updatingOrderId)
        @php $updOrder = \App\Models\Order::withoutGlobalScope('branch')->with(['customer','servicePackage'])->find($updatingOrderId); @endphp
        @if ($updOrder)
        <div class="px-5 pb-6 space-y-4">
            {{-- Order info --}}
            <div class="flex items-start justify-between gap-2">
                <div>
                    <p class="font-bold text-slate-800">Update Status</p>
                    <p class="text-xs text-slate-500 mt-0.5">{{ $updOrder->invoice_number }} · {{ $updOrder->customer?->name }}</p>
                    <p class="text-xs text-slate-500">{{ $updOrder->servicePackage?->name }} · {{ $updOrder->quantity }} {{ $updOrder->unit }}</p>
                </div>
                @php $nc = $stageConfig[$newStatus] ?? []; @endphp
                <span @class(['badge text-sm px-3 py-1.5', ($nc['dot'] ?? 'bg-slate-400') . ' text-white'])
                      style="background: {{ match($newStatus) { 'CUCI' => '#0284C7', 'SETRIKA' => '#06B6D4', 'PACKING' => '#F59E0B', 'SIAP_DIAMBIL' => '#10B981', default => '#94a3b8' } }}; color:white; box-shadow:none">
                    ▶ {{ $stageConfig[$newStatus]['label'] ?? $newStatus }}
                </span>
            </div>

            {{-- PIC selector --}}
            @if (in_array($newStatus, ['CUCI', 'SETRIKA', 'PACKING', 'SIAP_DIAMBIL']))
            <div>
                <label class="form-label">
                    @if ($newStatus === 'CUCI') Operator Cuci (PIC)
                    @elseif ($newStatus === 'SETRIKA') Operator Setrika (PIC)
                    @else Staff Packing / Serah Terima @endif
                </label>
                <select wire:model="picEmployeeId" class="form-input">
                    <option value="">— Tidak dipilih —</option>
                    @php $staffList = $newStatus === 'CUCI' ? $this->washers : ($newStatus === 'SETRIKA' ? $this->ironists : $this->allStaff); @endphp
                    @foreach ($staffList as $emp)
                    <option value="{{ $emp->id }}">{{ $emp->name }} ({{ $emp->total_output_kg }} Kg)</option>
                    @endforeach
                </select>
                @if ($picEmployeeId)
                @php $selEmp = $staffList->firstWhere('id', $picEmployeeId); @endphp
                @if ($selEmp)
                <div class="flex items-center gap-2 mt-2 rounded-xl bg-sky-50 border border-sky-100 px-3 py-2">
                    <svg class="size-4 text-sky-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                    <p class="text-xs text-sky-700">
                        <strong>{{ $selEmp->name }}</strong> — akan ditambah <strong>{{ $updOrder->quantity }} Kg</strong> ke output komisi
                    </p>
                </div>
                @endif
                @endif
            </div>
            @endif

            {{-- Rack location (for PACKING & SIAP_DIAMBIL) --}}
            @if (in_array($newStatus, ['PACKING', 'SIAP_DIAMBIL']))
            <div>
                <label class="form-label">Lokasi Rak (Smart Rack)</label>
                <input type="text" wire:model="rackLocation"
                       placeholder="cth. Rak A-03, Gantung B-12"
                       class="form-input">
            </div>
            @endif

            {{-- Actions --}}
            <div class="flex gap-3 pt-2">
                <button wire:click="commitUpdate"
                        wire:loading.attr="disabled"
                        wire:target="commitUpdate"
                        class="btn-primary flex-1 justify-center py-3">
                    <span wire:loading.remove wire:target="commitUpdate">Konfirmasi Update</span>
                    <span wire:loading wire:target="commitUpdate" class="flex items-center gap-2">
                        <svg class="size-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                        Memproses…
                    </span>
                </button>
                <button @click="showDrawer = false" class="btn-secondary">Batal</button>
            </div>
        </div>
        @endif
        @endif
    </div>
</div>

{{-- ══ SELESAI CONFIRM ════════════════════════════════════════════ --}}
<div x-show="showFinish" x-cloak style="display:none">
    <div class="dialog-overlay">
        <div x-show="showFinish"
             x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
             class="dialog-panel p-6">
            <div class="text-center mb-5">
                <div class="text-4xl mb-3">🎉</div>
                <p class="font-bold text-slate-800 text-lg">Tandai Selesai?</p>
                <p class="text-sm text-slate-500 mt-2">Cucian sudah diambil oleh pelanggan. Order akan ditutup.</p>
            </div>
            <div class="flex gap-3">
                <button wire:click="markSelesai" class="btn-primary flex-1 justify-center">Ya, Selesai!</button>
                <button @click="showFinish = false" class="btn-secondary flex-1 justify-center">Batal</button>
            </div>
        </div>
    </div>
</div>

</div>
