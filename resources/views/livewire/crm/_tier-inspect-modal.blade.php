{{-- Modal / Bottom-Sheet Partial: Inspect Customer Tier --}}
<div class="flex flex-col max-h-[85vh] sm:max-h-[80vh] w-full">

    {{-- ══ MODAL HEADER ══════════════════════════════════════════════════ --}}
    <div class="flex items-center justify-between p-3.5 sm:p-5 border-b border-slate-100 shrink-0">
        <div class="flex items-center gap-2 sm:gap-3">
            {{-- Tombol Back khusus Mobile (di Desktop disembunyikan md:hidden) --}}
            <button type="button"
                    wire:click="closeTierInspect"
                    class="p-2 -ml-1 text-slate-600 hover:text-slate-900 hover:bg-slate-100 rounded-xl transition-all active:scale-95 flex items-center justify-center shrink-0 md:hidden"
                    title="Kembali">
                <svg class="size-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/>
                </svg>
            </button>

            <span class="text-2xl sm:text-3xl shrink-0">
                {{ match($inspectTier) {
                    'VIP'     => '👑',
                    'SILVER'  => '🥈',
                    default   => '💼'
                } }}
            </span>
            <div>
                <div class="flex items-center gap-2">
                    <h3 class="font-bold text-slate-800 text-base sm:text-lg">Tier {{ $inspectTier }}</h3>
                    <span class="badge badge-sky text-xs font-semibold">{{ $this->inspectedCustomers->count() }} Pelanggan</span>
                </div>
                <p class="hidden sm:block text-xs text-slate-400 mt-0.5">Kelola data pelanggan, saldo deposit, poin loyalitas, dan status pesanan</p>
            </div>
        </div>

        {{-- Desktop-only close X --}}
        <button wire:click="closeTierInspect" class="btn-icon hidden md:flex" title="Tutup">
            <svg class="size-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
        </button>
    </div>

    {{-- ══ TOOLBAR & QUICK ACTIONS ═══════════════════════════════════════ --}}
    <div class="px-3.5 sm:px-5 py-2.5 bg-slate-50/80 border-b border-slate-100 flex items-center justify-between gap-2 shrink-0">
        {{-- Stats pill --}}
        <div class="flex items-center gap-1.5 text-xs">
            @php
                $activeCount = $this->inspectedCustomers->filter(fn($c) => !$c->is_churn && !$c->trashed())->count();
                $churnCount = $this->inspectedCustomers->filter(fn($c) => $c->is_churn)->count();
            @endphp
            <span class="badge badge-green text-xs font-medium">{{ $activeCount }} Aktif</span>
            @if ($churnCount > 0)
                <span class="badge badge-rose text-xs font-medium">{{ $churnCount }} Pasif</span>
            @endif
        </div>

        {{-- Action button --}}
        <button wire:click="createForTier('{{ $inspectTier }}')" class="btn-primary text-xs py-1.5 px-3">
            <svg class="size-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
            <span>Tambah Pelanggan</span>
        </button>
    </div>

    {{-- ══ CONTENT BODY (TABLE FOR DESKTOP, CARDS FOR MOBILE) ════════════ --}}
    <div class="overflow-y-auto p-3.5 sm:p-5 pb-8 sm:pb-6 flex-1 overscroll-contain scrollbar-thin">
        @if ($this->inspectedCustomers->isEmpty())
            <div class="card p-10 text-center text-slate-400">
                <p class="text-3xl mb-2">👥</p>
                <p class="font-bold text-slate-700">Belum ada pelanggan di Tier {{ $inspectTier }}</p>
                <p class="text-xs text-slate-400 mt-1 mb-4">Tambahkan pelanggan baru untuk kategori tier ini.</p>
                <button wire:click="createForTier('{{ $inspectTier }}')" class="btn-primary text-xs mx-auto">
                    + Tambah Pelanggan Pertama
                </button>
            </div>
        @else
            {{-- Desktop Table View --}}
            <div class="hidden md:block overflow-hidden rounded-xl border border-slate-200/80">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50/80 border-b border-slate-200/80 text-xs font-semibold text-slate-400 uppercase tracking-wide">
                        <tr>
                            <th class="text-left px-4 py-3">Pelanggan</th>
                            <th class="text-right px-4 py-3">Deposit Kg</th>
                            <th class="text-center px-4 py-3">Terakhir Order</th>
                            <th class="text-center px-4 py-3">Poin</th>
                            <th class="text-right px-4 py-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @foreach ($this->inspectedCustomers as $c)
                            <tr @class(['hover:bg-slate-50/60 transition-colors', 'opacity-50' => $c->trashed()])>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-3">
                                        <div class="size-9 rounded-full bg-gradient-to-br from-sky-100 to-sky-200 flex items-center justify-center shrink-0">
                                            <span class="text-sm font-bold text-sky-700">{{ mb_strtoupper(mb_substr($c->name, 0, 1)) }}</span>
                                        </div>
                                        <div>
                                            <p class="font-semibold text-slate-800">{{ $c->name }}</p>
                                            <div class="flex items-center gap-2 text-xs text-slate-400">
                                                <span>{{ $c->phone }}</span>
                                                <a href="https://wa.me/62{{ ltrim($c->phone,'0') }}" target="_blank" class="text-emerald-600 hover:text-emerald-700 font-semibold text-[11px]">WA ↗</a>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    @if ($c->deposit_kg > 0)
                                        <span class="font-bold text-emerald-600 tabular-nums">{{ (float)$c->deposit_kg }} Kg</span>
                                    @else
                                        <span class="text-slate-300">—</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center text-xs">
                                    @if ($c->last_order_date)
                                        @php $days = $c->days_since_last_order ?? 0; @endphp
                                        <span @class(['badge text-[10px]', 'badge-green' => $days <= 14, 'badge-amber' => $days > 14 && $days <= 30, 'badge-rose' => $days > 30])>
                                            {{ $c->last_order_human }}
                                        </span>
                                    @else
                                        <span class="badge badge-slate text-[10px]">Baru</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center text-sm font-semibold text-slate-600 tabular-nums">
                                    {{ number_format($c->loyalty_points) }}
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        <button wire:click="openDetail({{ $c->id }})" class="btn-icon" title="Detail">
                                            <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/></svg>
                                        </button>
                                        <button wire:click="openTopUp({{ $c->id }})" class="btn-icon text-emerald-600" title="Top-Up Deposit">
                                            <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                                        </button>
                                        <button wire:click="openEdit({{ $c->id }})" class="btn-icon" title="Edit">
                                            <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125"/></svg>
                                        </button>
                                        <button wire:click="confirmDelete({{ $c->id }})" class="btn-icon text-rose-400" title="Hapus">
                                            <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Mobile Cards View (Clean & Minimalist Native Feel) --}}
            <div class="md:hidden space-y-2.5">
                @foreach ($this->inspectedCustomers as $c)
                    <div @class([
                        'bg-white rounded-xl p-3.5 border border-slate-200/90 shadow-xs space-y-2.5 transition-all',
                        'opacity-60 bg-slate-50/50' => $c->trashed(),
                    ])>
                        <div class="flex items-start justify-between gap-2.5">
                            <div class="flex items-center gap-2.5 min-w-0 flex-1">
                                <div class="size-10 rounded-full bg-gradient-to-br from-sky-100 to-sky-200 flex items-center justify-center shrink-0">
                                    <span class="font-bold text-sky-700 text-sm">{{ mb_strtoupper(mb_substr($c->name, 0, 1)) }}</span>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <h4 class="font-bold text-slate-800 text-sm truncate leading-snug">{{ $c->name }}</h4>
                                    <div class="flex items-center gap-2 text-xs text-slate-400 mt-0.5">
                                        <span>{{ $c->phone }}</span>
                                        <a href="https://wa.me/62{{ ltrim($c->phone,'0') }}"
                                           target="_blank"
                                           class="text-emerald-600 hover:text-emerald-700 font-medium inline-flex items-center gap-0.5">
                                            <span>WA</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
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
                        </div>

                        <div class="flex items-center justify-end gap-1.5 pt-1.5 border-t border-dashed border-slate-100">
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
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

</div>
