{{-- Modal / Bottom-Sheet Partial: Inspect Category & Service Packages --}}
<div class="flex flex-col max-h-[85vh] sm:max-h-[80vh]">

    {{-- ══ MODAL HEADER ══════════════════════════════════════════════════ --}}
    <div class="flex items-center justify-between p-4 sm:p-5 border-b border-slate-100 shrink-0">
        <div class="flex items-center gap-2.5">
            <span class="text-2xl">
                {{ match($inspectCategory) {
                    'KILOAN' => '🧺',
                    'SATUAN' => '👔',
                    'EXPRESS' => '⚡',
                    default => '🧴'
                } }}
            </span>
            <div>
                <div class="flex items-center gap-2">
                    <h3 class="font-bold text-slate-800 text-base sm:text-lg">Kategori {{ $inspectCategory }}</h3>
                    <span class="badge badge-sky text-xs font-semibold">{{ $this->inspectedPackages->count() }} Paket</span>
                </div>
                <p class="text-xs text-slate-400 mt-0.5">Inspeksi paket harga, takaran sabun BOM, &amp; auto-approve POS</p>
            </div>
        </div>
        <button wire:click="closeCategoryInspect" class="btn-icon">
            <svg class="size-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
        </button>
    </div>

    {{-- ══ TOOLBAR & QUICK ACTIONS ═══════════════════════════════════════ --}}
    <div class="p-4 sm:px-5 py-3 bg-slate-50/70 border-b border-slate-100 flex flex-wrap items-center justify-between gap-2 shrink-0">
        {{-- Stats pill --}}
        <div class="flex items-center gap-2 text-xs">
            @php
                $activeCount = $this->inspectedPackages->where('is_active', true)->whereNull('deleted_at')->count();
                $inactiveCount = $this->inspectedPackages->count() - $activeCount;
            @endphp
            <span class="badge badge-green text-xs">{{ $activeCount }} Aktif di POS</span>
            @if ($inactiveCount > 0)
                <span class="badge badge-slate text-xs">{{ $inactiveCount }} Nonaktif</span>
            @endif
        </div>

        {{-- Action buttons --}}
        <div class="flex items-center gap-2">
            @if ($inactiveCount > 0)
                <button wire:click="autoApproveCategory('{{ $inspectCategory }}')"
                        title="Otomatis aktifkan semua paket di kategori ini"
                        class="btn-secondary text-xs py-1.5 px-3 hover:text-sky-700 hover:border-sky-300">
                    <span>⚡ Auto-Approve Semua</span>
                </button>
            @endif
            <button wire:click="createForCategory('{{ $inspectCategory }}')"
                    class="btn-primary text-xs py-1.5 px-3">
                <svg class="size-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                <span>Tambah Paket</span>
            </button>
        </div>
    </div>

    {{-- ══ PACKAGE INSPECTION LIST ════════════════════════════════════════ --}}
    <div class="overflow-y-auto p-4 sm:p-5 space-y-3 flex-1 scrollbar-thin">
        @forelse ($this->inspectedPackages as $pkg)
            <div @class([
                'p-3.5 sm:p-4 rounded-xl border transition-all',
                'bg-white border-slate-200/80 hover:border-slate-300 shadow-xs' => ! $pkg->trashed(),
                'bg-slate-50 border-slate-200 opacity-60' => $pkg->trashed(),
            ])>
                <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-3">
                    {{-- Left side info --}}
                    <div class="space-y-1.5 flex-1 min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <h4 class="font-bold text-slate-900 text-sm sm:text-base">{{ $pkg->name }}</h4>
                            <span class="badge badge-slate text-[11px] font-mono">{{ $pkg->unit }}</span>

                            {{-- Status badge --}}
                            @if ($pkg->trashed())
                                <span class="badge badge-rose text-[11px]">Dihapus</span>
                            @elseif ($pkg->is_active)
                                <span class="badge badge-green text-[11px]">✓ Aktif (POS Ready)</span>
                            @else
                                <span class="badge badge-slate text-[11px]">Nonaktif</span>
                            @endif
                        </div>

                        {{-- Price and duration --}}
                        <div class="flex items-center gap-3 text-xs sm:text-sm">
                            <span class="font-bold text-sky-700 text-base">
                                Rp {{ number_format($pkg->price_per_unit) }}
                                <span class="text-xs font-normal text-slate-400">/ {{ $pkg->unit }}</span>
                            </span>
                            <span class="text-slate-300">•</span>
                            <span class="text-slate-500 font-medium flex items-center gap-1">
                                <svg class="size-3.5 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                                {{ $pkg->estimated_hours }} jam ({{ round($pkg->estimated_hours / 24, 1) }} hari)
                            </span>
                        </div>

                        {{-- BOM Recipe pill --}}
                        <div class="flex items-center gap-2 pt-1 text-xs flex-wrap">
                            <span class="text-slate-400 font-medium">BOM:</span>
                            <span class="badge badge-sky text-[11px]">D: {{ $pkg->detergent_ml_per_unit }} ml</span>
                            <span class="badge badge-slate text-[11px]">S: {{ $pkg->softener_ml_per_unit }} ml</span>
                            <span class="badge text-[11px]" style="background:#f5f3ff;color:#5b21b6;box-shadow:0 0 0 1px #ddd6fe">P: {{ $pkg->perfume_ml_per_unit }} ml</span>
                        </div>
                    </div>

                    {{-- Right side actions --}}
                    <div class="flex items-center gap-2 shrink-0 pt-2 sm:pt-0 border-t sm:border-t-0 border-slate-100 justify-end">
                        @if ($pkg->trashed())
                            <button wire:click="restorePackage({{ $pkg->id }})" class="btn-secondary text-xs py-1.5 px-3">
                                Pulihkan
                            </button>
                        @else
                            {{-- Toggle Active / Auto-Approve switch --}}
                            <button wire:click="toggleActive({{ $pkg->id }})"
                                    title="{{ $pkg->is_active ? 'Nonaktifkan dari POS' : 'Aktifkan (Auto-Approve ke POS)' }}"
                                    class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg text-xs font-semibold border transition-all {{ $pkg->is_active ? 'bg-emerald-50 text-emerald-700 border-emerald-200 hover:bg-emerald-100' : 'bg-slate-100 text-slate-600 border-slate-200 hover:bg-slate-200' }}">
                                <span class="size-2 rounded-full {{ $pkg->is_active ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
                                <span>{{ $pkg->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                            </button>

                            {{-- Edit --}}
                            <button wire:click="openEdit({{ $pkg->id }})"
                                    title="Edit paket layanan"
                                    class="p-1.5 rounded-lg border border-slate-200 text-slate-500 hover:text-slate-900 hover:bg-slate-50 transition-colors">
                                <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125"/></svg>
                            </button>

                            {{-- Delete --}}
                            <button wire:click="confirmDelete({{ $pkg->id }})"
                                    title="Hapus paket"
                                    class="p-1.5 rounded-lg border border-slate-200 text-slate-300 hover:text-rose-600 hover:bg-rose-50 transition-colors">
                                <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/></svg>
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="card p-8 text-center text-slate-400">
                <p class="text-3xl mb-2">🧴</p>
                <p class="font-bold text-slate-700">Belum ada paket di kategori {{ $inspectCategory }}</p>
                <p class="text-xs text-slate-400 mt-1 mb-4">Tambahkan paket baru untuk kategori ini agar kasir dapat memilihnya di POS.</p>
                <button wire:click="createForCategory('{{ $inspectCategory }}')" class="btn-primary text-xs mx-auto">
                    + Tambah Paket Sekarang
                </button>
            </div>
        @endforelse
    </div>

    {{-- ══ MODAL FOOTER ══════════════════════════════════════════════════ --}}
    <div class="p-3 sm:px-5 bg-slate-50/50 border-t border-slate-100 flex items-center justify-between text-xs text-slate-400 shrink-0">
        <span>💡 Semua paket dengan status <strong class="text-emerald-700 font-semibold">Aktif</strong> otomatis muncul di Kasir POS.</span>
        <button wire:click="closeCategoryInspect" class="btn-secondary text-xs py-1.5 px-3">
            Tutup
        </button>
    </div>

</div>
