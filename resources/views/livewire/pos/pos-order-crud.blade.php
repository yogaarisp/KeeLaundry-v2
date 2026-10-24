<div x-data="{
    showReceipt:   @entangle('showReceipt'),
    showEdit:      @entangle('showEditOrder'),
    showVoid:      @entangle('showVoidConfirm'),
    showQuick:     @entangle('showQuickCustomer'),
}" class="space-y-0">

{{-- ══ TABS ═════════════════════════════════════════════════════════ --}}
<div class="flex gap-1 bg-slate-100 rounded-xl p-1 mb-5 w-full sm:w-fit">
    <button wire:click="$set('activeTab','pos')"
            @class(['flex-1 sm:flex-initial px-4 sm:px-5 py-2 rounded-lg text-sm font-semibold transition-all duration-150 text-center',
                    'bg-white shadow-sm text-sky-700' => $activeTab === 'pos',
                    'text-slate-500 hover:text-slate-700' => $activeTab !== 'pos'])>
        🛒 Kasir POS
    </button>
    <button wire:click="$set('activeTab','history')"
            @class(['flex-1 sm:flex-initial px-4 sm:px-5 py-2 rounded-lg text-sm font-semibold transition-all duration-150 text-center',
                    'bg-white shadow-sm text-sky-700' => $activeTab === 'history',
                    'text-slate-500 hover:text-slate-700' => $activeTab !== 'history'])>
        📋 Riwayat Order
    </button>
</div>

{{-- ══ TAB: KASIR POS ══════════════════════════════════════════════ --}}
@if ($activeTab === 'pos')
<div class="grid grid-cols-1 lg:grid-cols-5 gap-5">

    {{-- ── LEFT: Order Form (3/5) ─────────────────────────────────── --}}
    <div class="lg:col-span-3 space-y-4">

        {{-- Customer selector --}}
        <div class="card p-4 space-y-3">
            <div class="flex items-center justify-between">
                <p class="text-sm font-bold text-slate-800">1. Pilih Pelanggan</p>
                <button wire:click="$toggle('showQuickCustomer')" class="btn-secondary text-xs py-1.5 px-3 gap-1">
                    <svg class="size-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M18 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0ZM3 19.235v-.11a6.375 6.375 0 0 1 12.75 0v.109A12.318 12.318 0 0 1 9.374 21c-2.331 0-4.512-.645-6.374-1.766Z"/></svg>
                    Pelanggan Baru
                </button>
            </div>

            @if ($selectedCustomer)
                {{-- Selected customer banner --}}
                <div class="flex items-center gap-3 rounded-xl bg-sky-50 border border-sky-200 p-3">
                    <div class="size-10 rounded-full bg-sky-100 flex items-center justify-center shrink-0">
                        <span class="font-bold text-sky-700 text-sm">{{ mb_strtoupper(mb_substr($selectedCustomer->name, 0, 1)) }}</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2">
                            <p class="font-semibold text-slate-800 truncate">{{ $selectedCustomer->name }}</p>
                            <span @class(['badge text-[10px]',
                                'badge-sky' => $selectedCustomer->tier === 'VIP',
                                'badge-slate' => $selectedCustomer->tier !== 'VIP'])>{{ $selectedCustomer->tier }}</span>
                        </div>
                        <p class="text-xs text-slate-500">{{ $selectedCustomer->phone }}</p>
                        @if ($selectedCustomer->deposit_kg > 0)
                            <p class="text-xs text-emerald-600 font-semibold mt-0.5">💰 Deposit: {{ $selectedCustomer->deposit_kg }} Kg</p>
                        @endif
                        @if ($selectedCustomer->preferences)
                            <div class="flex items-start gap-1.5 mt-1.5 bg-amber-50 rounded-lg px-2 py-1.5">
                                <span class="text-amber-500 shrink-0 mt-0.5">⭐</span>
                                <p class="text-xs text-amber-700 italic">{{ $selectedCustomer->preferences }}</p>
                            </div>
                        @endif
                    </div>
                    <button wire:click="clearCustomer" class="btn-icon shrink-0">
                        <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            @else
                {{-- Search box --}}
                <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 size-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/></svg>
                    <input type="text"
                           wire:model.live.debounce.250ms="customerSearch"
                           placeholder="Cari nama atau nomor HP…"
                           class="form-input pl-9"
                           @focus="open = true">
                    {{-- Dropdown --}}
                    @if ($this->customerResults->isNotEmpty())
                    <div x-show="open" class="absolute z-30 w-full mt-1 bg-white rounded-2xl border border-slate-200 shadow-xl overflow-hidden">
                        @foreach ($this->customerResults as $c)
                        <button wire:click="selectCustomer({{ $c->id }})"
                                @click="open = false"
                                class="w-full flex items-center gap-3 px-4 py-3 hover:bg-sky-50 transition-colors text-left">
                            <div class="size-8 rounded-full bg-sky-100 flex items-center justify-center shrink-0">
                                <span class="text-xs font-bold text-sky-700">{{ mb_strtoupper(mb_substr($c->name, 0, 1)) }}</span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-slate-800">{{ $c->name }}</p>
                                <p class="text-xs text-slate-400">{{ $c->phone }}
                                    @if ($c->deposit_kg > 0) · <span class="text-emerald-600">{{ $c->deposit_kg }} Kg deposit</span> @endif
                                </p>
                            </div>
                            <span @class(['badge text-[10px]',
                                'badge-sky' => $c->tier === 'VIP',
                                'badge-slate' => $c->tier !== 'VIP'])>{{ $c->tier }}</span>
                        </button>
                        @endforeach
                    </div>
                    @endif
                </div>
                @error('selectedCustomerId') <p class="form-error">{{ $message }}</p> @enderror
            @endif
        </div>

        {{-- Package selector --}}
        <div class="card p-4 space-y-3">
            <p class="text-sm font-bold text-slate-800">2. Pilih Layanan</p>
            @foreach ($this->activePackages->groupBy('category') as $cat => $pkgs)
            <div>
                @php $catColor = match($cat) { 'KILOAN' => 'badge-sky', 'EXPRESS' => 'badge-amber', default => 'badge-cyan' }; @endphp
                <div class="flex items-center gap-2 mb-2">
                    <span class="badge {{ $catColor }} text-[10px]">{{ $cat }}</span>
                    <div class="flex-1 h-px bg-slate-100"></div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                    @foreach ($pkgs as $pkg)
                    <button wire:click="selectPackage({{ $pkg->id }})"
                            @class([
                                'text-left p-3 rounded-xl border-2 transition-all duration-150',
                                'border-sky-400 bg-sky-50' => $selectedPackageId === $pkg->id,
                                'border-slate-200 bg-white hover:border-sky-200 hover:bg-sky-50/50' => $selectedPackageId !== $pkg->id,
                            ])>
                        <p class="text-sm font-semibold text-slate-800">{{ $pkg->name }}</p>
                        <div class="flex items-center justify-between mt-1">
                            <p class="text-base font-bold text-sky-600">Rp {{ number_format($pkg->price_per_unit) }}<span class="text-xs font-normal text-slate-400">/{{ $pkg->unit }}</span></p>
                            <p class="text-xs text-slate-400">~{{ $pkg->estimated_hours }}j</p>
                        </div>
                        <p class="text-[10px] text-slate-400 mt-1">
                            D:{{ $pkg->detergent_ml_per_unit }} S:{{ $pkg->softener_ml_per_unit }} P:{{ $pkg->perfume_ml_per_unit }} ml/{{ $pkg->unit }}
                        </p>
                    </button>
                    @endforeach
                </div>
            </div>
            @endforeach
            @error('selectedPackageId') <p class="form-error mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Quantity + Options --}}
        <div class="card p-4 space-y-4">
            <p class="text-sm font-bold text-slate-800">3. Detail Order</p>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="form-label">Berat / Jumlah</label>
                    <div class="relative">
                        <input type="number" step="0.5" min="0.1"
                               wire:model.live.debounce.300ms="quantity"
                               class="form-input pr-8">
                        <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-slate-400">
                            {{ $selectedPackage?->unit ?? 'Kg' }}
                        </span>
                    </div>
                    @error('quantity') <p class="form-error">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="form-label">Pilih Parfum</label>
                    <select wire:model.live="perfumeVariant" class="form-input">
                        @foreach ($this->perfumeItems as $item)
                            <option value="{{ Str::after($item->name, 'Parfum Laundry ') ?: $item->name }}">
                                {{ $item->name }} ({{ number_format($item->system_stock, 0) }} {{ $item->unit }})
                            </option>
                        @endforeach
                        <option value="Sakura">Sakura</option>
                        <option value="Lavender">Lavender</option>
                        <option value="Fresh Ocean">Fresh Ocean</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="form-label">Catatan Khusus (opsional)</label>
                <textarea wire:model="notes" rows="2" placeholder="cth. Pisahkan baju putih, jangan dilipat…" class="form-input resize-none"></textarea>
            </div>
        </div>

        {{-- Payment --}}
        <div class="card p-4 space-y-4">
            <p class="text-sm font-bold text-slate-800">4. Pembayaran</p>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="form-label">Status Bayar</label>
                    <select wire:model="paymentStatus" class="form-input">
                        <option value="BELUM_LUNAS">Belum Lunas</option>
                        <option value="LUNAS">Lunas</option>
                    </select>
                </div>
                <div>
                    <label class="form-label">Metode Bayar</label>
                    <select wire:model="paymentMethod" class="form-input">
                        <option value="TUNAI">Tunai</option>
                        <option value="QRIS">QRIS</option>
                        <option value="TRANSFER">Transfer</option>
                        <option value="DEPOSIT_KG">Deposit Kg</option>
                    </select>
                </div>
            </div>
            @if ($paymentMethod === 'DEPOSIT_KG' && $selectedCustomer)
                <div class="flex items-center gap-2 rounded-xl bg-emerald-50 border border-emerald-200 px-3 py-2.5">
                    <svg class="size-4 text-emerald-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                    <p class="text-xs text-emerald-700">
                        Saldo deposit: <strong>{{ $selectedCustomer->deposit_kg }} Kg</strong>
                        → Sisa setelah order: <strong>{{ max(0, (float)$selectedCustomer->deposit_kg - $quantity) }} Kg</strong>
                    </p>
                </div>
            @endif
        </div>
    </div>

    {{-- ── RIGHT: Order Preview & Submit (2/5) ─────────────────────── --}}
    <div class="lg:col-span-2 space-y-4">
        {{-- Sticky order summary --}}
        <div class="card p-4 space-y-4 lg:sticky lg:top-24">
            <p class="text-sm font-bold text-slate-800">Ringkasan Order</p>

            @if ($this->orderPreview)
            @php $preview = $this->orderPreview; @endphp
            <div class="space-y-2.5">
                <div class="flex justify-between text-sm">
                    <span class="text-slate-500">Layanan</span>
                    <span class="font-semibold text-slate-800 text-right">{{ $preview['package_name'] }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-slate-500">Berat/Qty</span>
                    <span class="font-semibold text-slate-800">{{ $preview['quantity'] }} {{ $preview['unit'] }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-slate-500">Harga satuan</span>
                    <span class="font-semibold text-slate-800">Rp {{ number_format($preview['price_per_unit']) }}</span>
                </div>
                <div class="h-px bg-slate-100"></div>
                <div class="flex justify-between">
                    <span class="text-base font-bold text-slate-800">Total</span>
                    <span class="text-xl font-extrabold text-sky-600 tabular-nums">Rp {{ number_format($preview['total_price']) }}</span>
                </div>
            </div>

            {{-- BOM Auto-deduct preview --}}
            <div class="rounded-xl bg-slate-50 border border-slate-200/80 p-3">
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wide mb-2">Auto-Deduct Bahan Baku</p>
                <div class="space-y-1.5">
                    <div class="flex items-center gap-2">
                        <div class="size-2 rounded-full bg-sky-400 shrink-0"></div>
                        <span class="text-xs text-slate-600 flex-1">Deterjen</span>
                        <span class="text-xs font-bold text-slate-700 tabular-nums">{{ $preview['detergent_ml'] }} ml</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="size-2 rounded-full bg-slate-400 shrink-0"></div>
                        <span class="text-xs text-slate-600 flex-1">Softener</span>
                        <span class="text-xs font-bold text-slate-700 tabular-nums">{{ $preview['softener_ml'] }} ml</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="size-2 rounded-full bg-violet-400 shrink-0"></div>
                        <span class="text-xs text-slate-600 flex-1">Parfum {{ $perfumeVariant }}</span>
                        <span class="text-xs font-bold text-slate-700 tabular-nums">{{ $preview['perfume_ml'] }} ml</span>
                    </div>
                </div>
            </div>
            @else
            <div class="flex flex-col items-center justify-center py-8 text-center">
                <p class="text-3xl mb-2">🧺</p>
                <p class="text-sm text-slate-400">Pilih pelanggan dan layanan untuk melihat ringkasan</p>
            </div>
            @endif

            {{-- Submit --}}
            <button wire:click="submitOrder"
                    wire:loading.attr="disabled"
                    wire:target="submitOrder"
                    class="btn-primary w-full justify-center py-3 text-base"
                    @if (!$this->orderPreview || !$selectedCustomerId) disabled @endif
                    style="{{ (!$this->orderPreview || !$selectedCustomerId) ? 'opacity:0.5;cursor:not-allowed;' : '' }}">
                <span wire:loading.remove wire:target="submitOrder" class="flex items-center justify-center gap-2">
                    <svg class="size-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                    <span>Simpan Order</span>
                </span>
                <span wire:loading wire:target="submitOrder" class="flex items-center justify-center gap-2">
                    <svg class="size-4 animate-spin shrink-0" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                    <span>Menyimpan…</span>
                </span>
            </button>
        </div>
    </div>
</div>

{{-- Quick Create Customer Modal --}}
<div x-show="showQuick" x-cloak style="display:none">
    <div class="bottom-sheet-overlay md:hidden" @click="showQuick = false"></div>
    <div class="dialog-overlay hidden md:flex" @click.self="showQuick = false"></div>

    <div x-show="showQuick"
         x-transition:enter="transition ease-out duration-300" x-transition:enter-start="translate-y-full" x-transition:enter-end="translate-y-0"
         x-transition:leave="transition ease-in duration-200" x-transition:leave-start="translate-y-0" x-transition:leave-end="translate-y-full"
         class="bottom-sheet md:hidden">
        <div class="bottom-sheet-handle"></div>
        @include('livewire.pos._quick-customer-form')
    </div>
    <div x-show="showQuick"
         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
         class="hidden md:flex fixed inset-0 z-50 items-center justify-center p-4 pointer-events-none">
        <div class="pointer-events-auto w-full max-w-sm bg-white rounded-2xl shadow-2xl overflow-hidden max-h-[90vh] overflow-y-auto">
            @include('livewire.pos._quick-customer-form')
        </div>
    </div>
</div>
@endif

{{-- ══ TAB: RIWAYAT ORDER ═══════════════════════════════════════════ --}}
@if ($activeTab === 'history')
<div class="space-y-4">
    {{-- Filters --}}
    <div class="flex flex-wrap gap-2">
        <div class="relative flex-1 min-w-[180px]">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 size-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/></svg>
            <input type="search" wire:model.live.debounce.300ms="historySearch" placeholder="Cari nota/pelanggan…" class="form-input pl-9 py-2 text-sm">
        </div>
        <select wire:model.live="historyStatus" class="form-input py-2 text-sm w-auto">
            <option value="">Semua Status</option>
            @foreach (['ANTREAN','CUCI','SETRIKA','PACKING','SIAP_DIAMBIL','SELESAI','VOID'] as $s)
            <option value="{{ $s }}">{{ $s }}</option>
            @endforeach
        </select>
        <select wire:model.live="historyPayment" class="form-input py-2 text-sm w-auto">
            <option value="">Semua Bayar</option>
            <option value="LUNAS">Lunas</option>
            <option value="BELUM_LUNAS">Belum Lunas</option>
        </select>
        <input type="date" wire:model.live="historyDate" class="form-input py-2 text-sm w-auto">
    </div>

    {{-- Desktop Table --}}
    <div class="card hidden md:block overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50/80 border-b border-slate-200/80">
                    <tr>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wide">Invoice</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wide">Pelanggan</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wide">Layanan</th>
                        <th class="text-right px-4 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wide">Total</th>
                        <th class="text-center px-4 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wide">Status</th>
                        <th class="text-center px-4 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wide">Bayar</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($this->orders as $order)
                    @php
                        $statusClass = match($order->status) {
                            'SELESAI'      => 'badge-green',
                            'SIAP_DIAMBIL' => 'badge-sky',
                            'VOID'         => 'badge-rose',
                            'ANTREAN'      => 'badge-slate',
                            default        => 'badge-amber',
                        };
                        $payClass = $order->payment_status === 'LUNAS' ? 'badge-green' : ($order->payment_status === 'REFUND' ? 'badge-rose' : 'badge-amber');
                    @endphp
                    <tr @class(['hover:bg-slate-50/60', 'opacity-50' => $order->status === 'VOID'])>
                        <td class="px-4 py-3">
                            <p class="font-mono text-xs font-semibold text-slate-700">{{ $order->invoice_number }}</p>
                            <p class="text-xs text-slate-400">{{ $order->created_at->format('d M Y, H:i') }}</p>
                        </td>
                        <td class="px-4 py-3">
                            <p class="font-medium text-slate-800">{{ $order->customer?->name ?? '-' }}</p>
                            <p class="text-xs text-slate-400">{{ $order->customer?->phone }}</p>
                        </td>
                        <td class="px-4 py-3">
                            <p class="text-sm text-slate-700">{{ $order->servicePackage?->name }}</p>
                            <p class="text-xs text-slate-400">{{ $order->quantity }} {{ $order->unit }} · {{ $order->perfume_variant }}</p>
                        </td>
                        <td class="px-4 py-3 text-right font-bold text-slate-800 tabular-nums">
                            Rp {{ number_format($order->total_price) }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="badge {{ $statusClass }}">{{ $order->status }}</span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="badge {{ $payClass }}">{{ $order->payment_status }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-end gap-1">
                                <button wire:click="openReceipt({{ $order->id }})" class="btn-icon" title="Lihat Struk">
                                    <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"/></svg>
                                </button>
                                @if (!in_array($order->status, ['SELESAI','VOID']))
                                    @if ($order->payment_status === 'BELUM_LUNAS')
                                        <button wire:click="markAsPaid({{ $order->id }})" class="btn-icon" title="Tandai Lunas">
                                            <svg class="size-4 text-emerald-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z"/></svg>
                                        </button>
                                    @endif
                                    <button wire:click="openEditOrder({{ $order->id }})" class="btn-icon" title="Edit">
                                        <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Z"/></svg>
                                    </button>
                                    <button wire:click="confirmVoid({{ $order->id }})" class="btn-icon" title="Void">
                                        <svg class="size-4 text-rose-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9.75 9.75l4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="px-4 py-12 text-center text-slate-400">
                        <p class="text-2xl mb-2">📋</p>
                        <p>Tidak ada order yang ditemukan</p>
                    </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Mobile Cards --}}
    <div class="md:hidden space-y-3">
        @forelse ($this->orders as $order)
        @php
            $statusClass = match($order->status) { 'SELESAI' => 'badge-green', 'SIAP_DIAMBIL' => 'badge-sky', 'VOID' => 'badge-rose', 'ANTREAN' => 'badge-slate', default => 'badge-amber' };
            $payClass = $order->payment_status === 'LUNAS' ? 'badge-green' : ($order->payment_status === 'REFUND' ? 'badge-rose' : 'badge-amber');
        @endphp
        <div @class(['card p-4 space-y-2.5', 'opacity-50' => $order->status === 'VOID'])>
            <div class="flex items-start justify-between gap-2">
                <div>
                    <p class="font-mono text-xs font-semibold text-slate-600">{{ $order->invoice_number }}</p>
                    <p class="font-semibold text-slate-800 mt-0.5">{{ $order->customer?->name }}</p>
                    <p class="text-xs text-slate-400">{{ $order->servicePackage?->name }} · {{ $order->quantity }} {{ $order->unit }}</p>
                </div>
                <div class="text-right shrink-0">
                    <p class="font-bold text-sky-600 tabular-nums">Rp {{ number_format($order->total_price) }}</p>
                    <p class="text-xs text-slate-400">{{ $order->created_at->format('d M') }}</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <span class="badge {{ $statusClass }}">{{ $order->status }}</span>
                <span class="badge {{ $payClass }}">{{ $order->payment_status }}</span>
            </div>
            <div class="flex gap-2 pt-1 border-t border-slate-100">
                <button wire:click="openReceipt({{ $order->id }})" class="btn-secondary text-xs py-1.5 flex-1">Struk</button>
                @if (!in_array($order->status, ['SELESAI','VOID']))
                    @if ($order->payment_status === 'BELUM_LUNAS')
                        <button wire:click="markAsPaid({{ $order->id }})" class="btn-secondary text-xs py-1.5 flex-1 text-emerald-600">Lunasi</button>
                    @endif
                    <button wire:click="openEditOrder({{ $order->id }})" class="btn-secondary text-xs py-1.5 flex-1">Edit</button>
                    <button wire:click="confirmVoid({{ $order->id }})" class="btn-danger text-xs py-1.5 px-3">VOID</button>
                @endif
            </div>
        </div>
        @empty
        <div class="card p-12 text-center text-slate-400">
            <p class="text-3xl mb-2">📋</p>
            <p>Tidak ada order</p>
        </div>
        @endforelse
    </div>
</div>
@endif

{{-- ══ RECEIPT MODAL (RESPONSIVE THERMAL RECEIPT MODAL) ═══════════ --}}
<div x-show="showReceipt" x-cloak style="display:none">
    <div class="dialog-overlay" @click.self="showReceipt = false">
        <div x-show="showReceipt"
             x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
             class="pointer-events-auto w-full max-w-sm sm:max-w-md mx-auto bg-white rounded-3xl shadow-2xl overflow-hidden flex flex-col max-h-[92vh]">
            @if ($this->receiptOrder)
            @include('livewire.pos._receipt', ['order' => $this->receiptOrder])
            @endif
        </div>
    </div>
</div>

{{-- ══ EDIT ORDER MODAL ═════════════════════════════════════════════ --}}
<div x-show="showEdit" x-cloak style="display:none">
    <div class="bottom-sheet-overlay md:hidden" @click="showEdit = false"></div>
    <div class="dialog-overlay hidden md:flex" @click.self="showEdit = false"></div>
    <div x-show="showEdit"
         x-transition:enter="transition ease-out duration-300" x-transition:enter-start="translate-y-full" x-transition:enter-end="translate-y-0"
         x-transition:leave="transition ease-in duration-200" x-transition:leave-start="translate-y-0" x-transition:leave-end="translate-y-full"
         class="bottom-sheet md:hidden">
        <div class="bottom-sheet-handle"></div>
        @include('livewire.pos._edit-order-form')
    </div>
    <div x-show="showEdit"
         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
         class="hidden md:flex fixed inset-0 z-50 items-center justify-center p-4 pointer-events-none">
        <div class="pointer-events-auto w-full max-w-lg bg-white rounded-2xl shadow-2xl overflow-hidden max-h-[90vh] overflow-y-auto">
            @include('livewire.pos._edit-order-form')
        </div>
    </div>
</div>

{{-- ══ VOID CONFIRM ════════════════════════════════════════════════ --}}
<div x-show="showVoid" x-cloak style="display:none">
    <div class="dialog-overlay">
        <div x-show="showVoid"
             x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
             class="dialog-panel p-6">
            <div class="flex items-start gap-3 mb-4">
                <div class="size-10 rounded-full bg-rose-50 text-rose-500 flex items-center justify-center shrink-0">
                    <svg class="size-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/></svg>
                </div>
                <div>
                    <p class="font-bold text-slate-800">VOID / Batalkan Order?</p>
                    <p class="text-sm text-slate-500 mt-1">Stok sabun & deposit Kg akan dikembalikan secara otomatis.</p>
                </div>
            </div>
            <div>
                <label class="form-label">Alasan Pembatalan</label>
                <textarea wire:model="voidReason" rows="2" class="form-input resize-none" placeholder="Pelanggan membatalkan, salah input, dsb."></textarea>
                @error('voidReason') <p class="form-error">{{ $message }}</p> @enderror
            </div>
            <div class="flex gap-3 mt-5">
                <button wire:click="voidOrder" class="btn-danger flex-1">Ya, VOID Order</button>
                <button @click="showVoid = false" class="btn-secondary flex-1">Batal</button>
            </div>
        </div>
    </div>
</div>

</div>
