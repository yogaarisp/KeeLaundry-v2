<div x-data="{
    showItemForm:  @entangle('showItemForm'),
    showRestock:   @entangle('showRestock'),
    showUtility:   @entangle('showUtilityForm'),
}" class="space-y-5">

{{-- ══ HEADER ══════════════════════════════════════════════════════ --}}
<div class="flex flex-col sm:flex-row sm:items-center gap-3">
    <div>
        <h2 class="text-lg font-bold text-slate-800">Inventaris & Utilitas</h2>
        <p class="text-xs text-slate-400 mt-0.5">Stok bahan baku, opname fisik, dan log utilitas (HPP)</p>
    </div>
    <div class="flex items-center gap-2 sm:ml-auto">
        @if ($activeTab === 'inventory')
        <button wire:click="openCreateItem" class="btn-primary text-sm py-2">
            <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
            Item Baru
        </button>
        @elseif ($activeTab === 'utility')
        <button wire:click="openCreateUtility" class="btn-primary text-sm py-2">
            <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
            Catat Utilitas
        </button>
        @endif
    </div>
</div>

{{-- ══ LOW STOCK ALERT ═══════════════════════════════════════════════ --}}
@if ($this->lowStockItems->isNotEmpty())
<div class="rounded-2xl border-2 border-amber-100 bg-amber-50/50 px-4 py-3">
    <div class="flex items-center gap-2 mb-2">
        <svg class="size-4 text-amber-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/></svg>
        <p class="text-xs font-bold text-amber-700">{{ $this->lowStockItems->count() }} item mendekati atau di bawah stok minimum</p>
    </div>
    <div class="flex flex-wrap gap-2">
        @foreach ($this->lowStockItems as $item)
        <button wire:click="openRestock({{ $item->id }})"
                class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-white border border-amber-200 text-xs font-semibold text-amber-700 hover:bg-amber-50 transition-colors">
            🧴 {{ $item->name }} — {{ number_format($item->system_stock, 0) }} {{ $item->unit }}
            <span class="text-[10px] bg-amber-100 px-1.5 py-0.5 rounded-full">Restock</span>
        </button>
        @endforeach
    </div>
</div>
@endif

{{-- ══ TABS ════════════════════════════════════════════════════════ --}}
<div class="flex gap-1 bg-slate-100 rounded-xl p-1 w-fit flex-wrap">
    @foreach (['inventory' => '🧴 Stok Bahan Baku', 'opname' => '📊 Stok Opname', 'utility' => '⚡ Utilitas & HPP'] as $tab => $label)
    <button wire:click="$set('activeTab','{{ $tab }}')"
            @class(['px-4 py-2 rounded-lg text-xs font-semibold transition-all', 'bg-white shadow-sm text-sky-700' => $activeTab===$tab, 'text-slate-500' => $activeTab!==$tab])>
        {{ $label }}
    </button>
    @endforeach
</div>

{{-- ══ TAB: STOK BAHAN BAKU ═════════════════════════════════════════ --}}
@if ($activeTab === 'inventory')
<div class="space-y-4">
    {{-- Stock bars visualization --}}
    <div class="card p-4 space-y-3.5">
        <p class="text-sm font-bold text-slate-800 mb-2">Visualisasi Tangki Stok (Sabun & Parfum)</p>
        @foreach ($this->stockBarData as $item)
        @php $colors = ['rose' => ['fill' => 'bg-rose-400', 'text' => 'text-rose-600'], 'amber' => ['fill' => 'bg-amber-400', 'text' => 'text-amber-600'], 'emerald' => ['fill' => 'bg-emerald-400', 'text' => 'text-emerald-600']]; $c = $colors[$item['color']]; @endphp
        <div>
            <div class="flex items-center justify-between mb-1">
                <div class="flex items-center gap-2 min-w-0 flex-1">
                    <span @class(['size-2 rounded-full shrink-0', 'bg-sky-400' => $item['category']==='CHEMICAL', 'bg-violet-400' => $item['category']==='PARFUM'])></span>
                    <span class="text-sm font-medium text-slate-700 truncate">{{ $item['name'] }}</span>
                    @if ($item['is_low']) <span class="badge badge-rose text-[10px] shrink-0">⚠ Menipis</span> @endif
                    @if (abs($item['variance']) > 0.1)
                        <span @class(['badge text-[10px] shrink-0', 'badge-rose' => $item['variance'] < 0, 'badge-green' => $item['variance'] > 0])>
                            {{ $item['variance'] > 0 ? '+' : '' }}{{ round($item['variance'],1) }} {{ $item['unit'] }} selisih
                        </span>
                    @endif
                </div>
                <div class="text-right shrink-0 ml-3">
                    <span class="text-sm font-bold tabular-nums {{ $c['text'] }}">{{ number_format($item['system_stock'],0) }}</span>
                    <span class="text-xs text-slate-400"> {{ $item['unit'] }}</span>
                </div>
            </div>
            <div class="stock-bar-track">
                <div class="h-full rounded-full transition-all duration-700 ease-out {{ $c['fill'] }}" style="width: {{ max(3, $item['percent']) }}%"></div>
            </div>
            <div class="flex justify-between mt-1">
                <span class="text-[10px] text-slate-400">Min: {{ number_format($item['min_threshold'],0) }} {{ $item['unit'] }}</span>
                <span class="text-[10px] font-semibold {{ $c['text'] }}">{{ $item['percent'] }}%</span>
            </div>
        </div>
        @endforeach
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
                    <th class="text-right px-4 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wide">Harga/ml</th>
                    <th class="px-4 py-3"></th>
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
                        <div class="flex items-center justify-end gap-1">
                            @if ($item->trashed())
                                <button wire:click="restoreItem({{ $item->id }})" class="btn-secondary text-xs py-1 px-2.5">Pulihkan</button>
                            @else
                                <button wire:click="openRestock({{ $item->id }})" class="btn-secondary text-xs py-1 px-2.5 text-emerald-600">Restock</button>
                                <button wire:click="openEditItem({{ $item->id }})" class="btn-icon">
                                    <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Z"/></svg>
                                </button>
                                <button wire:click="deleteItem({{ $item->id }})" wire:confirm="Hapus item ini?" class="btn-icon">
                                    <svg class="size-4 text-rose-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/></svg>
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
        <div @class(['card p-4 space-y-3', 'opacity-60' => $item->trashed()])>
            <div class="flex items-start justify-between gap-2">
                <div>
                    <p class="font-semibold text-slate-800">{{ $item->name }}</p>
                    <p class="text-xs font-mono text-slate-400">{{ $item->sku }}</p>
                </div>
                <span class="badge {{ $cc }} text-[10px] shrink-0">{{ $item->category }}</span>
            </div>
            <div class="grid grid-cols-3 gap-2 text-xs text-center">
                <div @class(['rounded-xl p-2', 'bg-rose-50' => $item->is_low_stock, 'bg-slate-50' => !$item->is_low_stock])>
                    <p class="text-slate-400">Sistem</p>
                    <p @class(['font-bold', 'text-rose-600' => $item->is_low_stock, 'text-slate-700' => !$item->is_low_stock])>{{ number_format($item->system_stock,1) }} {{ $item->unit }}</p>
                </div>
                <div class="bg-slate-50 rounded-xl p-2"><p class="text-slate-400">Fisik</p><p class="font-bold text-slate-700">{{ number_format($item->actual_stock,1) }}</p></div>
                <div class="bg-slate-50 rounded-xl p-2"><p class="text-slate-400">Min</p><p class="font-bold text-amber-600">{{ number_format($item->min_threshold,0) }}</p></div>
            </div>
            <div class="flex gap-2">
                @if ($item->trashed())
                    <button wire:click="restoreItem({{ $item->id }})" class="btn-secondary text-xs py-1.5 flex-1">Pulihkan</button>
                @else
                    <button wire:click="openRestock({{ $item->id }})" class="btn-secondary text-xs py-1.5 flex-1 text-emerald-600">Restock</button>
                    <button wire:click="openEditItem({{ $item->id }})" class="btn-secondary text-xs py-1.5 flex-1">Edit</button>
                    <button wire:click="deleteItem({{ $item->id }})" wire:confirm="Hapus?" class="btn-danger text-xs py-1.5 px-3">🗑</button>
                @endif
            </div>
        </div>
        @empty
        <div class="card p-12 text-center text-slate-400"><p class="text-3xl mb-2">🧴</p><p>Belum ada item</p></div>
        @endforelse
    </div>

    {{-- Mutation log --}}
    <div class="card p-4">
        <p class="text-sm font-bold text-slate-800 mb-3">Log Mutasi Stok (30 Terakhir)</p>
        <div class="space-y-2 max-h-72 overflow-y-auto scrollbar-thin">
            @foreach ($this->mutationLog as $m)
            @php
                $mtClass = match($m->mutation_type){ 'RESTOCK_IN' => 'badge-green', 'AUTO_DEDUCT_ORDER' => 'badge-rose', 'VOID_ROLLBACK' => 'badge-amber', default => 'badge-slate' };
                $amtClass = (float)$m->amount > 0 ? 'text-emerald-600' : 'text-rose-600';
            @endphp
            <div class="flex items-center gap-3 py-1.5 border-b border-slate-100 last:border-0 text-sm">
                <span class="badge {{ $mtClass }} text-[10px] shrink-0">{{ $m->mutation_type }}</span>
                <span class="flex-1 text-slate-600 text-xs truncate">{{ $m->inventoryItem?->name }}</span>
                @if ($m->reference_invoice) <span class="text-[10px] font-mono text-slate-400 shrink-0">{{ $m->reference_invoice }}</span> @endif
                <span class="font-bold tabular-nums {{ $amtClass }} shrink-0">{{ (float)$m->amount > 0 ? '+' : '' }}{{ round($m->amount, 1) }} {{ $m->inventoryItem?->unit }}</span>
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
    <div class="rounded-2xl border border-sky-100 bg-sky-50/50 px-4 py-3 text-sm text-sky-700">
        <p class="font-semibold">Petunjuk Stok Opname</p>
        <p class="text-xs mt-1 text-sky-600">Masukkan jumlah stok fisik yang ada di gudang. Sistem akan menghitung selisih (<em>variance</em>) antara stok fisik vs stok sistem untuk mendeteksi pemborosan.</p>
    </div>

    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50/80 border-b border-slate-200/80">
                    <tr>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wide">Item</th>
                        <th class="text-right px-4 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wide">Stok Sistem</th>
                        <th class="text-center px-4 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wide">Stok Fisik (Input)</th>
                        <th class="text-right px-4 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wide">Selisih / Variance</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach ($this->inventoryItems->filter(fn($i) => !$i->trashed()) as $item)
                    @php
                        $inputVal = $opnameValues[$item->id] ?? (float)$item->actual_stock;
                        $variance = round($inputVal - (float)$item->system_stock, 2);
                    @endphp
                    <tr class="hover:bg-slate-50/60">
                        <td class="px-4 py-3">
                            <p class="font-semibold text-slate-800">{{ $item->name }}</p>
                            <p class="text-xs text-slate-400">{{ $item->category }}</p>
                        </td>
                        <td class="px-4 py-3 text-right tabular-nums font-semibold text-slate-700">
                            {{ number_format($item->system_stock, 1) }} {{ $item->unit }}
                        </td>
                        <td class="px-4 py-3">
                            <div class="relative max-w-[140px] mx-auto">
                                <input type="number" step="0.1" min="0"
                                       wire:model.live="opnameValues.{{ $item->id }}"
                                       class="form-input text-center pr-8 font-semibold">
                                <span class="absolute right-2.5 top-1/2 -translate-y-1/2 text-[10px] text-slate-400">{{ $item->unit }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-right">
                            @if (abs($variance) < 0.01)
                                <span class="badge badge-green">0 ✓</span>
                            @elseif ($variance < 0)
                                <div class="flex items-center justify-end gap-1">
                                    <span class="badge badge-rose">{{ $variance }} {{ $item->unit }}</span>
                                    <span class="text-[10px] text-rose-500">Boros</span>
                                </div>
                            @else
                                <span class="badge badge-sky">+{{ $variance }} {{ $item->unit }}</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="flex gap-3 justify-end">
        @if ($opnameSaved)
        <div class="flex items-center gap-2 text-emerald-600 text-sm font-semibold">
            <svg class="size-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
            Opname tersimpan!
        </div>
        @endif
        <button wire:click="saveOpname" wire:loading.attr="disabled" class="btn-primary">
            <span wire:loading.remove wire:target="saveOpname">💾 Simpan Hasil Opname</span>
            <span wire:loading wire:target="saveOpname">Menyimpan…</span>
        </button>
    </div>
</div>
@endif

{{-- ══ TAB: UTILITAS & HPP ══════════════════════════════════════════ --}}
@if ($activeTab === 'utility')
<div class="space-y-4">
    {{-- HPP utility stats --}}
    @php $ustats = $this->utilityStats; @endphp
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
        <div class="kpi-card border-l-4 border-l-sky-400">
            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide">HPP Utilitas / Kg</p>
            <p class="text-2xl font-bold text-sky-700 mt-1 tabular-nums">Rp {{ number_format($this->hppUtilityPerKg) }}</p>
            <p class="text-xs text-slate-400 mt-0.5">Bulan berjalan</p>
        </div>
        @foreach ([['⚡ Listrik', $ustats['listrik'], 'badge-amber'], ['💧 Air PDAM', $ustats['air'], 'badge-sky'], ['🔥 Gas LPG', $ustats['gas'], 'badge-rose']] as [$label, $cost, $bc])
        <div class="kpi-card">
            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide">{{ $label }}</p>
            <p class="text-xl font-bold text-slate-800 mt-1 tabular-nums">Rp {{ number_format($cost) }}</p>
            <span class="badge {{ $bc }} text-[10px] mt-1">MTD</span>
        </div>
        @endforeach
    </div>

    <div class="flex items-center gap-2">
        <select wire:model.live="utilityFilter" class="form-input py-2 text-sm w-auto">
            <option value="">Semua Utilitas</option>
            <option value="LISTRIK_PLN">Listrik PLN</option>
            <option value="AIR_PDAM">Air PDAM</option>
            <option value="GAS_LPG">Gas LPG</option>
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
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($this->utilityLogs as $log)
                @php
                    $utClass = match($log->utility_type){ 'LISTRIK_PLN' => 'badge-amber', 'AIR_PDAM' => 'badge-sky', 'GAS_LPG' => 'badge-rose' };
                    $utIcon  = match($log->utility_type){ 'LISTRIK_PLN' => '⚡', 'AIR_PDAM' => '💧', 'GAS_LPG' => '🔥' };
                @endphp
                <tr class="hover:bg-slate-50/60">
                    <td class="px-4 py-3"><span class="badge {{ $utClass }}">{{ $utIcon }} {{ $log->utility_type }}</span></td>
                    <td class="px-4 py-3 text-slate-700">{{ $log->period_date->format('M Y') }}</td>
                    <td class="px-4 py-3 text-right tabular-nums text-slate-500">{{ number_format($log->meter_start,1) }}</td>
                    <td class="px-4 py-3 text-right tabular-nums text-slate-500">{{ number_format($log->meter_end,1) }}</td>
                    <td class="px-4 py-3 text-right tabular-nums font-semibold text-slate-700">{{ number_format($log->usage_volume,1) }} {{ $log->unit }}</td>
                    <td class="px-4 py-3 text-right font-bold text-slate-800 tabular-nums">Rp {{ number_format($log->total_cost) }}</td>
                    <td class="px-4 py-3">
                        <div class="flex items-center justify-end gap-1">
                            <button wire:click="openEditUtility({{ $log->id }})" class="btn-icon">
                                <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Z"/></svg>
                            </button>
                            <button wire:click="deleteUtility({{ $log->id }})" wire:confirm="Hapus log utilitas ini?" class="btn-icon">
                                <svg class="size-4 text-rose-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/></svg>
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
        <div class="card p-4 space-y-2.5">
            <div class="flex items-center justify-between gap-2">
                <div class="flex items-center gap-2">
                    <span class="badge {{ $uc }} text-xs">{{ $ui }} {{ $log->utility_type }}</span>
                    <span class="text-sm text-slate-600 font-semibold">{{ $log->period_date->format('M Y') }}</span>
                </div>
                <span class="font-bold text-slate-800 tabular-nums">Rp {{ number_format($log->total_cost) }}</span>
            </div>
            <div class="grid grid-cols-2 gap-2 text-xs">
                <div class="bg-slate-50 rounded-lg p-2"><span class="text-slate-400">Pemakaian: </span><strong>{{ $log->usage_volume }} {{ $log->unit }}</strong></div>
                <div class="bg-slate-50 rounded-lg p-2"><span class="text-slate-400">Biaya/unit: </span><strong>Rp {{ number_format($log->cost_per_unit) }}</strong></div>
            </div>
            <div class="flex gap-2">
                <button wire:click="openEditUtility({{ $log->id }})" class="btn-secondary text-xs py-1.5 flex-1">Edit</button>
                <button wire:click="deleteUtility({{ $log->id }})" wire:confirm="Hapus?" class="btn-danger text-xs py-1.5 px-3">Hapus</button>
            </div>
        </div>
        @empty
        <div class="card p-12 text-center text-slate-400"><p class="text-3xl mb-2">⚡</p><p>Belum ada log utilitas</p></div>
        @endforelse
    </div>
</div>
@endif

{{-- ══ MODALS ══════════════════════════════════════════════════════ --}}

{{-- Item form --}}
<div x-show="showItemForm" x-cloak style="display:none">
    <div class="bottom-sheet-overlay md:hidden" @click="showItemForm = false"></div>
    <div class="dialog-overlay hidden md:flex" @click.self="showItemForm = false"></div>
    <div x-show="showItemForm" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="translate-y-full" x-transition:enter-end="translate-y-0" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="translate-y-0" x-transition:leave-end="translate-y-full" class="bottom-sheet md:hidden">
        <div class="bottom-sheet-handle"></div>
        @include('livewire.inventory._item-form')
    </div>
    <div x-show="showItemForm" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" class="hidden md:flex fixed inset-0 z-50 items-center justify-center p-4 pointer-events-none">
        <div class="pointer-events-auto w-full max-w-lg bg-white rounded-2xl shadow-2xl overflow-hidden max-h-[90vh] overflow-y-auto">
            @include('livewire.inventory._item-form')
        </div>
    </div>
</div>

{{-- Restock modal --}}
<div x-show="showRestock" x-cloak style="display:none">
    <div class="dialog-overlay" @click.self="showRestock = false">
        <div x-show="showRestock" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" class="dialog-panel p-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-bold text-slate-800">Restock / Stok Masuk</h3>
                <button @click="showRestock = false" class="btn-icon"><svg class="size-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg></button>
            </div>
            @if ($this->restockItem)
            <div class="flex items-center gap-3 rounded-xl bg-emerald-50 border border-emerald-100 p-3 mb-4">
                <span class="text-2xl">🧴</span>
                <div>
                    <p class="font-semibold text-slate-800">{{ $this->restockItem->name }}</p>
                    <p class="text-xs text-emerald-600">Stok saat ini: <strong>{{ $this->restockItem->system_stock }} {{ $this->restockItem->unit }}</strong></p>
                </div>
            </div>
            <div class="space-y-3">
                <div>
                    <label class="form-label">Tambah Stok ({{ $this->restockItem->unit }})</label>
                    <div class="flex gap-2 mb-2 flex-wrap">
                        @foreach ([1000, 5000, 10000] as $p)
                        <button wire:click="$set('restockAmount', {{ $p }})"
                                @class(['px-3 py-1.5 rounded-xl text-xs font-semibold border transition-all', 'border-sky-400 bg-sky-50 text-sky-700' => $restockAmount == $p, 'border-slate-200 text-slate-500' => $restockAmount != $p])>
                            +{{ number_format($p) }} {{ $this->restockItem->unit }}
                        </button>
                        @endforeach
                    </div>
                    <div class="relative"><input type="number" wire:model.live="restockAmount" min="0.1" class="form-input pr-12"><span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-slate-400">{{ $this->restockItem->unit }}</span></div>
                    @error('restockAmount') <p class="form-error">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="form-label">Keterangan (opsional)</label>
                    <input type="text" wire:model="restockNotes" class="form-input" placeholder="cth. Beli jerigen 20L di toko X">
                </div>
                @if ($restockAmount > 0)
                <div class="rounded-xl bg-sky-50 border border-sky-100 px-3 py-2 text-xs text-sky-700">
                    Stok setelah restock: <strong>{{ round((float)$this->restockItem->system_stock + $restockAmount, 1) }} {{ $this->restockItem->unit }}</strong>
                </div>
                @endif
            </div>
            @endif
            <div class="flex gap-3 mt-5">
                <button wire:click="saveRestock" class="btn-primary flex-1">Tambah Stok</button>
                <button @click="showRestock = false" class="btn-secondary">Batal</button>
            </div>
        </div>
    </div>
</div>

{{-- Utility form --}}
<div x-show="showUtility" x-cloak style="display:none">
    <div class="bottom-sheet-overlay md:hidden" @click="showUtility = false"></div>
    <div class="dialog-overlay hidden md:flex" @click.self="showUtility = false"></div>
    <div x-show="showUtility" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="translate-y-full" x-transition:enter-end="translate-y-0" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="translate-y-0" x-transition:leave-end="translate-y-full" class="bottom-sheet md:hidden">
        <div class="bottom-sheet-handle"></div>
        @include('livewire.inventory._utility-form')
    </div>
    <div x-show="showUtility" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" class="hidden md:flex fixed inset-0 z-50 items-center justify-center p-4 pointer-events-none">
        <div class="pointer-events-auto w-full max-w-lg bg-white rounded-2xl shadow-2xl overflow-hidden max-h-[90vh] overflow-y-auto">
            @include('livewire.inventory._utility-form')
        </div>
    </div>
</div>

</div>
