{{-- Modal / Bottom-Sheet Partial: Inspect Category & Service Packages --}}
<div class="flex flex-col max-h-[85vh] sm:max-h-[80vh] w-full">

    {{-- ══ MODAL HEADER ══════════════════════════════════════════════════ --}}
    <div class="flex items-center justify-between p-4 sm:p-5 border-b border-slate-100 shrink-0">
        <div class="flex items-center gap-3">
            <span class="text-2xl sm:text-3xl">
                {{ match($inspectCategory) {
                    'KILOAN'  => '🧺',
                    'SATUAN'  => '👔',
                    'EXPRESS' => '⚡',
                    default   => '🧴'
                } }}
            </span>
            <div>
                <div class="flex items-center gap-2">
                    <h3 class="font-bold text-slate-800 text-base sm:text-lg">Kategori {{ $inspectCategory }}</h3>
                    <span class="badge badge-sky text-xs font-semibold">{{ $this->inspectedPackages->count() }} Paket</span>
                </div>
                <p class="text-xs text-slate-400 mt-0.5">Kelola paket layanan, harga satuan, takaran sabun BOM, &amp; auto-approve POS</p>
            </div>
        </div>
        <button wire:click="closeCategoryInspect" class="btn-icon">
            <svg class="size-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
        </button>
    </div>

    {{-- ══ TOOLBAR & QUICK ACTIONS ═══════════════════════════════════════ --}}
    <div class="px-4 sm:px-5 py-3 bg-slate-50/80 border-b border-slate-100 flex flex-wrap items-center justify-between gap-2.5 shrink-0">
        {{-- Stats pill --}}
        <div class="flex items-center gap-2 text-xs">
            @php
                $activeCount = $this->inspectedPackages->where('is_active', true)->whereNull('deleted_at')->count();
                $inactiveCount = $this->inspectedPackages->count() - $activeCount;
            @endphp
            <span class="badge badge-green text-xs font-medium">{{ $activeCount }} Aktif di POS</span>
            @if ($inactiveCount > 0)
                <span class="badge badge-slate text-xs font-medium">{{ $inactiveCount }} Nonaktif</span>
            @endif
        </div>

        {{-- Action buttons --}}
        <div class="flex items-center gap-2">
            @if ($inactiveCount > 0)
                <button wire:click="autoApproveCategory('{{ $inspectCategory }}')"
                        title="Otomatis aktifkan semua paket nonaktif di kategori ini"
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

    {{-- ══ CONTENT BODY (TABLE FOR DESKTOP, CARDS FOR MOBILE) ════════════ --}}
    <div class="overflow-y-auto p-4 sm:p-5 flex-1 overscroll-contain scrollbar-thin">
        @if ($this->inspectedPackages->isEmpty())
            <div class="card p-10 text-center text-slate-400">
                <p class="text-3xl mb-2">🧴</p>
                <p class="font-bold text-slate-700">Belum ada paket di kategori {{ $inspectCategory }}</p>
                <p class="text-xs text-slate-400 mt-1 mb-4">Tambahkan paket baru untuk kategori ini agar kasir dapat memilihnya di POS.</p>
                <button wire:click="createForCategory('{{ $inspectCategory }}')" class="btn-primary text-xs mx-auto">
                    + Tambah Paket Pertama
                </button>
            </div>
        @else
            {{-- Desktop Table View (Mirrors the structure from screenshot) --}}
            <div class="hidden md:block overflow-hidden rounded-xl border border-slate-200/80">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50/80 border-b border-slate-200/80 text-xs font-semibold text-slate-400 uppercase tracking-wide">
                        <tr>
                            <th class="text-left px-4 py-3">Layanan</th>
                            <th class="text-right px-4 py-3">Harga / Unit</th>
                            <th class="text-center px-4 py-3">Estimasi</th>
                            <th class="text-center px-4 py-3">BOM (ml/unit)</th>
                            <th class="text-center px-4 py-3">Status</th>
                            <th class="text-right px-4 py-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @foreach ($this->inspectedPackages as $pkg)
                            <tr @class(['hover:bg-slate-50/60 transition-colors', 'opacity-50' => $pkg->trashed()])>
                                <td class="px-4 py-3">
                                    <p class="font-semibold text-slate-800">{{ $pkg->name }}</p>
                                    <p class="text-xs text-slate-400 font-mono">{{ $pkg->unit }}</p>
                                </td>
                                <td class="px-4 py-3 text-right font-semibold text-slate-800 tabular-nums">
                                    Rp {{ number_format($pkg->price_per_unit) }}
                                    <span class="font-normal text-slate-400 text-xs">/ {{ $pkg->unit }}</span>
                                </td>
                                <td class="px-4 py-3 text-center text-sm text-slate-600">
                                    {{ $pkg->estimated_hours }} jam
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-center gap-1.5 text-xs">
                                        <span class="badge badge-sky" title="Deterjen">D {{ $pkg->detergent_ml_per_unit }}ml</span>
                                        <span class="badge badge-slate" title="Softener">S {{ $pkg->softener_ml_per_unit }}ml</span>
                                        <span class="badge" style="background:#f5f3ff;color:#5b21b6;box-shadow:0 0 0 1px #ddd6fe" title="Parfum">P {{ $pkg->perfume_ml_per_unit }}ml</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @if ($pkg->trashed())
                                        <span class="badge badge-rose text-xs">Dihapus</span>
                                    @elseif ($pkg->is_active)
                                        <span class="badge badge-green text-xs">✓ Aktif</span>
                                    @else
                                        <span class="badge badge-slate text-xs">Nonaktif</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-end gap-1">
                                        @if ($pkg->trashed())
                                            <button wire:click="restorePackage({{ $pkg->id }})" class="btn-secondary text-xs py-1 px-2.5">
                                                Pulihkan
                                            </button>
                                        @else
                                            {{-- Toggle active (auto-approve) --}}
                                            <button wire:click="toggleActive({{ $pkg->id }})"
                                                    class="btn-icon"
                                                    title="{{ $pkg->is_active ? 'Nonaktifkan dari POS' : 'Aktifkan (Auto-Approve)' }}">
                                                @if ($pkg->is_active)
                                                    <svg class="size-4 text-amber-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88"/></svg>
                                                @else
                                                    <svg class="size-4 text-emerald-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/></svg>
                                                @endif
                                            </button>

                                            {{-- Edit --}}
                                            <button wire:click="openEdit({{ $pkg->id }})"
                                                    class="btn-icon"
                                                    title="Edit paket">
                                                <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125"/></svg>
                                            </button>

                                            {{-- Delete --}}
                                            <button wire:click="confirmDelete({{ $pkg->id }})"
                                                    class="btn-icon"
                                                    title="Hapus paket">
                                                <svg class="size-4 text-rose-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/></svg>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Mobile Cards View --}}
            <div class="md:hidden space-y-3">
                @foreach ($this->inspectedPackages as $pkg)
                    <div @class([
                        'card p-4 space-y-2.5',
                        'opacity-60' => $pkg->trashed(),
                    ])>
                        <div class="flex items-start justify-between gap-2">
                            <div>
                                <h4 class="font-bold text-slate-800 text-sm">{{ $pkg->name }}</h4>
                                <p class="text-xs text-slate-400 mt-0.5">{{ $pkg->estimated_hours }} jam • {{ $pkg->unit }}</p>
                            </div>
                            @if ($pkg->trashed())
                                <span class="badge badge-rose text-xs">Dihapus</span>
                            @elseif ($pkg->is_active)
                                <span class="badge badge-green text-xs">✓ Aktif</span>
                            @else
                                <span class="badge badge-slate text-xs">Nonaktif</span>
                            @endif
                        </div>

                        <div class="flex items-center justify-between">
                            <p class="text-base font-bold text-sky-700 tabular-nums">
                                Rp {{ number_format($pkg->price_per_unit) }}
                                <span class="text-xs font-normal text-slate-400">/ {{ $pkg->unit }}</span>
                            </p>
                            <div class="flex gap-1 text-[11px]">
                                <span class="badge badge-sky">D {{ $pkg->detergent_ml_per_unit }}ml</span>
                                <span class="badge badge-slate">S {{ $pkg->softener_ml_per_unit }}ml</span>
                                <span class="badge" style="background:#f5f3ff;color:#5b21b6;box-shadow:0 0 0 1px #ddd6fe">P {{ $pkg->perfume_ml_per_unit }}ml</span>
                            </div>
                        </div>

                        <div class="flex gap-2 pt-2 border-t border-slate-100">
                            @if ($pkg->trashed())
                                <button wire:click="restorePackage({{ $pkg->id }})" class="btn-secondary text-xs py-1.5 flex-1">Pulihkan</button>
                            @else
                                <button wire:click="toggleActive({{ $pkg->id }})" class="btn-secondary text-xs py-1.5 flex-1">
                                    {{ $pkg->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                </button>
                                <button wire:click="openEdit({{ $pkg->id }})" class="btn-secondary text-xs py-1.5 flex-1">Edit</button>
                                <button wire:click="confirmDelete({{ $pkg->id }})" class="btn-danger text-xs py-1.5 px-3">Hapus</button>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- ══ MODAL FOOTER ══════════════════════════════════════════════════ --}}
    <div class="p-3 sm:px-5 bg-slate-50/60 border-t border-slate-100 flex items-center justify-between text-xs text-slate-400 shrink-0">
        <span>💡 Paket dengan status <strong class="text-emerald-700 font-semibold">Aktif</strong> otomatis muncul di Kasir POS.</span>
        <button wire:click="closeCategoryInspect" class="btn-secondary text-xs py-1.5 px-3">
            Tutup
        </button>
    </div>

</div>
