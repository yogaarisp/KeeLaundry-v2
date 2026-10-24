<div x-data="{
    showDrawer:  @entangle('showUpdateDrawer'),
    showFinish:  @entangle('showFinishConfirm'),
}" class="space-y-4">

{{-- ══ HEADER ══════════════════════════════════════════════════════ --}}
<div class="flex items-center justify-between gap-3">
    <div>
        <h2 class="text-lg font-bold text-slate-800">Kanban Produksi</h2>
        <p class="text-xs text-slate-400 mt-0.5">
            {{ $this->totalActive }} order aktif · Hari ini
        </p>
    </div>
    <div class="flex items-center gap-2 shrink-0">
        <button wire:click="$refresh" class="btn-icon" wire:loading.class="animate-spin" wire:target="$refresh" title="Refresh">
            <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99"/></svg>
        </button>
        <a href="/pos" wire:navigate class="btn-primary text-sm py-2">
            <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
            <span class="hidden sm:inline">Order Baru</span>
            <span class="sm:hidden">Order</span>
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

<div class="md:hidden overflow-x-auto scrollbar-none -mx-4 px-4 pb-1">
    <div class="flex gap-2 w-max">
        @foreach ($stageConfig as $status => $cfg)
        <button wire:click="$set('mobileStage', '{{ $status }}')"
                @class([
                    'flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition-all shadow-xs',
                    'bg-slate-900 text-white ring-2 ring-slate-900' => $mobileStage === $status,
                    'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50' => $mobileStage !== $status,
                ])>
            <span @class(['size-2 rounded-full shrink-0', $cfg['dot']])></span>
            {{ $cfg['label'] }}
            <span @class([
                'text-[10px] font-bold px-1.5 py-0.5 rounded-full',
                $mobileStage === $status ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-600'
            ])>
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
        <p class="font-medium text-sm">Tidak ada order di tahap ini</p>
    </div>
    @endif
</div>

{{-- ══ DESKTOP: 5-Column Kanban Board ════════════════════════════ --}}
<div class="hidden md:grid md:grid-cols-5 gap-3.5 items-start">
    @foreach ($stageConfig as $status => $cfg)
    @php $columnOrders = $this->boardData[$status] ?? collect(); @endphp
    <div class="flex flex-col gap-2.5 bg-slate-100/70 p-2.5 rounded-2xl border border-slate-200/60 min-h-[550px]">
        {{-- Column header --}}
        <div class="flex items-center gap-2 px-3 py-2 rounded-xl bg-white border border-slate-200/80 shadow-xs">
            <span @class(['size-2 rounded-full shrink-0', $cfg['dot']])></span>
            <span class="text-xs font-bold text-slate-700 flex-1 truncate">{{ $cfg['label'] }}</span>
            <span class="text-xs font-bold text-slate-500 bg-slate-100 rounded-full px-2 py-0.5">{{ $columnOrders->count() }}</span>
        </div>

        {{-- Cards --}}
        <div class="space-y-2.5 flex-1">
            @foreach ($columnOrders as $order)
            @include('livewire.production._kanban-card', ['order' => $order, 'mobile' => false])
            @endforeach

            @if ($columnOrders->isEmpty())
            <div class="h-32 flex flex-col items-center justify-center text-center p-4 border border-dashed border-slate-200 rounded-xl text-slate-400">
                <p class="text-xs font-medium">Belum ada order</p>
            </div>
            @endif
        </div>
    </div>
    @endforeach
</div>

{{-- ══ RECENTLY COMPLETED ════════════════════════════════════════ --}}
@if ($this->recentCompleted->isNotEmpty())
<div class="card p-4 mt-2">
    <div class="flex items-center justify-between mb-3">
        <div>
            <p class="text-sm font-bold text-slate-800">Selesai Terbaru</p>
            <p class="text-xs text-slate-400">5 cucian terakhir yang sudah diambil pelanggan</p>
        </div>
        <span class="badge badge-green font-semibold">{{ $this->recentCompleted->count() }}</span>
    </div>
    <div class="divide-y divide-slate-100">
        @foreach ($this->recentCompleted as $order)
        <div class="flex items-center gap-3 py-2.5 first:pt-0 last:pb-0">
            <div class="size-8 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-bold text-slate-800 truncate">{{ $order->customer?->name }}</p>
                <p class="text-xs text-slate-400 mt-0.5">{{ $order->invoice_number }} · {{ $order->servicePackage?->name }}</p>
            </div>
            <p class="text-xs text-slate-400 shrink-0 font-medium">{{ $order->updated_at->diffForHumans() }}</p>
        </div>
        @endforeach
    </div>
</div>
@endif

{{-- ══ UPDATE DRAWER / BOTTOM SHEET ════════════════════════════════ --}}
<div x-show="showDrawer" x-cloak style="display:none">
    <div class="bottom-sheet-overlay" @click="showDrawer = false"></div>

    <div x-show="showDrawer"
         x-transition:enter="transition ease-out duration-300" x-transition:enter-start="translate-y-full md:opacity-0 md:scale-95" x-transition:enter-end="translate-y-0 md:opacity-100 md:scale-100"
         x-transition:leave="transition ease-in duration-200" x-transition:leave-start="translate-y-0 md:opacity-100 md:scale-100" x-transition:leave-end="translate-y-full md:opacity-0 md:scale-95"
         class="bottom-sheet md:max-w-md md:mx-auto">
        
        <div class="bottom-sheet-handle md:hidden"></div>

        @if ($updatingOrderId)
        @php $updOrder = \App\Models\Order::withoutGlobalScope('branch')->with(['customer','servicePackage'])->find($updatingOrderId); @endphp
        @if ($updOrder)
        <div class="p-5 space-y-4">
            {{-- Header with Mobile Back Arrow and Desktop Close Button --}}
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2">
                    <button type="button" @click="showDrawer = false" class="md:hidden p-1.5 -ml-1 text-slate-400 hover:text-slate-600 rounded-lg hover:bg-slate-100">
                        <svg class="size-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/></svg>
                    </button>
                    <div>
                        <h3 class="font-bold text-slate-800 text-base">Update Tahap Produksi</h3>
                        <p class="text-xs text-slate-400">{{ $updOrder->invoice_number }}</p>
                    </div>
                </div>
                <button type="button" @click="showDrawer = false" class="hidden md:flex p-1.5 text-slate-400 hover:text-slate-600 rounded-lg hover:bg-slate-100">
                    <svg class="size-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
                </button>
            </div>

            {{-- Target Stage Badge Card --}}
            <div class="bg-slate-50 border border-slate-200/80 rounded-2xl p-3.5 space-y-2">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-bold text-slate-800">{{ $updOrder->customer?->name }}</p>
                        <p class="text-xs text-slate-500 mt-0.5">{{ $updOrder->servicePackage?->name }} · {{ $updOrder->quantity }} {{ $updOrder->unit }}</p>
                    </div>
                    @php $nc = $stageConfig[$newStatus] ?? []; @endphp
                    <span class="badge text-xs px-3 py-1.5 font-bold text-white shadow-xs"
                          style="background: {{ match($newStatus) { 'CUCI' => '#0284C7', 'SETRIKA' => '#06B6D4', 'PACKING' => '#F59E0B', 'SIAP_DIAMBIL' => '#10B981', default => '#64748b' } }}">
                        ▶ {{ $stageConfig[$newStatus]['label'] ?? $newStatus }}
                    </span>
                </div>
            </div>

            {{-- PIC selector --}}
            @if (in_array($newStatus, ['CUCI', 'SETRIKA', 'PACKING', 'SIAP_DIAMBIL']))
            <div class="space-y-1.5">
                <label class="form-label text-xs font-semibold text-slate-700">
                    @if ($newStatus === 'CUCI') Operator Cuci (PIC)
                    @elseif ($newStatus === 'SETRIKA') Operator Setrika (PIC)
                    @else Staff Packing / Serah Terima @endif
                </label>
                <select wire:model="picEmployeeId" class="form-input text-sm">
                    <option value="">— Tidak dipilih —</option>
                    @php $staffList = $newStatus === 'CUCI' ? $this->washers : ($newStatus === 'SETRIKA' ? $this->ironists : $this->allStaff); @endphp
                    @foreach ($staffList as $emp)
                    <option value="{{ $emp->id }}">{{ $emp->name }} (Output: {{ $emp->total_output_kg }} Kg)</option>
                    @endforeach
                </select>
                @if ($picEmployeeId)
                @php $selEmp = $staffList->firstWhere('id', $picEmployeeId); @endphp
                @if ($selEmp)
                <div class="flex items-center gap-2 mt-2 rounded-xl bg-sky-50 border border-sky-100 px-3 py-2">
                    <svg class="size-4 text-sky-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                    <p class="text-xs text-sky-700">
                        <strong>{{ $selEmp->name }}</strong> — komisi bertambah <strong>{{ $updOrder->quantity }} Kg</strong>
                    </p>
                </div>
                @endif
                @endif
            </div>
            @endif

            {{-- Rack location (for PACKING & SIAP_DIAMBIL) --}}
            @if (in_array($newStatus, ['PACKING', 'SIAP_DIAMBIL']))
            <div class="space-y-1.5">
                <label class="form-label text-xs font-semibold text-slate-700">Lokasi Rak Simpan (Smart Rack)</label>
                <input type="text" wire:model="rackLocation"
                       placeholder="cth. Rak A-03, Gantung B-12"
                       class="form-input text-sm">
            </div>
            @endif

            {{-- Action Button (Full Width, Standard CTA) --}}
            <div class="pt-2">
                <button wire:click="commitUpdate"
                        wire:loading.attr="disabled"
                        wire:target="commitUpdate"
                        class="btn-primary w-full justify-center py-3 text-sm font-bold shadow-md">
                    <span wire:loading.remove wire:target="commitUpdate">Konfirmasi Pindah Tahap</span>
                    <span wire:loading wire:target="commitUpdate" class="flex items-center gap-2">
                        <svg class="size-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                        Memproses…
                    </span>
                </button>
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
             class="dialog-panel p-6 relative max-w-sm w-full">
            <button @click="showFinish = false" class="absolute top-4 right-4 text-slate-400 hover:text-slate-600 p-1 rounded-lg hover:bg-slate-100">
                <svg class="size-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
            </button>
            <div class="text-center my-3">
                <div class="size-14 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center mx-auto mb-3 text-2xl shadow-xs">
                    🎉
                </div>
                <h3 class="font-bold text-slate-800 text-lg">Tandai Cucian Selesai?</h3>
                <p class="text-xs text-slate-500 mt-1.5 leading-relaxed">Cucian sudah diserahkan / diambil oleh pelanggan. Order akan ditutup dari antrean aktif.</p>
            </div>
            <div class="pt-3">
                <button wire:click="markSelesai"
                        wire:loading.attr="disabled"
                        wire:target="markSelesai"
                        class="btn-primary w-full justify-center py-3 bg-emerald-600 hover:bg-emerald-700 text-sm font-bold shadow-md">
                    <span wire:loading.remove wire:target="markSelesai">Ya, Cucian Selesai Diambil</span>
                    <span wire:loading wire:target="markSelesai" class="flex items-center gap-2">
                        <svg class="size-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                        Menyimpan…
                    </span>
                </button>
            </div>
        </div>
    </div>
</div>

</div>
