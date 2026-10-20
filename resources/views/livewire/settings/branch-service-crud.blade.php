<div x-data="{ showForm: @entangle('showForm'), confirmVoid: @entangle('confirmVoid') }" class="space-y-5">

    {{-- ══ TOP HEADER & ACTIONS ═══════════════════════════════════════════ --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h2 class="text-xl sm:text-2xl font-bold text-slate-800">Master Layanan &amp; Resep BOM</h2>
            <p class="text-xs sm:text-sm text-slate-400 mt-0.5">Kelola paket layanan laundry dan takaran otomatis sabun per kategori</p>
        </div>
        <div class="flex items-center gap-2">
            <button wire:click="openCreate" class="btn-primary text-xs sm:text-sm whitespace-nowrap">
                <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                <span>Tambah Layanan</span>
            </button>
        </div>
    </div>

    {{-- ══ FILTERS & CATEGORY TABS ════════════════════════════════════════ --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        {{-- Category Tabs --}}
        <div class="flex items-center gap-1 bg-slate-100 p-1 rounded-xl w-fit max-w-full overflow-x-auto scrollbar-none">
            <button wire:click="$set('filterCat', '')"
                    @class([
                        'px-3.5 py-1.5 rounded-lg text-xs sm:text-sm font-medium whitespace-nowrap transition-all flex items-center gap-1.5',
                        'bg-white text-slate-900 font-semibold shadow-xs' => $filterCat === '',
                        'text-slate-600 hover:text-slate-900' => $filterCat !== '',
                    ])>
                <span>Semua Kategori</span>
                <span class="px-1.5 py-0.2 rounded-full text-[11px] font-bold bg-slate-200/80 text-slate-700">
                    {{ $this->packages->count() }}
                </span>
            </button>

            <button wire:click="$set('filterCat', 'KILOAN')"
                    @class([
                        'px-3.5 py-1.5 rounded-lg text-xs sm:text-sm font-medium whitespace-nowrap transition-all flex items-center gap-1.5',
                        'bg-white text-slate-900 font-semibold shadow-xs' => $filterCat === 'KILOAN',
                        'text-slate-600 hover:text-slate-900' => $filterCat !== 'KILOAN',
                    ])>
                <span>🧺 Kiloan</span>
                <span class="px-1.5 py-0.2 rounded-full text-[11px] font-bold bg-slate-200/80 text-slate-700">
                    {{ $this->packages->where('category', 'KILOAN')->count() }}
                </span>
            </button>

            <button wire:click="$set('filterCat', 'SATUAN')"
                    @class([
                        'px-3.5 py-1.5 rounded-lg text-xs sm:text-sm font-medium whitespace-nowrap transition-all flex items-center gap-1.5',
                        'bg-white text-slate-900 font-semibold shadow-xs' => $filterCat === 'SATUAN',
                        'text-slate-600 hover:text-slate-900' => $filterCat !== 'SATUAN',
                    ])>
                <span>👔 Satuan</span>
                <span class="px-1.5 py-0.2 rounded-full text-[11px] font-bold bg-slate-200/80 text-slate-700">
                    {{ $this->packages->where('category', 'SATUAN')->count() }}
                </span>
            </button>

            <button wire:click="$set('filterCat', 'EXPRESS')"
                    @class([
                        'px-3.5 py-1.5 rounded-lg text-xs sm:text-sm font-medium whitespace-nowrap transition-all flex items-center gap-1.5',
                        'bg-white text-slate-900 font-semibold shadow-xs' => $filterCat === 'EXPRESS',
                        'text-slate-600 hover:text-slate-900' => $filterCat !== 'EXPRESS',
                    ])>
                <span>⚡ Express</span>
                <span class="px-1.5 py-0.2 rounded-full text-[11px] font-bold bg-slate-200/80 text-slate-700">
                    {{ $this->packages->where('category', 'EXPRESS')->count() }}
                </span>
            </button>
        </div>

        {{-- Search Box --}}
        <div class="relative sm:w-64">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 size-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/></svg>
            <input type="search" wire:model.live.debounce.300ms="search"
                   placeholder="Cari nama layanan..."
                   class="form-input pl-9 py-1.5 text-xs sm:text-sm w-full">
        </div>
    </div>

    {{-- ══ CATEGORY CARDS ═════════════════════════════════════════════════ --}}
    @php
        $categories = [
            'KILOAN' => [
                'name' => 'Layanan Kiloan',
                'icon' => '🧺',
                'desc' => 'Cucian ditimbang per kilogram (Reguler, Cuci Setrika, Lipat Saja)',
                'defaultUnit' => 'Kg',
            ],
            'SATUAN' => [
                'name' => 'Layanan Satuan',
                'icon' => '👔',
                'desc' => 'Cucian dihitung per item/pcs (Bedcover, Jas, Sepatu, Karpet, Boneka)',
                'defaultUnit' => 'Pcs / Meter',
            ],
            'EXPRESS' => [
                'name' => 'Layanan Express',
                'icon' => '⚡',
                'desc' => 'Layanan prioritas cepat kilat (Express 3–8 jam, Same Day)',
                'defaultUnit' => 'Kg / Pcs',
            ],
        ];

        // Filter categories if a specific category is chosen in filterCat
        $visibleCategories = $filterCat ? [$filterCat => $categories[$filterCat] ?? null] : $categories;
    @endphp

    <div class="space-y-4">
        @foreach ($visibleCategories as $catKey => $catMeta)
            @if (!$catMeta) @continue @endif
            @php
                $catPackages = $this->packages->where('category', $catKey);
                $activeCount = $catPackages->where('is_active', true)->whereNull('deleted_at')->count();
            @endphp

            {{-- Card Kategori --}}
            <div class="card p-4 sm:p-5 space-y-4">
                {{-- Category Header --}}
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-slate-100">
                    <div class="flex items-center gap-3">
                        <div class="size-10 rounded-xl bg-slate-100 flex items-center justify-center text-xl shrink-0">
                            {{ $catMeta['icon'] }}
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <h3 class="text-base font-bold text-slate-800">{{ $catMeta['name'] }}</h3>
                                <span class="badge badge-slate text-[11px] font-semibold">
                                    {{ $catPackages->count() }} paket ({{ $activeCount }} aktif)
                                </span>
                            </div>
                            <p class="text-xs text-slate-400 mt-0.5">{{ $catMeta['desc'] }}</p>
                        </div>
                    </div>

                    <div>
                        <button wire:click="openCreate('{{ $catKey }}')"
                                class="btn-secondary text-xs py-1.5 px-3 w-full sm:w-auto">
                            <svg class="size-3.5 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                            <span>Tambah Layanan {{ $catKey === 'KILOAN' ? 'Kiloan' : ($catKey === 'SATUAN' ? 'Satuan' : 'Express') }}</span>
                        </button>
                    </div>
                </div>

                {{-- Services List in this Category Card --}}
                <div class="space-y-2.5">
                    @forelse ($catPackages as $pkg)
                        <div @class([
                            'flex flex-col sm:flex-row sm:items-center justify-between p-3 sm:p-3.5 rounded-xl border border-slate-200/70 hover:border-slate-300 hover:bg-slate-50/50 transition-all gap-3',
                            'opacity-60 bg-slate-50/30' => $pkg->trashed(),
                        ])>
                            {{-- Info: Name, Duration, BOM Sabun --}}
                            <div class="space-y-1.5 flex-1 min-w-0">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <h4 class="font-bold text-slate-800 text-sm sm:text-base">{{ $pkg->name }}</h4>
                                    
                                    @if ($pkg->trashed())
                                        <span class="badge badge-rose text-[11px]">Dihapus</span>
                                    @elseif ($pkg->is_active)
                                        <span class="badge badge-green text-[11px]">Aktif</span>
                                    @else
                                        <span class="badge badge-slate text-[11px]">Nonaktif</span>
                                    @endif
                                </div>

                                <div class="flex items-center gap-2.5 text-xs text-slate-500 flex-wrap">
                                    <span class="flex items-center gap-1 font-medium text-slate-600">
                                        <svg class="size-3.5 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                                        {{ $pkg->estimated_hours }} jam
                                    </span>
                                    <span class="text-slate-300">•</span>
                                    <span class="flex items-center gap-1.5 text-slate-500 font-medium">
                                        <span>Takaran BOM:</span>
                                        <span class="bg-slate-100 px-1.5 py-0.5 rounded text-[11px] text-slate-600">Deterjen {{ $pkg->detergent_ml_per_unit }}ml</span>
                                        <span class="bg-slate-100 px-1.5 py-0.5 rounded text-[11px] text-slate-600">Softener {{ $pkg->softener_ml_per_unit }}ml</span>
                                        <span class="bg-slate-100 px-1.5 py-0.5 rounded text-[11px] text-slate-600">Parfum {{ $pkg->perfume_ml_per_unit }}ml</span>
                                    </span>
                                </div>
                            </div>

                            {{-- Price & Quick Actions --}}
                            <div class="flex items-center justify-between sm:justify-end gap-3 shrink-0 pt-2 sm:pt-0 border-t sm:border-t-0 border-slate-100">
                                <div class="text-left sm:text-right">
                                    <p class="text-base sm:text-lg font-bold text-slate-900 tabular-nums">
                                        Rp {{ number_format($pkg->price_per_unit) }}
                                        <span class="text-xs font-normal text-slate-400">/ {{ $pkg->unit }}</span>
                                    </p>
                                </div>

                                <div class="flex items-center gap-1">
                                    @if ($pkg->trashed())
                                        <button wire:click="restorePackage({{ $pkg->id }})" class="btn-secondary text-xs py-1.5 px-3">
                                            Pulihkan
                                        </button>
                                    @else
                                        {{-- Edit button --}}
                                        <button wire:click="openEdit({{ $pkg->id }})"
                                                class="p-1.5 rounded-lg border border-slate-200 text-slate-500 hover:text-slate-800 hover:bg-slate-50 transition-colors"
                                                title="Edit Layanan">
                                            <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125"/></svg>
                                        </button>

                                        {{-- Toggle Active / Nonaktif button --}}
                                        <button wire:click="toggleActive({{ $pkg->id }})"
                                                class="p-1.5 rounded-lg border border-slate-200 text-slate-500 hover:text-slate-800 hover:bg-slate-50 transition-colors"
                                                title="{{ $pkg->is_active ? 'Nonaktifkan layanan ini' : 'Aktifkan layanan ini' }}">
                                            @if ($pkg->is_active)
                                                <svg class="size-4 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/></svg>
                                            @else
                                                <svg class="size-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88"/></svg>
                                            @endif
                                        </button>

                                        {{-- Delete button --}}
                                        <button wire:click="confirmDelete({{ $pkg->id }})"
                                                class="p-1.5 rounded-lg border border-slate-200 text-slate-300 hover:text-rose-600 hover:bg-rose-50 transition-colors"
                                                title="Hapus Layanan">
                                            <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/></svg>
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="py-8 text-center text-slate-400">
                            <p class="text-sm">Belum ada layanan dalam kategori ini.</p>
                            <button wire:click="openCreate('{{ $catKey }}')" class="btn-secondary text-xs mt-2">
                                + Tambah Layanan Sekarang
                            </button>
                        </div>
                    @endforelse
                </div>
            </div>
        @endforeach
    </div>

    {{-- ══ FORM MODAL (CREATE / EDIT) ═════════════════════════════════════ --}}
    <div x-show="showForm" x-cloak style="display:none">
        <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-50" @click="showForm = false; $wire.closeForm()"></div>

        {{-- Mobile Bottom Sheet --}}
        <div x-show="showForm"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="translate-y-full"
             x-transition:enter-end="translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="translate-y-0"
             x-transition:leave-end="translate-y-full"
             class="md:hidden bottom-sheet max-h-[90vh] overflow-y-auto">
            <div class="bottom-sheet-handle"></div>
            @include('livewire.settings._service-form')
        </div>

        {{-- Desktop Centered Dialog --}}
        <div x-show="showForm"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="hidden md:flex fixed inset-0 z-50 items-center justify-center p-4 pointer-events-none">
            <div class="pointer-events-auto w-full max-w-lg bg-white rounded-2xl shadow-2xl overflow-hidden max-h-[90vh] overflow-y-auto">
                @include('livewire.settings._service-form')
            </div>
        </div>
    </div>

    {{-- ══ DELETE CONFIRMATION MODAL ══════════════════════════════════════ --}}
    <div x-show="confirmVoid" x-cloak style="display:none">
        <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-50" @click="confirmVoid = false"></div>
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 pointer-events-none">
            <div class="pointer-events-auto w-full max-w-sm bg-white rounded-2xl shadow-2xl p-6 text-center space-y-4">
                <div class="size-12 rounded-full bg-rose-50 text-rose-500 mx-auto flex items-center justify-center text-xl">
                    🗑️
                </div>
                <div>
                    <h3 class="font-bold text-slate-800 text-base">Hapus Layanan Ini?</h3>
                    <p class="text-xs text-slate-500 mt-1">Layanan akan dinonaktifkan / dihapus dari pilihan. Histori nota yang sudah ada tetap aman.</p>
                </div>
                <div class="flex gap-2">
                    <button wire:click="deletePackage" class="btn-danger flex-1 justify-center py-2 text-xs sm:text-sm">
                        Ya, Hapus
                    </button>
                    <button @click="confirmVoid = false" class="btn-secondary flex-1 justify-center py-2 text-xs sm:text-sm">
                        Batal
                    </button>
                </div>
            </div>
        </div>
    </div>

</div>
