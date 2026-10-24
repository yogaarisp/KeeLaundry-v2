<div x-data="{
    showForm:      @entangle('showForm'),
    showTopUp:     @entangle('showTopUp'),
    showDetail:    @entangle('showDetail'),
    showComplaint: @entangle('showComplaintForm'),
    showDelete:    @entangle('showDeleteConfirm'),
    showTierModal: @entangle('showTierModal'),
}" class="space-y-5">

{{-- ══ HEADER ══════════════════════════════════════════════════════ --}}
<div class="flex items-center justify-between gap-3">
    <div>
        <h2 class="text-lg sm:text-xl font-bold text-slate-800 leading-tight">CRM Pelanggan</h2>
        <p class="text-xs text-slate-400 mt-0.5 hidden sm:block">Kelola pelanggan, deposit Kg, dan tiket komplain</p>
    </div>
    <div class="flex items-center gap-2 shrink-0">
        <button wire:click="openCreate" class="btn-primary text-xs sm:text-sm py-2 px-3 sm:px-4 flex items-center gap-1.5 shadow-sm">
            <svg class="size-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M18 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0ZM3 19.235v-.11a6.375 6.375 0 0 1 12.75 0v.109A12.318 12.318 0 0 1 9.374 21c-2.331 0-4.512-.645-6.374-1.766Z"/></svg>
            <span class="sm:hidden font-semibold">+ Pelanggan</span>
            <span class="hidden sm:inline font-semibold">Tambah Pelanggan</span>
        </button>
    </div>
</div>

{{-- ══ TIER SUMMARY CARDS (INTERACTIVE: QUICK FILTER ON DESKTOP, INSPECT DRAWER ON MOBILE) ══ --}}
<div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5">
    @php
        $tierMeta = [
            'REGULAR' => ['badge-slate', '💼', 'Pelanggan umum cuci kiloan & satuan'],
            'SILVER'  => ['badge-sky',   '🥈', 'Langganan rutin dengan deposit Kg'],
            'VIP'     => ['badge-amber', '👑', 'Pelanggan prioritas & volume besar'],
        ];
    @endphp
    @foreach ($tierMeta as $tier => [$badgeCls, $icon, $desc])
        @php
            $stat = $this->tierStats[$tier] ?? ['total' => 0, 'active' => 0];
            $isSelected = ($activeTab === 'customers' && $filterTier === $tier);
        @endphp
        <button type="button"
                @click="if (window.innerWidth < 768) { $wire.openTierInspect('{{ $tier }}') } else { $wire.set('activeTab', 'customers'); $wire.set('filterTier', '{{ $isSelected ? '' : $tier }}') }"
                @class([
                    'card p-4 sm:p-5 text-left transition-all group focus:outline-none focus:ring-2 focus:ring-sky-500/20',
                    'border-sky-500 ring-2 ring-sky-500/20 bg-sky-50/40' => $isSelected,
                    'hover:border-sky-300 hover:shadow-md' => !$isSelected,
                ])>
            <div class="flex items-start justify-between">
                <div class="flex items-center gap-3">
                    <span class="text-3xl group-hover:scale-110 transition-transform">{{ $icon }}</span>
                    <div>
                        <p class="text-xs font-bold text-slate-500 uppercase tracking-wide group-hover:text-sky-700 transition-colors">Tier {{ $tier }}</p>
                        <p class="text-2xl font-bold text-slate-800 mt-0.5">{{ $stat['total'] ?? 0 }} <span class="text-xs font-normal text-slate-400">pelanggan</span></p>
                    </div>
                </div>
                <div class="flex flex-col items-end gap-1">
                    <span class="badge {{ $badgeCls }}">{{ $stat['active'] ?? 0 }} aktif</span>
                    @if ($isSelected)
                        <span class="text-[10px] font-semibold text-sky-600 hidden md:inline">✓ Aktif difilter</span>
                    @endif
                </div>
            </div>
            <div class="mt-3.5 pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-400">
                <span class="truncate">{{ $desc }}</span>
                <span class="text-sky-600 font-semibold group-hover:translate-x-1 transition-transform flex items-center gap-1 shrink-0 ml-1">
                    <span class="md:hidden">Buka Pelanggan</span>
                    <span class="hidden md:inline">{{ $isSelected ? 'Reset Filter' : 'Filter Tabel' }}</span>
                    <svg class="size-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/></svg>
                </span>
            </div>
        </button>
    @endforeach
</div>

{{-- ══ TABS (SEGMENTED CONTROL: 3-COL EQUAL GRID ON MOBILE, COMPACT FIT ON DESKTOP) ══ --}}
<div class="grid grid-cols-3 sm:flex sm:w-fit w-full bg-slate-100 rounded-xl p-1 gap-1">
    {{-- Tab 1: Semua Pelanggan --}}
    <button wire:click="$set('activeTab','customers')"
            @class([
                'flex items-center justify-center gap-1.5 py-2 px-2 sm:px-4 rounded-lg text-xs font-semibold transition-all',
                'bg-white shadow-xs text-sky-700' => $activeTab === 'customers',
                'text-slate-500 hover:text-slate-700' => $activeTab !== 'customers',
            ])>
        <span class="sm:hidden">Semua</span>
        <span class="hidden sm:inline">👥 Semua Pelanggan</span>
    </button>

    {{-- Tab 2: Pelanggan Pasif --}}
    <button wire:click="$set('activeTab','churn')"
            @class([
                'flex items-center justify-center gap-1.5 py-2 px-2 sm:px-4 rounded-lg text-xs font-semibold transition-all',
                'bg-white shadow-xs text-sky-700' => $activeTab === 'churn',
                'text-slate-500 hover:text-slate-700' => $activeTab !== 'churn',
            ])>
        <span class="sm:hidden">Pasif</span>
        <span class="hidden sm:inline">🚨 Pelanggan Pasif</span>
        @if ($this->churnCustomers->count() > 0)
            <span class="px-1.5 py-0.2 rounded-full text-[10px] font-bold bg-rose-100 text-rose-700 leading-none">
                {{ $this->churnCustomers->count() }}
            </span>
        @endif
    </button>

    {{-- Tab 3: Komplain --}}
    @php
        $pendingComplaints = ($this->complaintStats['OPEN'] ?? 0) + ($this->complaintStats['IN_PROGRESS'] ?? 0);
    @endphp
    <button wire:click="$set('activeTab','complaints')"
            @class([
                'flex items-center justify-center gap-1.5 py-2 px-2 sm:px-4 rounded-lg text-xs font-semibold transition-all',
                'bg-white shadow-xs text-sky-700' => $activeTab === 'complaints',
                'text-slate-500 hover:text-slate-700' => $activeTab !== 'complaints',
            ])>
        <span class="sm:hidden">Komplain</span>
        <span class="hidden sm:inline">💬 Komplain</span>
        @if ($pendingComplaints > 0)
            <span class="px-1.5 py-0.2 rounded-full text-[10px] font-bold bg-amber-100 text-amber-700 leading-none">
                {{ $pendingComplaints }}
            </span>
        @endif
    </button>
</div>

{{-- ══ TAB: PELANGGAN ════════════════════════════════════════════════ --}}
@if ($activeTab === 'customers')
<div class="space-y-4">
    {{-- Search + filter --}}
    <div class="flex gap-2">
        <div class="relative flex-1 min-w-0">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 size-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/></svg>
            <input type="search" wire:model.live.debounce.300ms="search" placeholder="Cari nama atau no. HP…" class="form-input pl-9 py-2 text-sm w-full">
        </div>
        <select wire:model.live="filterTier" class="form-input py-2 text-sm w-28 sm:w-auto shrink-0">
            <option value="">Semua Tier</option>
            <option value="REGULAR">Regular</option>
            <option value="SILVER">Silver</option>
            <option value="VIP">VIP</option>
        </select>
    </div>

    {{-- Desktop table --}}
    <div class="card hidden md:block overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-50/80 border-b border-slate-200/80">
                <tr>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wide">Pelanggan</th>
                    <th class="text-center px-4 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wide">Tier</th>
                    <th class="text-right px-4 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wide">Deposit Kg</th>
                    <th class="text-center px-4 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wide">Terakhir Order</th>
                    <th class="text-center px-4 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wide">Poin</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($this->customers as $c)
                <tr @class(['hover:bg-slate-50/60 transition-colors', 'opacity-50' => $c->trashed()])>
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-3">
                            <div class="size-9 rounded-full bg-gradient-to-br from-sky-100 to-sky-200 flex items-center justify-center shrink-0">
                                <span class="text-sm font-bold text-sky-700">{{ mb_strtoupper(mb_substr($c->name, 0, 1)) }}</span>
                            </div>
                            <div>
                                <p class="font-semibold text-slate-800">{{ $c->name }}</p>
                                <p class="text-xs text-slate-400">{{ $c->phone }}</p>
                                @if ($c->preferences)
                                    <p class="text-[11px] text-amber-600 italic truncate max-w-[200px]">★ {{ $c->preferences }}</p>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-3 text-center">
                        @php $tierClass = match($c->tier){ 'VIP' => 'badge-sky', 'SILVER' => 'badge-slate', default => 'badge-slate' }; @endphp
                        <span class="badge {{ $tierClass }}">{{ $c->tier }}</span>
                    </td>
                    <td class="px-4 py-3 text-right">
                        @if ($c->deposit_kg > 0)
                            <span class="font-bold text-emerald-600 tabular-nums">{{ $c->deposit_kg }} Kg</span>
                        @else
                            <span class="text-slate-300">—</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-center text-xs">
                        @if ($c->last_order_date)
                            @php $days = $c->days_since_last_order ?? 0; @endphp
                            <span @class(['badge text-[10px]', 'badge-rose' => $days > 30, 'badge-amber' => $days > 14 && $days <= 30, 'badge-green' => $days <= 14])>
                                {{ $c->last_order_human }}
                            </span>
                        @else
                            <span class="badge badge-slate text-[10px]">Baru</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-center text-sm font-semibold text-slate-600 tabular-nums">{{ number_format($c->loyalty_points) }}</td>
                    <td class="px-4 py-3">
                        <div class="flex items-center justify-end gap-1">
                            @if ($c->trashed())
                                <button wire:click="restoreCustomer({{ $c->id }})" class="btn-secondary text-xs py-1 px-2.5">Pulihkan</button>
                            @else
                                <button wire:click="openDetail({{ $c->id }})" class="btn-icon" title="Detail">
                                    <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/></svg>
                                </button>
                                <button wire:click="openTopUp({{ $c->id }})" class="btn-icon" title="Top-Up Deposit">
                                    <svg class="size-4 text-emerald-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z"/></svg>
                                </button>
                                <button wire:click="openEdit({{ $c->id }})" class="btn-icon" title="Edit">
                                    <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Z"/></svg>
                                </button>
                                <button wire:click="confirmDelete({{ $c->id }})" class="btn-icon" title="Hapus">
                                    <svg class="size-4 text-rose-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/></svg>
                                </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-4 py-12 text-center text-slate-400">
                    <p class="text-2xl mb-2">👥</p><p>Belum ada pelanggan terdaftar</p>
                </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Mobile cards (Clean, Minimalist & Native Mobile Feel) --}}
    <div class="md:hidden space-y-2.5">
        @forelse ($this->customers as $c)
        <div @class([
            'bg-white rounded-xl p-3.5 border border-slate-200/90 shadow-xs space-y-2.5 transition-all',
            'opacity-60 bg-slate-50/50' => $c->trashed(),
        ])>
            {{-- Row 1: Avatar, Name, Phone & Tier Badge --}}
            <div class="flex items-start justify-between gap-2.5">
                <div class="flex items-center gap-2.5 min-w-0 flex-1">
                    <div class="size-10 rounded-full bg-gradient-to-br from-sky-100 to-sky-200 flex items-center justify-center shrink-0">
                        <span class="font-bold text-sky-700 text-sm">{{ mb_strtoupper(mb_substr($c->name, 0, 1)) }}</span>
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center gap-1.5">
                            <h4 class="font-bold text-slate-800 text-sm truncate leading-snug">{{ $c->name }}</h4>
                            @php $tc = match($c->tier){ 'VIP' => 'badge-sky', 'SILVER' => 'badge-slate', default => 'badge-slate' }; @endphp
                            <span class="badge {{ $tc }} text-[10px] shrink-0 font-semibold">{{ $c->tier }}</span>
                        </div>
                        <div class="flex items-center gap-2 text-xs text-slate-400 mt-0.5">
                            <span>{{ $c->phone }}</span>
                            <a href="https://wa.me/62{{ ltrim($c->phone,'0') }}"
                               target="_blank"
                               class="text-emerald-600 hover:text-emerald-700 font-medium inline-flex items-center gap-0.5"
                               title="Chat WhatsApp">
                                <svg class="size-3" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
                                <span>WA</span>
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Activity Indicator --}}
                <div class="shrink-0 text-right">
                    @if ($c->is_churn)
                        <span class="badge badge-rose text-[10px] font-semibold">⚠️ Pasif</span>
                    @elseif ($c->last_order_date)
                        @php $days = $c->days_since_last_order ?? 0; @endphp
                        <span @class(['badge text-[10px]', 'badge-green' => $days <= 14, 'badge-amber' => $days > 14])>
                            {{ $c->last_order_human }}
                        </span>
                    @else
                        <span class="badge badge-slate text-[10px]">Baru</span>
                    @endif
                </div>
            </div>

            {{-- Row 2: Metrics Chips (Deposit, Poin, & Preferensi) --}}
            <div class="flex items-center justify-between gap-2 pt-1 border-t border-slate-100 text-xs">
                <div class="flex items-center gap-1.5 flex-wrap">
                    @if ($c->deposit_kg > 0)
                        <span class="inline-flex items-center gap-1 font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-md border border-emerald-200/60 font-mono text-[11px]">
                            💰 {{ (float)$c->deposit_kg }} Kg
                        </span>
                    @else
                        <span class="text-[11px] text-slate-400">0 Kg deposit</span>
                    @endif
                    <span class="text-slate-300">·</span>
                    <span class="text-[11px] text-slate-500 font-medium">⭐ {{ number_format($c->loyalty_points) }} pts</span>
                </div>

                @if ($c->preferences)
                    <span class="text-[11px] text-amber-600 truncate max-w-[130px] italic">★ {{ $c->preferences }}</span>
                @endif
            </div>

            {{-- Row 3: Action Buttons (Minimalist & Thumb-friendly) --}}
            <div class="flex items-center justify-end gap-1.5 pt-1.5 border-t border-dashed border-slate-100">
                @if ($c->trashed())
                    <button wire:click="restoreCustomer({{ $c->id }})" class="btn-secondary text-xs py-1 px-3">Pulihkan</button>
                @else
                    <button wire:click="openDetail({{ $c->id }})"
                            class="flex items-center gap-1 text-xs font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 px-3 py-1.5 rounded-lg transition-colors">
                        <svg class="size-3.5 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/></svg>
                        <span>Detail</span>
                    </button>

                    <button wire:click="openTopUp({{ $c->id }})"
                            class="flex items-center gap-1 text-xs font-semibold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200/80 px-2.5 py-1.5 rounded-lg transition-colors">
                        <span>+ Top-Up</span>
                    </button>

                    <button wire:click="openEdit({{ $c->id }})"
                            class="flex items-center gap-1 text-xs font-medium text-slate-600 hover:text-slate-900 bg-white border border-slate-200 px-2.5 py-1.5 rounded-lg transition-colors"
                            title="Edit data pelanggan">
                        <svg class="size-3.5 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125"/></svg>
                        <span>Edit</span>
                    </button>

                    <button wire:click="confirmDelete({{ $c->id }})"
                            class="p-1.5 text-rose-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors ml-auto"
                            title="Hapus pelanggan">
                        <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/></svg>
                    </button>
                @endif
            </div>
        </div>
        @empty
        <div class="card p-12 text-center text-slate-400">
            <p class="text-3xl mb-2">👥</p>
            <p class="font-semibold text-slate-700">Belum ada pelanggan terdaftar</p>
        </div>
        @endforelse
    </div>
</div>
@endif

{{-- ══ TAB: CHURN ALERT ══════════════════════════════════════════════ --}}
@if ($activeTab === 'churn')
<div class="space-y-3">
    <div class="rounded-2xl border-2 border-rose-100 bg-rose-50/50 px-4 py-3 flex items-center gap-3">
        <svg class="size-5 text-rose-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/></svg>
        <p class="text-sm font-semibold text-rose-700">{{ $this->churnCustomers->count() }} pelanggan tidak order lebih dari 14 hari</p>
    </div>

    @forelse ($this->churnCustomers as $c)
    <div class="card p-4 flex items-center gap-3">
        <div class="size-11 rounded-full bg-rose-50 flex items-center justify-center shrink-0">
            <span class="font-bold text-rose-500">{{ mb_strtoupper(mb_substr($c->name, 0, 1)) }}</span>
        </div>
        <div class="flex-1 min-w-0">
            <div class="flex items-center gap-2 flex-wrap">
                <p class="font-semibold text-slate-800">{{ $c->name }}</p>
                @php $tc = match($c->tier){ 'VIP' => 'badge-sky', 'SILVER' => 'badge-slate', default => 'badge-slate' }; @endphp
                <span class="badge {{ $tc }} text-[10px]">{{ $c->tier }}</span>
                @if ($c->deposit_kg > 0)
                    <span class="badge badge-green text-[10px]">💰 {{ $c->deposit_kg }} Kg deposit</span>
                @endif
            </div>
            <p class="text-xs text-slate-400 mt-0.5">{{ $c->phone }}</p>
            <p class="text-xs mt-1">
                @if ($c->last_order_date)
                    @php $days = $c->days_since_last_order ?? 0; @endphp
                    <span @class(['badge text-[10px]', 'badge-rose' => $days > 30, 'badge-amber' => $days <= 30])>{{ $c->last_order_human }}</span>
                @else
                    <span class="badge badge-slate text-[10px]">Belum pernah order</span>
                @endif
            </p>
        </div>
        <div class="flex flex-col sm:flex-row gap-2 shrink-0">
            <select wire:change="upgradeTier({{ $c->id }}, $event.target.value)"
                    class="form-input py-1 text-xs w-auto">
                @foreach (['REGULAR', 'SILVER', 'VIP'] as $tier)
                    <option value="{{ $tier }}" @selected($c->tier === $tier)>{{ $tier }}</option>
                @endforeach
            </select>
            <a href="https://wa.me/62{{ ltrim($c->phone,'0') }}?text={{ urlencode('Halo ' . $c->name . '! Kee Laundry kangen cucian Anda. Ada promo spesial hari ini 🧺') }}"
               target="_blank"
               class="flex items-center justify-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-500 text-white text-xs font-semibold hover:bg-emerald-600 transition-colors">
                <svg class="size-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
                Kirim WA
            </a>
        </div>
    </div>
    @empty
    <div class="card p-12 text-center text-slate-400">
        <p class="text-3xl mb-2">✨</p>
        <p class="font-medium">Semua pelanggan aktif!</p>
        <p class="text-xs mt-1">Tidak ada yang perlu dihubungi saat ini.</p>
    </div>
    @endforelse
</div>
@endif

{{-- ══ TAB: KOMPLAIN ═════════════════════════════════════════════════ --}}
@if ($activeTab === 'complaints')
<div class="space-y-4">
    {{-- Interactive Status Filter Pills & Search Toolbar --}}
    <div class="space-y-3">
        {{-- Row 1: Interactive Status Filter Pills (Horizontal scroll on mobile, wrap on desktop) --}}
        <div class="flex items-center gap-1.5 overflow-x-auto pb-0.5 scrollbar-none text-xs">
            @php
                $totalComplaints = array_sum($this->complaintStats);
                $openCount = $this->complaintStats['OPEN'] ?? 0;
                $inProgressCount = $this->complaintStats['IN_PROGRESS'] ?? 0;
                $resolvedCount = $this->complaintStats['RESOLVED'] ?? 0;
            @endphp

            {{-- Semua --}}
            <button type="button" wire:click="$set('complaintFilter', '')"
                    @class([
                        'px-3 py-1.5 rounded-lg font-semibold transition-all shrink-0 flex items-center gap-1.5',
                        'bg-slate-800 text-white shadow-xs' => empty($complaintFilter),
                        'bg-slate-100 text-slate-600 hover:bg-slate-200' => !empty($complaintFilter),
                    ])>
                <span>Semua</span>
                <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ empty($complaintFilter) ? 'bg-slate-700 text-slate-200' : 'bg-slate-200 text-slate-600' }}">
                    {{ $totalComplaints }}
                </span>
            </button>

            {{-- Open --}}
            <button type="button" wire:click="$set('complaintFilter', 'OPEN')"
                    @class([
                        'px-3 py-1.5 rounded-lg font-semibold transition-all shrink-0 flex items-center gap-1.5',
                        'bg-rose-600 text-white shadow-xs' => $complaintFilter === 'OPEN',
                        'bg-rose-50 text-rose-700 hover:bg-rose-100 border border-rose-200/60' => $complaintFilter !== 'OPEN',
                    ])>
                <span class="size-2 rounded-full bg-rose-500 {{ $complaintFilter === 'OPEN' ? 'ring-2 ring-white/50' : '' }}"></span>
                <span>Open</span>
                <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ $complaintFilter === 'OPEN' ? 'bg-rose-700 text-white' : 'bg-rose-100 text-rose-800' }}">
                    {{ $openCount }}
                </span>
            </button>

            {{-- Diproses --}}
            <button type="button" wire:click="$set('complaintFilter', 'IN_PROGRESS')"
                    @class([
                        'px-3 py-1.5 rounded-lg font-semibold transition-all shrink-0 flex items-center gap-1.5',
                        'bg-amber-600 text-white shadow-xs' => $complaintFilter === 'IN_PROGRESS',
                        'bg-amber-50 text-amber-800 hover:bg-amber-100 border border-amber-200/60' => $complaintFilter !== 'IN_PROGRESS',
                    ])>
                <span class="size-2 rounded-full bg-amber-500 {{ $complaintFilter === 'IN_PROGRESS' ? 'ring-2 ring-white/50' : '' }}"></span>
                <span>Diproses</span>
                <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ $complaintFilter === 'IN_PROGRESS' ? 'bg-amber-700 text-white' : 'bg-amber-100 text-amber-900' }}">
                    {{ $inProgressCount }}
                </span>
            </button>

            {{-- Selesai --}}
            <button type="button" wire:click="$set('complaintFilter', 'RESOLVED')"
                    @class([
                        'px-3 py-1.5 rounded-lg font-semibold transition-all shrink-0 flex items-center gap-1.5',
                        'bg-emerald-600 text-white shadow-xs' => $complaintFilter === 'RESOLVED',
                        'bg-emerald-50 text-emerald-700 hover:bg-emerald-100 border border-emerald-200/60' => $complaintFilter !== 'RESOLVED',
                    ])>
                <span class="size-2 rounded-full bg-emerald-500 {{ $complaintFilter === 'RESOLVED' ? 'ring-2 ring-white/50' : '' }}"></span>
                <span>Selesai</span>
                <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ $complaintFilter === 'RESOLVED' ? 'bg-emerald-700 text-white' : 'bg-emerald-100 text-emerald-800' }}">
                    {{ $resolvedCount }}
                </span>
            </button>
        </div>

        {{-- Row 2: Search Input & Action Button --}}
        <div class="flex items-center gap-2">
            <div class="relative flex-1 min-w-0">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 size-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/></svg>
                <input type="search" wire:model.live.debounce.300ms="complaintSearch" placeholder="Cari invoice, pelanggan, atau masalah…" class="form-input pl-9 py-2 text-sm w-full">
            </div>
            <button wire:click="openCreateComplaint()" class="btn-primary text-xs sm:text-sm py-2 px-3 sm:px-4 flex items-center gap-1.5 shrink-0 whitespace-nowrap shadow-sm">
                <svg class="size-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                <span class="sm:hidden font-semibold">Tiket</span>
                <span class="hidden sm:inline font-semibold">Tiket Baru</span>
            </button>
        </div>
    </div>

    <div class="card overflow-hidden">
        <table class="w-full text-sm hidden md:table">
            <thead class="bg-slate-50/80 border-b border-slate-200/80">
                <tr>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wide">Invoice</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wide">Pelanggan</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wide">Masalah</th>
                    <th class="text-center px-4 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wide">Status</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($this->complaints as $complaint)
                @php $stClass = match($complaint->status){ 'RESOLVED' => 'badge-green', 'IN_PROGRESS' => 'badge-amber', default => 'badge-rose' }; @endphp
                <tr class="hover:bg-slate-50/60">
                    <td class="px-4 py-3 font-mono text-xs font-semibold text-slate-600">{{ $complaint->invoice_number }}</td>
                    <td class="px-4 py-3">
                        <p class="font-semibold text-slate-800">{{ $complaint->customer?->name }}</p>
                        <p class="text-xs text-slate-400">{{ $complaint->customer?->phone }}</p>
                    </td>
                    <td class="px-4 py-3">
                        <p class="font-medium text-slate-700">{{ $complaint->issue_type }}</p>
                        <p class="text-xs text-slate-400 truncate max-w-[200px]">{{ $complaint->description }}</p>
                        @if ($complaint->compensation)
                            <p class="text-xs text-emerald-600 mt-0.5">🎁 {{ $complaint->compensation }}</p>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-center"><span class="badge {{ $stClass }}">{{ $complaint->status }}</span></td>
                    <td class="px-4 py-3">
                        <div class="flex items-center justify-end gap-1">
                            @if ($complaint->status !== 'RESOLVED')
                                <button wire:click="resolveComplaint({{ $complaint->id }})" class="btn-secondary text-xs py-1 px-2.5 text-emerald-600">Resolve</button>
                            @endif
                            <button wire:click="openEditComplaint({{ $complaint->id }})" class="btn-icon">
                                <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Z"/></svg>
                            </button>
                            <button wire:click="deleteComplaint({{ $complaint->id }})" wire:confirm="Hapus tiket komplain ini?" class="btn-icon">
                                <svg class="size-4 text-rose-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/></svg>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="px-4 py-12 text-center text-slate-400">
                    <p class="text-2xl mb-2">💬</p><p>Tidak ada komplain</p>
                </td></tr>
                @endforelse
            </tbody>
        </table>

        {{-- Mobile complaint cards (Sleek & Compact) --}}
        <div class="md:hidden divide-y divide-slate-100">
            @forelse ($this->complaints as $complaint)
            @php $stClass = match($complaint->status){ 'RESOLVED' => 'badge-green', 'IN_PROGRESS' => 'badge-amber', default => 'badge-rose' }; @endphp
            <div class="p-3.5 space-y-2.5">
                <div class="flex items-start justify-between gap-2">
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center gap-2">
                            <span class="font-mono text-xs font-bold text-slate-700">{{ $complaint->invoice_number }}</span>
                            <span class="badge {{ $stClass }} text-[10px] shrink-0 font-semibold">{{ $complaint->status }}</span>
                        </div>
                        <p class="font-bold text-slate-800 text-sm mt-0.5">{{ $complaint->customer?->name }}</p>
                        <p class="text-xs font-semibold text-rose-600 mt-0.5">⚠️ {{ $complaint->issue_type }}</p>
                        <p class="text-xs text-slate-500 mt-0.5 leading-relaxed">{{ Str::limit($complaint->description, 80) }}</p>
                        @if ($complaint->compensation)
                            <p class="text-xs text-emerald-600 font-medium mt-1">🎁 {{ $complaint->compensation }}</p>
                        @endif
                    </div>
                </div>
                <div class="flex items-center justify-end gap-1.5 pt-1.5 border-t border-dashed border-slate-100">
                    @if ($complaint->status !== 'RESOLVED')
                        <button wire:click="resolveComplaint({{ $complaint->id }})"
                                class="flex items-center gap-1 text-xs font-semibold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200/80 px-2.5 py-1.5 rounded-lg transition-colors">
                            <span>✓ Selesai</span>
                        </button>
                    @endif
                    <button wire:click="openEditComplaint({{ $complaint->id }})"
                            class="flex items-center gap-1 text-xs font-medium text-slate-600 hover:text-slate-900 bg-white border border-slate-200 px-2.5 py-1.5 rounded-lg transition-colors">
                        <svg class="size-3.5 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125"/></svg>
                        <span>Edit</span>
                    </button>
                    <button wire:click="deleteComplaint({{ $complaint->id }})" wire:confirm="Hapus tiket komplain ini?"
                            class="p-1.5 text-rose-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors ml-auto"
                            title="Hapus komplain">
                        <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/></svg>
                    </button>
                </div>
            </div>
            @empty
            <div class="p-12 text-center text-slate-400"><p class="text-2xl mb-2">💬</p><p>Tidak ada komplain</p></div>
            @endforelse
        </div>
    </div>
</div>
@endif

{{-- ══ MODALS ══════════════════════════════════════════════════════ --}}

{{-- Customer Form --}}
<div x-show="showForm" x-cloak style="display:none">
    <div class="bottom-sheet-overlay md:hidden" @click="showForm = false"></div>
    <div class="dialog-overlay hidden md:flex" @click.self="showForm = false"></div>
    <div x-show="showForm" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="translate-y-full" x-transition:enter-end="translate-y-0" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="translate-y-0" x-transition:leave-end="translate-y-full" class="bottom-sheet md:hidden">
        <div class="bottom-sheet-handle"></div>
        @include('livewire.crm._customer-form')
    </div>
    <div x-show="showForm" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" class="hidden md:flex fixed inset-0 z-50 items-center justify-center p-4 pointer-events-none">
        <div class="pointer-events-auto w-full max-w-lg bg-white rounded-2xl shadow-2xl overflow-hidden max-h-[90vh] overflow-y-auto">
            @include('livewire.crm._customer-form')
        </div>
    </div>
</div>

{{-- Top-Up Deposit (Responsive: Bottom Sheet on Mobile, Centered Dialog on Desktop) --}}
<div x-show="showTopUp" x-cloak style="display:none">
    <div class="bottom-sheet-overlay md:hidden" @click="showTopUp = false"></div>
    <div class="dialog-overlay hidden md:flex" @click.self="showTopUp = false"></div>

    {{-- Mobile: Bottom Sheet --}}
    <div x-show="showTopUp"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="translate-y-full"
         x-transition:enter-end="translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="translate-y-0"
         x-transition:leave-end="translate-y-full"
         class="bottom-sheet md:hidden overscroll-contain">
        <div class="bottom-sheet-handle"></div>
        <div class="p-4 space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2">
                    <button type="button" @click="showTopUp = false" class="p-2 -ml-1 text-slate-600 hover:text-slate-900 hover:bg-slate-100 rounded-xl transition-all active:scale-95 flex items-center justify-center shrink-0" title="Kembali">
                        <svg class="size-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/></svg>
                    </button>
                    <h3 class="font-bold text-slate-800 text-base">Top-Up Deposit Kg</h3>
                </div>
            </div>
            @if ($topUpCustomerId)
            @php $topUpCust = \App\Models\Customer::find($topUpCustomerId); @endphp
            @if ($topUpCust)
            <div class="flex items-center gap-3 rounded-xl bg-emerald-50 border border-emerald-100 p-3">
                <div class="size-9 rounded-full bg-emerald-100 flex items-center justify-center shrink-0">
                    <span class="font-bold text-emerald-700">{{ mb_strtoupper(mb_substr($topUpCust->name, 0, 1)) }}</span>
                </div>
                <div>
                    <p class="font-semibold text-slate-800 text-sm">{{ $topUpCust->name }}</p>
                    <p class="text-xs text-emerald-600">Saldo saat ini: <strong>{{ (float)$topUpCust->deposit_kg }} Kg</strong></p>
                </div>
            </div>
            @endif
            @endif
            <div class="space-y-3">
                <div>
                    <label class="form-label">Pilih Paket Deposit (Kg)</label>
                    <div class="grid grid-cols-4 gap-2">
                        @foreach ([5, 10, 20, 50] as $preset)
                        <button type="button"
                                wire:click="$set('topUpKg', {{ $preset }})"
                                @class(['py-2 rounded-xl text-xs font-bold border transition-all text-center', 'border-sky-500 bg-sky-50 text-sky-700 ring-2 ring-sky-500/20' => $topUpKg == $preset, 'border-slate-200 text-slate-600 hover:border-sky-200 bg-white' => $topUpKg != $preset])>
                            {{ $preset }} Kg
                        </button>
                        @endforeach
                    </div>
                    <div class="relative mt-2">
                        <input type="number" step="0.5" min="0.5" wire:model.live="topUpKg" class="form-input pr-8" placeholder="Jumlah Kg kustom">
                        <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-slate-400 font-bold">Kg</span>
                    </div>
                    @error('topUpKg') <p class="form-error">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="form-label">Catatan (opsional)</label>
                    <input type="text" wire:model="topUpNotes" class="form-input text-sm" placeholder="cth. Promo Lebaran 10Kg">
                </div>
                @if ($topUpCustomerId)
                @php $c = \App\Models\Customer::find($topUpCustomerId); @endphp
                @if ($c)
                <div class="rounded-xl bg-sky-50 border border-sky-100 px-3 py-2 text-xs text-sky-700 font-medium">
                    Saldo setelah top-up: <strong>{{ round((float)$c->deposit_kg + (float)$topUpKg, 2) }} Kg</strong>
                </div>
                @endif
                @endif
            </div>
            <div class="pt-2">
                <button wire:click="saveTopUp" class="btn-primary w-full py-2.5 font-bold text-sm">
                    Simpan &amp; Tambah Deposit
                </button>
            </div>
        </div>
    </div>

    {{-- Desktop: Centered Dialog --}}
    <div x-show="showTopUp"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         class="hidden md:flex fixed inset-0 z-50 items-center justify-center p-4 pointer-events-none">
        <div class="pointer-events-auto w-full max-w-md bg-white rounded-2xl shadow-2xl overflow-hidden p-5 space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <h3 class="font-bold text-slate-800 text-base">Top-Up Deposit Kg</h3>
                <button @click="showTopUp = false" class="btn-icon" title="Tutup">
                    <svg class="size-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
                </button>
            </div>
            @if ($topUpCustomerId)
            @php $topUpCust = \App\Models\Customer::find($topUpCustomerId); @endphp
            @if ($topUpCust)
            <div class="flex items-center gap-3 rounded-xl bg-emerald-50 border border-emerald-100 p-3">
                <div class="size-9 rounded-full bg-emerald-100 flex items-center justify-center shrink-0">
                    <span class="font-bold text-emerald-700">{{ mb_strtoupper(mb_substr($topUpCust->name, 0, 1)) }}</span>
                </div>
                <div>
                    <p class="font-semibold text-slate-800 text-sm">{{ $topUpCust->name }}</p>
                    <p class="text-xs text-emerald-600">Saldo saat ini: <strong>{{ (float)$topUpCust->deposit_kg }} Kg</strong></p>
                </div>
            </div>
            @endif
            @endif
            <div class="space-y-3">
                <div>
                    <label class="form-label">Pilih Paket Deposit (Kg)</label>
                    <div class="grid grid-cols-4 gap-2">
                        @foreach ([5, 10, 20, 50] as $preset)
                        <button type="button"
                                wire:click="$set('topUpKg', {{ $preset }})"
                                @class(['py-2 rounded-xl text-xs font-bold border transition-all text-center', 'border-sky-500 bg-sky-50 text-sky-700 ring-2 ring-sky-500/20' => $topUpKg == $preset, 'border-slate-200 text-slate-600 hover:border-sky-200 bg-white' => $topUpKg != $preset])>
                            {{ $preset }} Kg
                        </button>
                        @endforeach
                    </div>
                    <div class="relative mt-2">
                        <input type="number" step="0.5" min="0.5" wire:model.live="topUpKg" class="form-input pr-8" placeholder="Jumlah Kg kustom">
                        <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-slate-400 font-bold">Kg</span>
                    </div>
                    @error('topUpKg') <p class="form-error">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="form-label">Catatan (opsional)</label>
                    <input type="text" wire:model="topUpNotes" class="form-input text-sm" placeholder="cth. Promo Lebaran 10Kg">
                </div>
                @if ($topUpCustomerId)
                @php $c = \App\Models\Customer::find($topUpCustomerId); @endphp
                @if ($c)
                <div class="rounded-xl bg-sky-50 border border-sky-100 px-3 py-2 text-xs text-sky-700 font-medium">
                    Saldo setelah top-up: <strong>{{ round((float)$c->deposit_kg + (float)$topUpKg, 2) }} Kg</strong>
                </div>
                @endif
                @endif
            </div>
            <div class="flex gap-2 pt-2 border-t border-slate-100">
                <button wire:click="saveTopUp" class="btn-primary flex-1 font-semibold text-xs py-2.5">
                    Simpan Deposit
                </button>
                <button @click="showTopUp = false" class="btn-secondary text-xs px-4">
                    Batal
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Complaint Form --}}
<div x-show="showComplaint" x-cloak style="display:none">
    <div class="bottom-sheet-overlay md:hidden" @click="showComplaint = false"></div>
    <div class="dialog-overlay hidden md:flex" @click.self="showComplaint = false"></div>
    <div x-show="showComplaint" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="translate-y-full" x-transition:enter-end="translate-y-0" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="translate-y-0" x-transition:leave-end="translate-y-full" class="bottom-sheet md:hidden">
        <div class="bottom-sheet-handle"></div>
        @include('livewire.crm._complaint-form')
    </div>
    <div x-show="showComplaint" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" class="hidden md:flex fixed inset-0 z-50 items-center justify-center p-4 pointer-events-none">
        <div class="pointer-events-auto w-full max-w-lg bg-white rounded-2xl shadow-2xl overflow-hidden max-h-[90vh] overflow-y-auto">
            @include('livewire.crm._complaint-form')
        </div>
    </div>
</div>

{{-- Delete confirm --}}
<div x-show="showDelete" x-cloak style="display:none">
    <div class="dialog-overlay">
        <div x-show="showDelete" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" class="dialog-panel p-6">
            <p class="font-bold text-slate-800 mb-1">Hapus Pelanggan?</p>
            <p class="text-sm text-slate-500 mb-5">Data pelanggan akan di-soft-delete. Histori transaksi tetap aman.</p>
            <div class="flex gap-3">
                <button wire:click="deleteCustomer" class="btn-danger flex-1">Hapus</button>
                <button @click="showDelete = false" class="btn-secondary flex-1">Batal</button>
            </div>
        </div>
    </div>
</div>

{{-- Customer Detail Drawer (Bottom Sheet on Mobile, Centered Dialog on Desktop) --}}
<div x-show="showDetail" x-cloak style="display:none">
    <div class="bottom-sheet-overlay md:hidden" @click="showDetail = false"></div>
    <div class="dialog-overlay hidden md:flex" @click.self="showDetail = false"></div>

    {{-- Mobile: Bottom Sheet --}}
    <div x-show="showDetail"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="translate-y-full"
         x-transition:enter-end="translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="translate-y-0"
         x-transition:leave-end="translate-y-full"
         class="bottom-sheet md:hidden overscroll-contain">
        <div class="bottom-sheet-handle"></div>
        @if ($this->detailCustomer)
            @php $dc = $this->detailCustomer; @endphp
            <div class="px-5 pb-6 space-y-4">
                {{-- Header with Back button --}}
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div class="flex items-center gap-2">
                        <button type="button"
                                @click="showDetail = false"
                                class="p-2 -ml-1 text-slate-600 hover:text-slate-900 hover:bg-slate-100 rounded-xl transition-all active:scale-95 flex items-center justify-center shrink-0"
                                title="Kembali">
                            <svg class="size-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/></svg>
                        </button>
                        <h3 class="font-bold text-slate-800 text-base">Detail Pelanggan</h3>
                    </div>
                </div>

                {{-- Profile summary --}}
                <div class="flex items-center gap-3">
                    <div class="size-14 rounded-2xl bg-gradient-to-br from-sky-100 to-sky-200 flex items-center justify-center shrink-0 shadow-xs">
                        <span class="text-xl font-bold text-sky-700">{{ mb_strtoupper(mb_substr($dc->name, 0, 1)) }}</span>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-lg font-bold text-slate-800 truncate leading-snug">{{ $dc->name }}</p>
                        <div class="flex items-center gap-2 mt-0.5">
                            <span class="text-sm text-slate-500">{{ $dc->phone }}</span>
                            <a href="https://wa.me/62{{ ltrim($dc->phone,'0') }}"
                               target="_blank"
                               class="text-xs text-emerald-600 hover:text-emerald-700 font-semibold inline-flex items-center gap-0.5 bg-emerald-50 px-2 py-0.5 rounded-md border border-emerald-200/60">
                                <svg class="size-3" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
                                <span>WhatsApp</span>
                            </a>
                        </div>
                        @php $tc = match($dc->tier){ 'VIP' => 'badge-sky', 'SILVER' => 'badge-slate', default => 'badge-slate' }; @endphp
                        <span class="badge {{ $tc }} mt-1 text-xs">{{ $dc->tier }}</span>
                    </div>
                </div>

                {{-- Metric grid --}}
                <div class="grid grid-cols-3 gap-2.5">
                    <div class="card p-3 text-center border border-slate-200/80">
                        <p class="text-[11px] text-slate-400 font-semibold uppercase">Deposit</p>
                        <p class="font-bold text-emerald-600 text-base mt-0.5">{{ (float)$dc->deposit_kg }} Kg</p>
                    </div>
                    <div class="card p-3 text-center border border-slate-200/80">
                        <p class="text-[11px] text-slate-400 font-semibold uppercase">Poin</p>
                        <p class="font-bold text-sky-600 text-base mt-0.5">{{ number_format($dc->loyalty_points) }}</p>
                    </div>
                    <div class="card p-3 text-center border border-slate-200/80">
                        <p class="text-[11px] text-slate-400 font-semibold uppercase">Total Order</p>
                        <p class="font-bold text-slate-700 text-base mt-0.5">{{ $dc->orders->count() }}</p>
                    </div>
                </div>

                @if ($dc->preferences)
                <div class="rounded-xl bg-amber-50/70 border border-amber-200/60 p-3">
                    <p class="text-xs font-bold text-amber-800 mb-0.5">⭐ Preferensi Cucian</p>
                    <p class="text-xs text-amber-900 leading-relaxed">{{ $dc->preferences }}</p>
                </div>
                @endif

                @if ($dc->address)
                <div class="rounded-xl bg-slate-50 border border-slate-200/80 p-3">
                    <div class="flex items-center justify-between mb-1">
                        <p class="text-xs font-bold text-slate-600">📍 Alamat Jemput</p>
                        <a href="{{ $dc->maps_url ?: ('https://www.google.com/maps/search/?api=1&query=' . urlencode($dc->address)) }}"
                           target="_blank"
                           class="text-xs text-sky-600 hover:text-sky-700 font-semibold inline-flex items-center gap-1 hover:underline">
                            <span>Google Maps ➔</span>
                        </a>
                    </div>
                    <p class="text-xs text-slate-700 leading-relaxed">{{ $dc->address }}</p>
                </div>
                @endif

                <div>
                    <p class="text-xs font-bold text-slate-500 uppercase tracking-wide mb-2">Riwayat Order Terbaru</p>
                    <div class="divide-y divide-slate-100 max-h-48 overflow-y-auto">
                        @forelse ($dc->orders->take(5) as $o)
                        <div class="flex items-center justify-between py-2 text-xs">
                            <div>
                                <p class="font-mono font-semibold text-slate-700">{{ $o->invoice_number }}</p>
                                <p class="text-slate-400 mt-0.5">{{ $o->created_at->format('d M Y, H:i') }}</p>
                            </div>
                            <div class="text-right">
                                <p class="font-bold text-slate-800">Rp {{ number_format($o->total_price) }}</p>
                                @php $sc = match($o->status){ 'SELESAI' => 'badge-green', 'VOID' => 'badge-rose', default => 'badge-amber' }; @endphp
                                <span class="badge {{ $sc }} text-[10px] mt-0.5">{{ $o->status }}</span>
                            </div>
                        </div>
                        @empty
                        <p class="text-xs text-slate-400 py-3 text-center">Belum ada riwayat transaksi.</p>
                        @endforelse
                    </div>
                </div>

                {{-- Action buttons --}}
                <div class="flex gap-2 pt-3 border-t border-slate-100">
                    <button wire:click="openTopUp({{ $dc->id }})" class="btn-primary flex-1 text-xs py-2.5 font-semibold">
                        + Top-Up Deposit
                    </button>
                    <button wire:click="openCreateComplaint({{ $dc->id }})" class="btn-secondary flex-1 text-xs py-2.5 font-semibold">
                        💬 Tiket Komplain
                    </button>
                </div>
            </div>
        @endif
    </div>

    {{-- Desktop: Centered Dialog Modal --}}
    <div x-show="showDetail"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         class="hidden md:flex fixed inset-0 z-50 items-center justify-center p-4 pointer-events-none">
        <div class="pointer-events-auto w-full max-w-lg bg-white rounded-2xl shadow-2xl overflow-hidden max-h-[90vh] overflow-y-auto p-5 space-y-4">
            @if ($this->detailCustomer)
                @php $dc = $this->detailCustomer; @endphp
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <h3 class="font-bold text-slate-800 text-base sm:text-lg">Detail Pelanggan</h3>
                    <button @click="showDetail = false" class="btn-icon" title="Tutup">
                        <svg class="size-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <div class="flex items-center gap-3">
                    <div class="size-14 rounded-2xl bg-gradient-to-br from-sky-100 to-sky-200 flex items-center justify-center shrink-0 shadow-xs">
                        <span class="text-xl font-bold text-sky-700">{{ mb_strtoupper(mb_substr($dc->name, 0, 1)) }}</span>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-lg font-bold text-slate-800 truncate leading-snug">{{ $dc->name }}</p>
                        <div class="flex items-center gap-2 mt-0.5">
                            <span class="text-sm text-slate-500">{{ $dc->phone }}</span>
                            <a href="https://wa.me/62{{ ltrim($dc->phone,'0') }}"
                               target="_blank"
                               class="text-xs text-emerald-600 hover:text-emerald-700 font-semibold inline-flex items-center gap-0.5 bg-emerald-50 px-2 py-0.5 rounded-md border border-emerald-200/60">
                                <span>WhatsApp ↗</span>
                            </a>
                        </div>
                        @php $tc = match($dc->tier){ 'VIP' => 'badge-sky', 'SILVER' => 'badge-slate', default => 'badge-slate' }; @endphp
                        <span class="badge {{ $tc }} mt-1 text-xs">{{ $dc->tier }}</span>
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-2.5">
                    <div class="card p-3 text-center border border-slate-200/80">
                        <p class="text-[11px] text-slate-400 font-semibold uppercase">Deposit</p>
                        <p class="font-bold text-emerald-600 text-base mt-0.5">{{ (float)$dc->deposit_kg }} Kg</p>
                    </div>
                    <div class="card p-3 text-center border border-slate-200/80">
                        <p class="text-[11px] text-slate-400 font-semibold uppercase">Poin</p>
                        <p class="font-bold text-sky-600 text-base mt-0.5">{{ number_format($dc->loyalty_points) }}</p>
                    </div>
                    <div class="card p-3 text-center border border-slate-200/80">
                        <p class="text-[11px] text-slate-400 font-semibold uppercase">Total Order</p>
                        <p class="font-bold text-slate-700 text-base mt-0.5">{{ $dc->orders->count() }}</p>
                    </div>
                </div>

                @if ($dc->preferences)
                <div class="rounded-xl bg-amber-50/70 border border-amber-200/60 p-3">
                    <p class="text-xs font-bold text-amber-800 mb-0.5">⭐ Preferensi Cucian</p>
                    <p class="text-xs text-amber-900 leading-relaxed">{{ $dc->preferences }}</p>
                </div>
                @endif

                @if ($dc->address)
                <div class="rounded-xl bg-slate-50 border border-slate-200/80 p-3">
                    <div class="flex items-center justify-between mb-1">
                        <p class="text-xs font-bold text-slate-600">📍 Alamat Jemput</p>
                        <a href="{{ $dc->maps_url ?: ('https://www.google.com/maps/search/?api=1&query=' . urlencode($dc->address)) }}"
                           target="_blank"
                           class="text-xs text-sky-600 hover:text-sky-700 font-semibold inline-flex items-center gap-1 hover:underline">
                            <span>Google Maps ➔</span>
                        </a>
                    </div>
                    <p class="text-xs text-slate-700 leading-relaxed">{{ $dc->address }}</p>
                </div>
                @endif

                <div>
                    <p class="text-xs font-bold text-slate-500 uppercase tracking-wide mb-2">Riwayat Order Terbaru</p>
                    <div class="divide-y divide-slate-100 max-h-48 overflow-y-auto">
                        @forelse ($dc->orders->take(5) as $o)
                        <div class="flex items-center justify-between py-2 text-xs">
                            <div>
                                <p class="font-mono font-semibold text-slate-700">{{ $o->invoice_number }}</p>
                                <p class="text-slate-400 mt-0.5">{{ $o->created_at->format('d M Y, H:i') }}</p>
                            </div>
                            <div class="text-right">
                                <p class="font-bold text-slate-800">Rp {{ number_format($o->total_price) }}</p>
                                @php $sc = match($o->status){ 'SELESAI' => 'badge-green', 'VOID' => 'badge-rose', default => 'badge-amber' }; @endphp
                                <span class="badge {{ $sc }} text-[10px] mt-0.5">{{ $o->status }}</span>
                            </div>
                        </div>
                        @empty
                        <p class="text-xs text-slate-400 py-3 text-center">Belum ada riwayat transaksi.</p>
                        @endforelse
                    </div>
                </div>

                <div class="flex gap-2 pt-3 border-t border-slate-100">
                    <button wire:click="openTopUp({{ $dc->id }})" class="btn-primary flex-1 text-xs py-2.5 font-semibold">
                        + Top-Up Deposit
                    </button>
                    <button wire:click="openCreateComplaint({{ $dc->id }})" class="btn-secondary flex-1 text-xs py-2.5 font-semibold">
                        💬 Tiket Komplain
                    </button>
                </div>
            @endif
        </div>
    </div>
</div>

{{-- ══ TIER INSPECT MODAL (POPUP DESKTOP & BOTTOM-SHEET MOBILE) ══ --}}
<div x-show="showTierModal" x-cloak style="display:none">
    {{-- Mobile Overlay --}}
    <div class="bottom-sheet-overlay md:hidden"
         @click="showTierModal = false; $wire.closeTierInspect()"
         @touchmove.prevent></div>

    {{-- Desktop Overlay --}}
    <div class="dialog-overlay hidden md:flex"
         @click.self="showTierModal = false; $wire.closeTierInspect()"></div>

    {{-- Mobile: Slide-Up Bottom Sheet --}}
    <div x-show="showTierModal"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="translate-y-full"
         x-transition:enter-end="translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="translate-y-0"
         x-transition:leave-end="translate-y-full"
         class="bottom-sheet md:hidden overscroll-contain"
         @touchmove.stop>
        <div class="bottom-sheet-handle"></div>
        @include('livewire.crm._tier-inspect-modal')
    </div>

    {{-- Desktop: Centered Dialog Popup Modal --}}
    <div x-show="showTierModal"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="hidden md:flex fixed inset-0 z-50 items-center justify-center p-4 pointer-events-none">
        <div class="pointer-events-auto w-full max-w-4xl bg-white rounded-2xl shadow-2xl overflow-hidden">
            @include('livewire.crm._tier-inspect-modal')
        </div>
    </div>
</div>

</div>
