<div x-data="{
    showItemForm:  @entangle('showItemForm'),
    showRestock:   @entangle('showRestock'),
    showUtility:   @entangle('showUtilityForm'),
}" class="space-y-4">

{{-- ══ HEADER ══════════════════════════════════════════════════════ --}}
<div class="flex items-center justify-between gap-3">
    <div>
        <h2 class="text-lg font-bold text-slate-800">Inventaris & Utilitas</h2>
        <p class="text-xs text-slate-400 mt-0.5">Stok bahan baku, opname fisik, dan log utilitas (HPP)</p>
    </div>
    <div class="flex items-center gap-2 shrink-0">
        @if ($activeTab === 'inventory')
        <button wire:click="openCreateItem" class="btn-primary text-sm py-2">
            <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
            <span class="hidden sm:inline">Item Baru</span>
            <span class="sm:hidden">Item</span>
        </button>
        @elseif ($activeTab === 'utility')
        <button wire:click="openCreateUtility" class="btn-primary text-sm py-2">
            <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
            <span class="hidden sm:inline">Catat Utilitas</span>
            <span class="sm:hidden">Catat</span>
        </button>
        @endif
    </div>
</div>

{{-- ══ LOW STOCK ALERT ═══════════════════════════════════════════════ --}}
@if ($this->lowStockItems->isNotEmpty())
<div class="rounded-2xl border border-amber-200 bg-amber-50/70 p-3.5 space-y-2">
    <div class="flex items-center gap-2">
        <svg class="size-4 text-amber-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/></svg>
        <p class="text-xs font-bold text-amber-800">{{ $this->lowStockItems->count() }} item menipis mendekati atau di bawah batas minimum</p>
    </div>
    <div class="flex flex-wrap gap-2 pt-0.5">
        @foreach ($this->lowStockItems as $item)
        <button wire:click="openRestock({{ $item->id }})"
                class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-white border border-amber-300 text-xs font-bold text-amber-800 hover:bg-amber-100/50 shadow-xs transition-colors">
            🧴 {{ $item->name }} — {{ number_format($item->system_stock, 0) }} {{ $item->unit }}
            <span class="text-[10px] bg-amber-100 text-amber-900 px-1.5 py-0.5 rounded-full font-bold">Restock</span>
        </button>
        @endforeach
    </div>
</div>
@endif

{{-- ══ TABS (3-Column Segmented Control on Mobile) ═════════════════ --}}
<div class="grid grid-cols-3 bg-slate-100 p-1 rounded-2xl sm:w-fit sm:flex sm:gap-1">
    @php
    $tabItems = [
        'inventory' => ['icon' => '🧴', 'label' => 'Stok', 'full' => 'Stok Bahan Baku'],
        'opname'    => ['icon' => '📊', 'label' => 'Opname', 'full' => 'Stok Opname'],
        'utility'   => ['icon' => '⚡', 'label' => 'Utilitas', 'full' => 'Utilitas & HPP'],
    ];
    @endphp
    @foreach ($tabItems as $tabKey => $tabData)
    <button wire:click="$set('activeTab', '{{ $tabKey }}')"
            @class([
                'py-2 px-3 sm:px-4 rounded-xl text-xs font-bold transition-all text-center flex items-center justify-center gap-1.5',
                'bg-white text-slate-900 shadow-xs' => $activeTab === $tabKey,
                'text-slate-500 hover:text-slate-800' => $activeTab !== $tabKey,
            ])>
        <span>{{ $tabData['icon'] }}</span>
        <span class="sm:hidden">{{ $tabData['label'] }}</span>
        <span class="hidden sm:inline">{{ $tabData['full'] }}</span>
    </button>
    @endforeach
</div>

{{-- ══ TAB: STOK BAHAN BAKU ═════════════════════════════════════════ --}}
@if ($activeTab === 'inventory')
<div class="space-y-4">
    {{-- Stock bars visualization (Clean Minimalist Gauge) --}}
    <div class="card p-4 space-y-3.5">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <div class="flex items-center gap-2.5">
                <div class="size-8 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center text-sm font-bold shrink-0">
                    🛢️
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-800 leading-tight">Level Tangki Bahan Baku</h3>
                    <p class="text-[11px] text-slate-400 mt-0.5">Kapasitas sabun cuci & parfum laundry</p>
                </div>
            </div>
            <span class="text-[11px] font-semibold text-emerald-600 bg-emerald-50 border border-emerald-100 rounded-full px-2.5 py-0.5 flex items-center gap-1.5 shrink-0">
                <span class="size-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                Real-time
            </span>
        </div>

        <div class="divide-y divide-slate-100">
            @foreach ($this->stockBarData as $item)
            @php
                $isChemical = $item['category'] === 'CHEMICAL';
                $iconBg = $isChemical ? 'bg-sky-50 border-sky-100 text-sky-600' : 'bg-purple-50 border-purple-100 text-purple-600';
                $icon = $isChemical ? '🧴' : '🌸';

                $colorTheme = match($item['color']) {
                    'rose'    => ['bar' => 'bg-rose-500',    'badge' => 'bg-rose-50 text-rose-700 border-rose-200',    'text' => 'text-rose-600'],
                    'amber'   => ['bar' => 'bg-amber-500',   'badge' => 'bg-amber-50 text-amber-700 border-amber-200', 'text' => 'text-amber-600'],
                    default   => ['bar' => 'bg-emerald-500', 'badge' => 'bg-emerald-50 text-emerald-700 border-emerald-200', 'text' => 'text-emerald-600'],
                };
            @endphp
            <div class="py-3 first:pt-0 last:pb-0 space-y-2">
                {{-- Row 1: Icon + Name + Badges on Left | Volume & Percentage on Right --}}
                <div class="flex items-center justify-between gap-2">
                    <div class="flex items-center gap-2.5 min-w-0 flex-1">
                        <span class="size-7 rounded-lg border {{ $iconBg }} flex items-center justify-center text-xs shrink-0 shadow-2xs">
                            {{ $icon }}
                        </span>
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-1.5 flex-wrap">
                                <span class="text-xs sm:text-sm font-bold text-slate-800 truncate">{{ $item['name'] }}</span>
                                @if ($item['is_low'])
                                    <span class="badge badge-rose text-[9px] px-1.5 py-0.2 shrink-0">Menipis</span>
                                @endif
                                @if (abs($item['variance']) > 0.1)
                                    <span @class(['badge text-[9px] px-1.5 py-0.2 shrink-0 hidden sm:inline-flex', 'badge-rose' => $item['variance'] < 0, 'badge-green' => $item['variance'] > 0])>
                                        {{ $item['variance'] > 0 ? '+' : '' }}{{ round($item['variance'],1) }} {{ $item['unit'] }}
                                    </span>
                                @endif
                            </div>
                            <p class="text-[11px] text-slate-400 mt-0.5">Min: {{ number_format($item['min_threshold'],0) }} {{ $item['unit'] }}</p>
                        </div>
                    </div>

                    {{-- Right: Volume & Percentage --}}
                    <div class="text-right shrink-0">
                        <p class="text-xs sm:text-sm font-bold text-slate-800 tabular-nums">
                            {{ number_format($item['system_stock'],0) }} <span class="text-xs font-normal text-slate-400">{{ $item['unit'] }}</span>
                        </p>
                        <span class="inline-block text-[10px] font-bold {{ $colorTheme['text'] }} mt-0.5">
                            {{ $item['percent'] }}% Kapasitas
                        </span>
                    </div>
                </div>

                {{-- Row 2: Sleek Progress Bar --}}
                <div class="h-2 w-full bg-slate-100 rounded-full overflow-hidden">
                    <div class="h-full rounded-full transition-all duration-500 ease-out {{ $colorTheme['bar'] }}"
                         style="width: {{ max(4, min(100, $item['percent'])) }}%"></div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Desktop table --}}
    <div class="card hidden md:block overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-50/80 border-b border-slate-200/80">
                <tr>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wide">Item</th>
                    <th class="text-center px-4 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wide">Kategori</th>
                    <th class="text-right px-4 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wide">Stok Sistem</th>
                    <th class="text-right px-4 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wide">Stok Fisik</th>
                    <th class="text-right px-4 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wide">Minimum</th>
                    <th class="text-right px-4 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wide">Harga Modal</th>
                    <th class="px-4 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($this->inventoryItems as $item)
                @php $catClass = match($item->category){ 'CHEMICAL' => 'badge-sky', 'PARFUM' => 'badge-slate', 'GAS' => 'badge-amber', default => 'badge-slate' }; @endphp
                <tr @class(['hover:bg-slate-50/60', 'opacity-50' => $item->trashed()])>
                    <td class="px-4 py-3">
                        <p class="font-semibold text-slate-800">{{ $item->name }}</p>
                        <p class="text-xs font-mono text-slate-400">{{ $item->sku }}</p>
                    </td>
                    <td class="px-4 py-3 text-center"><span class="badge {{ $catClass }}">{{ $item->category }}</span></td>
                    <td class="px-4 py-3 text-right tabular-nums">
                        @if ($item->is_low_stock)
                            <span class="font-bold text-rose-600">{{ number_format($item->system_stock,1) }}</span>
                        @else
                            <span class="font-bold text-emerald-600">{{ number_format($item->system_stock,1) }}</span>
                        @endif
                        <span class="text-xs text-slate-400"> {{ $item->unit }}</span>
                    </td>
                    <td class="px-4 py-3 text-right tabular-nums text-slate-600">
                        {{ number_format($item->actual_stock,1) }} <span class="text-xs text-slate-400">{{ $item->unit }}</span>
                        @if (abs($item->variance) > 0.1)
                            <span @class(['block text-[10px] font-semibold', 'text-rose-500' => $item->variance < 0, 'text-emerald-500' => $item->variance > 0])>
                                {{ $item->variance > 0 ? '+' : '' }}{{ round($item->variance,1) }}
                            </span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-right tabular-nums text-slate-500">{{ number_format($item->min_threshold,0) }} {{ $item->unit }}</td>
                    <td class="px-4 py-3 text-right tabular-nums text-slate-500">Rp {{ number_format($item->cost_per_unit, 2) }}</td>
                    <td class="px-4 py-3">
                        <div class="flex items-center justify-end gap-1.5">
                            @if ($item->trashed())
                                <button wire:click="restoreItem({{ $item->id }})" class="btn-secondary text-xs py-1 px-2.5">Pulihkan</button>
                            @else
                                <button wire:click="openRestock({{ $item->id }})" class="btn-secondary text-xs py-1.5 px-3 text-emerald-700 bg-emerald-50 border-emerald-200 hover:bg-emerald-100 font-semibold">Restock</button>
                                <button wire:click="openEditItem({{ $item->id }})" class="btn-icon" title="Edit">
                                    <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Z"/></svg>
                                </button>
                                <button wire:click="deleteItem({{ $item->id }})" wire:confirm="Hapus item ini?" class="btn-icon text-slate-400 hover:text-rose-600 hover:bg-rose-50" title="Hapus">
                                    <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/></svg>
                                </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="px-4 py-12 text-center text-slate-400"><p class="text-2xl mb-2">🧴</p><p>Belum ada item inventaris</p></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Mobile cards --}}
    <div class="md:hidden space-y-3">
        @forelse ($this->inventoryItems as $item)
        @php $cc = match($item->category){ 'CHEMICAL' => 'badge-sky', 'PARFUM' => 'badge-slate', default => 'badge-amber' }; @endphp
        <div @class(['card p-3.5 space-y-3', 'opacity-60' => $item->trashed()])>
            <div class="flex items-start justify-between gap-2">
                <div>
                    <p class="font-bold text-slate-800 text-sm leading-tight">{{ $item->name }}</p>
                    <p class="text-xs font-mono text-slate-400 mt-0.5">{{ $item->sku }}</p>
                </div>
                <span class="badge {{ $cc }} text-[10px] shrink-0 font-semibold">{{ $item->category }}</span>
            </div>
            <div class="grid grid-cols-3 gap-2 text-xs text-center">
                <div @class(['rounded-xl p-2 border', 'bg-rose-50 border-rose-100' => $item->is_low_stock, 'bg-slate-50 border-slate-100' => !$item->is_low_stock])>
                    <p class="text-slate-400 text-[11px]">Sistem</p>
                    <p @class(['font-bold mt-0.5', 'text-rose-600' => $item->is_low_stock, 'text-slate-700' => !$item->is_low_stock])>{{ number_format($item->system_stock,1) }} {{ $item->unit }}</p>
                </div>
                <div class="bg-slate-50 border border-slate-100 rounded-xl p-2">
                    <p class="text-slate-400 text-[11px]">Fisik</p>
                    <p class="font-bold text-slate-700 mt-0.5">{{ number_format($item->actual_stock,1) }}</p>
                </div>
                <div class="bg-slate-50 border border-slate-100 rounded-xl p-2">
                    <p class="text-slate-400 text-[11px]">Min</p>
                    <p class="font-bold text-amber-600 mt-0.5">{{ number_format($item->min_threshold,0) }}</p>
                </div>
            </div>
            <div class="flex items-center gap-2 pt-1 border-t border-slate-100">
                @if ($item->trashed())
                    <button wire:click="restoreItem({{ $item->id }})" class="btn-secondary text-xs py-2 flex-1">Pulihkan</button>
                @else
                    <button wire:click="openRestock({{ $item->id }})" class="flex-1 min-w-0 bg-emerald-50 text-emerald-700 border border-emerald-200 hover:bg-emerald-100 font-semibold text-xs py-2 px-3 rounded-xl flex items-center justify-center gap-1 transition-colors">
                        🧴 Restock
                    </button>
                    <button wire:click="openEditItem({{ $item->id }})" class="btn-secondary text-xs py-2 px-3 flex-1 justify-center">
                        Edit
                    </button>
                    <button wire:click="deleteItem({{ $item->id }})" wire:confirm="Hapus item ini?" class="size-9 shrink-0 rounded-xl bg-slate-50 border border-slate-200 text-slate-400 hover:text-rose-600 hover:bg-rose-50 flex items-center justify-center transition-colors" title="Hapus">
                        <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/></svg>
                    </button>
                @endif
            </div>
        </div>
        @empty
        <div class="card p-12 text-center text-slate-400"><p class="text-3xl mb-2">🧴</p><p>Belum ada item</p></div>
        @endforelse
    </div>

    {{-- Mutation log --}}
    <div class="card p-4">
        <p class="text-sm font-bold text-slate-800 mb-3">Log Mutasi Stok Terakhir</p>
        <div class="space-y-2 max-h-72 overflow-y-auto scrollbar-thin">
            @foreach ($this->mutationLog as $m)
            @php
                $mtClass = match($m->mutation_type){ 'RESTOCK_IN' => 'badge-green', 'AUTO_DEDUCT_ORDER' => 'badge-rose', 'VOID_ROLLBACK' => 'badge-amber', default => 'badge-slate' };
                $amtClass = (float)$m->amount > 0 ? 'text-emerald-600' : 'text-rose-600';
            @endphp
            <div class="flex items-center gap-2 sm:gap-3 py-2 border-b border-slate-100 last:border-0 text-sm">
                <span class="badge {{ $mtClass }} text-[10px] shrink-0">{{ $m->mutation_type }}</span>
                <span class="flex-1 text-slate-700 text-xs truncate font-medium">{{ $m->inventoryItem?->name }}</span>
                @if ($m->reference_invoice) <span class="text-[10px] font-mono text-slate-400 shrink-0 hidden sm:inline">{{ $m->reference_invoice }}</span> @endif
                <span class="font-bold tabular-nums text-xs sm:text-sm {{ $amtClass }} shrink-0">{{ (float)$m->amount > 0 ? '+' : '' }}{{ round($m->amount, 1) }} {{ $m->inventoryItem?->unit }}</span>
                <span class="text-[10px] text-slate-400 shrink-0">{{ $m->created_at->diffForHumans() }}</span>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endif

{{-- ══ TAB: STOK OPNAME ════════════════════════════════════════════ --}}
@if ($activeTab === 'opname')
<div class="space-y-4">
    {{-- Info Banner --}}
    <div class="rounded-2xl border border-sky-100 bg-sky-50/70 p-3.5 text-sm text-sky-800 space-y-1">
        <p class="font-bold flex items-center gap-1.5 text-xs text-sky-900">
            <svg class="size-4 text-sky-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z"/></svg>
            Petunjuk Stok Opname
        </p>
        <p class="text-xs text-sky-700 leading-relaxed">Masukkan jumlah stok fisik riil di gudang. Sistem akan otomatis menghitung selisih (<em>variance</em>) untuk memantau potensi kebocoran atau pemborosan tak tercatat.</p>
    </div>

    {{-- MOBILE: Opname Cards (Sleek, Unsquished Touch-friendly layout) --}}
    <div class="md:hidden space-y-3">
        @foreach ($this->inventoryItems->filter(fn($i) => !$i->trashed()) as $item)
        @php
            $inputVal = $opnameValues[$item->id] ?? (float)$item->actual_stock;
            $variance = round($inputVal - (float)$item->system_stock, 2);
        @endphp
        <div class="card p-3.5 space-y-3">
            {{-- Header: Item Name + Variance Badge --}}
            <div class="flex items-start justify-between gap-2">
                <div class="min-w-0 flex-1">
                    <p class="font-bold text-slate-800 text-sm leading-snug">{{ $item->name }}</p>
                    <p class="text-xs text-slate-400 mt-0.5">{{ $item->category }} · <span class="font-mono">{{ $item->sku }}</span></p>
                </div>
                <div class="shrink-0">
                    @if (abs($variance) < 0.01)
                        <span class="badge badge-green font-bold text-xs py-1 px-2.5">✓ Pas (0)</span>
                    @elseif ($variance < 0)
                        <span class="badge badge-rose font-bold text-xs py-1 px-2.5">{{ $variance }} {{ $item->unit }}</span>
                    @else
                        <span class="badge badge-sky font-bold text-xs py-1 px-2.5">+{{ $variance }} {{ $item->unit }}</span>
                    @endif
                </div>
            </div>

            {{-- 2-Column Comparison: Sistem vs Input Fisik --}}
            <div class="grid grid-cols-2 gap-2.5 pt-0.5">
                {{-- Box Stok Sistem --}}
                <div class="bg-slate-50 border border-slate-200/80 rounded-xl p-2.5 flex flex-col justify-center">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wide">Stok Sistem</span>
                    <span class="text-sm font-bold text-slate-700 tabular-nums mt-0.5">
                        {{ number_format($item->system_stock, 1) }} <span class="text-xs font-normal text-slate-500">{{ $item->unit }}</span>
                    </span>
                </div>

                {{-- Box Input Stok Fisik --}}
                <div class="space-y-1">
                    <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wide">Stok Fisik Riil</span>
                    <div class="relative">
                        <input type="number" step="0.1" min="0"
                               wire:model.live="opnameValues.{{ $item->id }}"
                               class="form-input text-center pr-8 font-bold text-sm h-10 w-full bg-white border-slate-300 focus:border-sky-500 focus:ring-sky-500 rounded-xl shadow-2xs">
                        <span class="absolute right-2.5 top-1/2 -translate-y-1/2 text-xs text-slate-400 font-semibold pointer-events-none">{{ $item->unit }}</span>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- DESKTOP: Opname Table --}}
    <div class="card hidden md:block overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-50/80 border-b border-slate-200/80">
                <tr>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wide">Item</th>
                    <th class="text-right px-5 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wide">Stok Sistem</th>
                    <th class="text-center px-5 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wide">Stok Fisik (Input)</th>
                    <th class="text-right px-5 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wide">Selisih (Variance)</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach ($this->inventoryItems->filter(fn($i) => !$i->trashed()) as $item)
                @php
                    $inputVal = $opnameValues[$item->id] ?? (float)$item->actual_stock;
                    $variance = round($inputVal - (float)$item->system_stock, 2);
                @endphp
                <tr class="hover:bg-slate-50/60">
                    <td class="px-5 py-3.5">
                        <p class="font-bold text-slate-800 text-sm">{{ $item->name }}</p>
                        <p class="text-xs text-slate-400 mt-0.5">{{ $item->category }} · <span class="font-mono">{{ $item->sku }}</span></p>
                    </td>
                    <td class="px-5 py-3.5 text-right tabular-nums font-semibold text-slate-700">
                        {{ number_format($item->system_stock, 1) }} {{ $item->unit }}
                    </td>
                    <td class="px-5 py-3.5">
                        <div class="relative max-w-[140px] mx-auto">
                            <input type="number" step="0.1" min="0"
                                   wire:model.live="opnameValues.{{ $item->id }}"
                                   class="form-input text-center pr-9 font-bold text-sm h-10 shadow-2xs">
                            <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-slate-400 font-semibold pointer-events-none">{{ $item->unit }}</span>
                        </div>
                    </td>
                    <td class="px-5 py-3.5 text-right">
                        @if (abs($variance) < 0.01)
                            <span class="badge badge-green font-bold text-xs py-1 px-2.5">✓ Pas (0)</span>
                        @elseif ($variance < 0)
                            <div class="flex items-center justify-end gap-1.5">
                                <span class="badge badge-rose font-bold text-xs py-1 px-2.5">{{ $variance }} {{ $item->unit }}</span>
                                <span class="text-xs text-rose-500 font-semibold">Kurang</span>
                            </div>
                        @else
                            <span class="badge badge-sky font-bold text-xs py-1 px-2.5">+{{ $variance }} {{ $item->unit }}</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Save Bar --}}
    <div class="flex flex-col sm:flex-row items-center justify-between gap-3 pt-2">
        <div>
            @if ($opnameSaved)
            <div class="flex items-center gap-1.5 text-emerald-600 text-sm font-bold">
                <svg class="size-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                Hasil opname berhasil disimpan!
            </div>
            @endif
        </div>
        <button wire:click="saveOpname" wire:loading.attr="disabled" wire:target="saveOpname"
                class="btn-primary w-full sm:w-auto px-7 py-3 font-bold text-sm shadow-md justify-center">
            <span wire:loading.remove wire:target="saveOpname">💾 Simpan Hasil Opname</span>
            <span wire:loading wire:target="saveOpname" class="flex items-center gap-2">
                <svg class="size-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                Menyimpan…
            </span>
        </button>
    </div>
</div>
@endif

{{-- ══ TAB: UTILITAS & HPP ══════════════════════════════════════════ --}}
@if ($activeTab === 'utility')
<div class="space-y-4">
    {{-- HPP utility stats (Balanced 4-card Grid) --}}
    @php $ustats = $this->utilityStats; @endphp
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
        <div class="kpi-card border-l-4 border-l-sky-500">
            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide">HPP Utilitas / Kg</p>
            <p class="text-xl sm:text-2xl font-bold text-sky-700 mt-1 tabular-nums">Rp {{ number_format($this->hppUtilityPerKg) }}</p>
            <p class="text-[11px] text-slate-400 mt-0.5">Bulan berjalan</p>
        </div>
        @foreach ([['⚡ Listrik', $ustats['listrik'], 'badge-amber'], ['💧 Air PDAM', $ustats['air'], 'badge-sky'], ['🔥 Gas LPG', $ustats['gas'], 'badge-rose']] as [$label, $cost, $bc])
        <div class="kpi-card">
            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide">{{ $label }}</p>
            <p class="text-lg sm:text-xl font-bold text-slate-800 mt-1 tabular-nums">Rp {{ number_format($cost) }}</p>
            <span class="badge {{ $bc }} text-[10px] mt-1 font-semibold">MTD</span>
        </div>
        @endforeach
    </div>

    {{-- Filter toolbar --}}
    <div class="flex items-center gap-2">
        <select wire:model.live="utilityFilter" class="form-input py-2 text-sm w-full sm:w-auto font-medium">
            <option value="">Semua Utilitas</option>
            <option value="LISTRIK_PLN">⚡ Listrik PLN</option>
            <option value="AIR_PDAM">💧 Air PDAM</option>
            <option value="GAS_LPG">🔥 Gas LPG</option>
        </select>
    </div>

    {{-- Utility table --}}
    <div class="card hidden md:block overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-50/80 border-b border-slate-200/80">
                <tr>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wide">Tipe</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wide">Periode</th>
                    <th class="text-right px-4 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wide">Meteran Awal</th>
                    <th class="text-right px-4 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wide">Meteran Akhir</th>
                    <th class="text-right px-4 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wide">Pemakaian</th>
                    <th class="text-right px-4 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wide">Total Biaya</th>
                    <th class="px-4 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($this->utilityLogs as $log)
                @php
                    $utClass = match($log->utility_type){ 'LISTRIK_PLN' => 'badge-amber', 'AIR_PDAM' => 'badge-sky', 'GAS_LPG' => 'badge-rose' };
                    $utIcon  = match($log->utility_type){ 'LISTRIK_PLN' => '⚡', 'AIR_PDAM' => '💧', 'GAS_LPG' => '🔥' };
                @endphp
                <tr class="hover:bg-slate-50/60">
                    <td class="px-4 py-3"><span class="badge {{ $utClass }} font-semibold">{{ $utIcon }} {{ $log->utility_type }}</span></td>
                    <td class="px-4 py-3 text-slate-700 font-medium">{{ $log->period_date->format('M Y') }}</td>
                    <td class="px-4 py-3 text-right tabular-nums text-slate-500">{{ number_format($log->meter_start,1) }}</td>
                    <td class="px-4 py-3 text-right tabular-nums text-slate-500">{{ number_format($log->meter_end,1) }}</td>
                    <td class="px-4 py-3 text-right tabular-nums font-semibold text-slate-700">{{ number_format($log->usage_volume,1) }} {{ $log->unit }}</td>
                    <td class="px-4 py-3 text-right font-bold text-slate-800 tabular-nums">Rp {{ number_format($log->total_cost) }}</td>
                    <td class="px-4 py-3">
                        <div class="flex items-center justify-end gap-1.5">
                            <button wire:click="openEditUtility({{ $log->id }})" class="btn-icon" title="Edit">
                                <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Z"/></svg>
                            </button>
                            <button wire:click="deleteUtility({{ $log->id }})" wire:confirm="Hapus log utilitas ini?" class="btn-icon text-slate-400 hover:text-rose-600 hover:bg-rose-50" title="Hapus">
                                <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/></svg>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="px-4 py-12 text-center text-slate-400"><p class="text-2xl mb-2">⚡</p><p>Belum ada log utilitas</p></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Mobile utility cards --}}
    <div class="md:hidden space-y-3">
        @forelse ($this->utilityLogs as $log)
        @php $uc = match($log->utility_type){ 'LISTRIK_PLN' => 'badge-amber', 'AIR_PDAM' => 'badge-sky', 'GAS_LPG' => 'badge-rose' }; $ui = match($log->utility_type){ 'LISTRIK_PLN' => '⚡', 'AIR_PDAM' => '💧', 'GAS_LPG' => '🔥' }; @endphp
        <div class="card p-3.5 space-y-2.5">
            <div class="flex items-center justify-between gap-2">
                <div class="flex items-center gap-2">
                    <span class="badge {{ $uc }} text-xs font-semibold">{{ $ui }} {{ $log->utility_type }}</span>
                    <span class="text-xs text-slate-500 font-bold">{{ $log->period_date->format('M Y') }}</span>
                </div>
                <span class="font-bold text-slate-800 tabular-nums text-sm">Rp {{ number_format($log->total_cost) }}</span>
            </div>
            <div class="grid grid-cols-2 gap-2 text-xs">
                <div class="bg-slate-50 border border-slate-100 rounded-xl p-2.5"><span class="text-slate-400 block text-[11px]">Pemakaian:</span><strong class="text-slate-700 text-sm">{{ $log->usage_volume }} {{ $log->unit }}</strong></div>
                <div class="bg-slate-50 border border-slate-100 rounded-xl p-2.5"><span class="text-slate-400 block text-[11px]">Tarif/unit:</span><strong class="text-slate-700 text-sm">Rp {{ number_format($log->cost_per_unit) }}</strong></div>
            </div>
            <div class="flex items-center gap-2 pt-1 border-t border-slate-100">
                <button wire:click="openEditUtility({{ $log->id }})" class="btn-secondary text-xs py-2 flex-1 justify-center">Edit</button>
                <button wire:click="deleteUtility({{ $log->id }})" wire:confirm="Hapus log utilitas ini?" class="size-9 shrink-0 rounded-xl bg-slate-50 border border-slate-200 text-slate-400 hover:text-rose-600 hover:bg-rose-50 flex items-center justify-center transition-colors" title="Hapus">
                    <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/></svg>
                </button>
            </div>
        </div>
        @empty
        <div class="card p-12 text-center text-slate-400"><p class="text-3xl mb-2">⚡</p><p>Belum ada log utilitas</p></div>
        @endforelse
    </div>
</div>
@endif

{{-- ══ MODALS & BOTTOM SHEETS ═══════════════════════════════════════ --}}

{{-- Item form (Unified Responsive Modal/Bottom-Sheet) --}}
<div x-show="showItemForm" x-cloak style="display:none">
    <div class="bottom-sheet-overlay" @click="showItemForm = false"></div>
    <div x-show="showItemForm"
         x-transition:enter="transition ease-out duration-300" x-transition:enter-start="translate-y-full md:opacity-0 md:scale-95" x-transition:enter-end="translate-y-0 md:opacity-100 md:scale-100"
         x-transition:leave="transition ease-in duration-200" x-transition:leave-start="translate-y-0 md:opacity-100 md:scale-100" x-transition:leave-end="translate-y-full md:opacity-0 md:scale-95"
         class="bottom-sheet md:max-w-xl md:mx-auto">
        <div class="bottom-sheet-handle md:hidden"></div>
        @include('livewire.inventory._item-form')
    </div>
</div>

{{-- Restock modal --}}
<div x-show="showRestock" x-cloak style="display:none">
    <div class="bottom-sheet-overlay" @click="showRestock = false"></div>
    <div x-show="showRestock"
         x-transition:enter="transition ease-out duration-300" x-transition:enter-start="translate-y-full md:opacity-0 md:scale-95" x-transition:enter-end="translate-y-0 md:opacity-100 md:scale-100"
         x-transition:leave="transition ease-in duration-200" x-transition:leave-start="translate-y-0 md:opacity-100 md:scale-100" x-transition:leave-end="translate-y-full md:opacity-0 md:scale-95"
         class="bottom-sheet md:max-w-md md:mx-auto">
        
        <div class="bottom-sheet-handle md:hidden"></div>

        <div class="p-5 space-y-4">
            {{-- Header with Mobile Back Arrow and Desktop Close Button --}}
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2">
                    <button type="button" @click="showRestock = false" class="md:hidden p-1.5 -ml-1 text-slate-400 hover:text-slate-600 rounded-lg hover:bg-slate-100 transition-colors">
                        <svg class="size-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/></svg>
                    </button>
                    <h3 class="font-bold text-slate-800 text-base">Restock / Stok Masuk</h3>
                </div>
                <button type="button" @click="showRestock = false" class="hidden md:flex p-1.5 text-slate-400 hover:text-slate-600 rounded-lg hover:bg-slate-100 transition-colors">
                    <svg class="size-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
                </button>
            </div>

            @if ($this->restockItem)
            <div class="flex items-center gap-3 rounded-2xl bg-emerald-50 border border-emerald-100 p-3.5">
                <div class="size-11 rounded-xl bg-white shadow-xs flex items-center justify-center text-xl shrink-0">
                    🧴
                </div>
                <div class="min-w-0 flex-1">
                    <p class="font-bold text-slate-800 text-sm truncate">{{ $this->restockItem->name }}</p>
                    <p class="text-xs text-emerald-700 font-medium mt-0.5">Stok saat ini: <strong>{{ number_format($this->restockItem->system_stock, 1) }} {{ $this->restockItem->unit }}</strong></p>
                </div>
            </div>

            <div class="space-y-3.5">
                <div>
                    <label class="form-label text-xs font-semibold text-slate-700">Tambah Stok ({{ $this->restockItem->unit }})</label>
                    <div class="flex gap-2 mb-2 flex-wrap">
                        @foreach ([1000, 5000, 10000] as $p)
                        <button type="button" wire:click="$set('restockAmount', {{ $p }})"
                                @class(['px-3 py-1.5 rounded-xl text-xs font-bold border transition-all shadow-xs', 'border-sky-500 bg-sky-50 text-sky-700 ring-1 ring-sky-500' => $restockAmount == $p, 'border-slate-200 bg-white text-slate-600 hover:bg-slate-50' => $restockAmount != $p])>
                            +{{ number_format($p) }} {{ $this->restockItem->unit }}
                        </button>
                        @endforeach
                    </div>
                    <div class="relative">
                        <input type="number" wire:model.live="restockAmount" min="0.1" class="form-input pr-12 text-sm font-bold">
                        <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-slate-400 font-semibold">{{ $this->restockItem->unit }}</span>
                    </div>
                    @error('restockAmount') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="form-label text-xs font-semibold text-slate-700">Keterangan (opsional)</label>
                    <input type="text" wire:model="restockNotes" class="form-input text-sm" placeholder="cth. Beli jerigen 20L di Supplier X">
                </div>

                @if ($restockAmount > 0)
                <div class="rounded-xl bg-sky-50 border border-sky-100 px-3.5 py-2 text-xs text-sky-800 flex items-center justify-between">
                    <span>Estimasi stok baru:</span>
                    <strong class="text-sm font-bold">{{ round((float)$this->restockItem->system_stock + $restockAmount, 1) }} {{ $this->restockItem->unit }}</strong>
                </div>
                @endif
            </div>
            @endif

            {{-- Full Width Submit Button --}}
            <div class="pt-2">
                <button type="button" wire:click="saveRestock"
                        wire:loading.attr="disabled"
                        wire:target="saveRestock"
                        class="btn-primary w-full justify-center py-3 text-sm font-bold shadow-md">
                    <span wire:loading.remove wire:target="saveRestock">Konfirmasi Tambah Stok</span>
                    <span wire:loading wire:target="saveRestock" class="flex items-center gap-2">
                        <svg class="size-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                        Menyimpan…
                    </span>
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Utility form (Unified Responsive Modal/Bottom-Sheet) --}}
<div x-show="showUtility" x-cloak style="display:none">
    <div class="bottom-sheet-overlay" @click="showUtility = false"></div>
    <div x-show="showUtility"
         x-transition:enter="transition ease-out duration-300" x-transition:enter-start="translate-y-full md:opacity-0 md:scale-95" x-transition:enter-end="translate-y-0 md:opacity-100 md:scale-100"
         x-transition:leave="transition ease-in duration-200" x-transition:leave-start="translate-y-0 md:opacity-100 md:scale-100" x-transition:leave-end="translate-y-full md:opacity-0 md:scale-95"
         class="bottom-sheet md:max-w-xl md:mx-auto">
        <div class="bottom-sheet-handle md:hidden"></div>
        @include('livewire.inventory._utility-form')
    </div>
</div>

</div>
