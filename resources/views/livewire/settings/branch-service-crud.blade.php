<div x-data="{
    showForm: @entangle('showForm'),
    showCategoryModal: @entangle('showCategoryModal'),
    confirmVoid: @entangle('confirmVoid'),
    showAllTable: false,
    lockScroll(lock) {
        if (lock) {
            document.body.style.overflow = 'hidden';
            document.body.style.touchAction = 'none';
        } else {
            document.body.style.overflow = '';
            document.body.style.touchAction = '';
        }
    }
}"
x-init="
    $watch('showCategoryModal', v => lockScroll(v || showForm || confirmVoid));
    $watch('showForm', v => lockScroll(v || showCategoryModal || confirmVoid));
    $watch('confirmVoid', v => lockScroll(v || showCategoryModal || showForm));
"
class="space-y-5">

{{-- ══ HEADER ══════════════════════════════════════════════════════ --}}
<div class="space-y-3">
    {{-- Top Row: Title + Add Button --}}
    <div class="flex items-center justify-between gap-3">
        <div>
            <h2 class="text-lg font-bold text-slate-800">Master Layanan &amp; Resep BOM</h2>
            <p class="text-xs text-slate-400 mt-0.5">Kelola paket, atur harga, &amp; takaran bahan baku POS</p>
        </div>
        <button wire:click="openCreate" class="btn-primary whitespace-nowrap text-sm py-2 shrink-0">
            <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
            <span class="hidden sm:inline">Tambah Layanan</span>
            <span class="sm:hidden">Layanan</span>
        </button>
    </div>

    {{-- Filter Row: Search + Category Selector --}}
    <div class="flex items-center gap-2">
        <div class="relative flex-1 min-w-0">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 size-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/></svg>
            <input type="search" wire:model.live.debounce.300ms="search"
                   placeholder="Cari paket layanan…"
                   class="form-input pl-9 py-2 text-sm w-full">
        </div>
        <select wire:model.live="filterCat" class="form-input py-2 text-sm w-auto font-medium shrink-0">
            <option value="">Semua Kategori</option>
            <option value="KILOAN">🧺 Kiloan</option>
            <option value="SATUAN">👔 Satuan</option>
            <option value="EXPRESS">⚡ Express</option>
        </select>
    </div>
</div>

{{-- ══ CATEGORY CARDS (CLICK TO OPEN INSPECT MODAL IN MOBILE, OR QUICK FILTER IN DESKTOP) ═════ --}}
<div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5">
    @php
        $catMeta = [
            'KILOAN'  => ['badge-sky',   '🧺', 'Kiloan Cuci Komplit, Kering, Setrika'],
            'SATUAN'  => ['badge-cyan',  '👔', 'Satuan Kemeja, Jas, Bedcover, Karpet'],
            'EXPRESS' => ['badge-amber', '⚡', 'Layanan Kilat Selesai 3 - 8 Jam'],
        ];
    @endphp
    @foreach ($catMeta as $cat => [$badgeCls, $icon, $desc])
        @php
            $stat = $this->categoryStats[$cat] ?? ['total' => 0, 'active' => 0];
            $isSelected = ($filterCat === $cat);
        @endphp
        <button type="button"
                @click="if (window.innerWidth < 768) { $wire.openCategoryInspect('{{ $cat }}') } else { $wire.set('filterCat', '{{ $isSelected ? '' : $cat }}') }"
                @class([
                    'card p-4 sm:p-5 text-left transition-all group focus:outline-none focus:ring-2 focus:ring-sky-500/20',
                    'border-sky-500 ring-2 ring-sky-500/20 bg-sky-50/40' => $isSelected,
                    'hover:border-sky-300 hover:shadow-md' => !$isSelected,
                ])>
            <div class="flex items-start justify-between">
                <div class="flex items-center gap-3">
                    <span class="text-3xl group-hover:scale-110 transition-transform">{{ $icon }}</span>
                    <div>
                        <p class="text-xs font-bold text-slate-500 uppercase tracking-wide group-hover:text-sky-700 transition-colors">{{ $cat }}</p>
                        <p class="text-2xl font-bold text-slate-800 mt-0.5">{{ $stat['total'] ?? 0 }} <span class="text-xs font-normal text-slate-400">paket</span></p>
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
                    <span class="md:hidden">Buka Paket</span>
                    <span class="hidden md:inline">{{ $isSelected ? 'Reset Filter' : 'Filter Tabel' }}</span>
                    <svg class="size-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/></svg>
                </span>
            </div>
        </button>
    @endforeach
</div>

{{-- ══ DESKTOP MASTER TABLE (ALWAYS VISIBLE & HIGHLY POLISHED) ════════ --}}
<div class="hidden md:block space-y-3 pt-2">
    {{-- Section header & filter status bar --}}
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-2">
            <h3 class="font-bold text-slate-800 text-base">Daftar Paket Layanan &amp; Resep BOM</h3>
            <span class="badge badge-sky text-xs font-semibold">{{ $this->packages->count() }} Layanan</span>
            @if ($filterCat !== '')
                <button wire:click="$set('filterCat', '')" class="badge badge-slate text-xs font-medium hover:bg-slate-200 transition-colors" title="Klik untuk hapus filter">
                    Kategori: {{ $filterCat }} <span class="text-slate-400 ml-1 font-bold">✕</span>
                </button>
            @endif
            @if ($search !== '')
                <button wire:click="$set('search', '')" class="badge badge-slate text-xs font-medium hover:bg-slate-200 transition-colors" title="Klik untuk hapus pencarian">
                    Cari: "{{ $search }}" <span class="text-slate-400 ml-1 font-bold">✕</span>
                </button>
            @endif
        </div>
        @if ($filterCat !== '' || $search !== '')
            <button wire:click="$set('search', ''); $wire.set('filterCat', '')" class="text-xs text-sky-600 hover:underline font-medium">
                Tampilkan Semua Layanan
            </button>
        @endif
    </div>

    {{-- Main Desktop Table --}}
    <div class="card overflow-hidden border border-slate-200/80 shadow-xs">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50/80 border-b border-slate-200/80 text-xs font-semibold text-slate-400 uppercase tracking-wide">
                    <tr>
                        <th class="text-left px-5 py-3.5">Layanan</th>
                        <th class="text-left px-4 py-3.5">Kategori</th>
                        <th class="text-right px-4 py-3.5">Harga / Satuan</th>
                        <th class="text-center px-4 py-3.5">Estimasi</th>
                        <th class="text-center px-4 py-3.5">Resep BOM Sabun</th>
                        <th class="text-center px-4 py-3.5">Status POS</th>
                        <th class="text-right px-5 py-3.5">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse ($this->packages as $pkg)
                        <tr @class(['hover:bg-slate-50/70 transition-colors', 'opacity-50 bg-slate-50/30' => $pkg->trashed()])>
                            <td class="px-5 py-3.5">
                                <p class="font-bold text-slate-800">{{ $pkg->name }}</p>
                                <p class="text-xs text-slate-400 mt-0.5">Satuan: <span class="font-medium text-slate-600">{{ $pkg->unit }}</span></p>
                            </td>
                            <td class="px-4 py-3.5">
                                @php $catClass = match($pkg->category) {
                                    'KILOAN'  => 'badge-sky',
                                    'EXPRESS' => 'badge-amber',
                                    default   => 'badge-cyan',
                                }; @endphp
                                <span class="badge {{ $catClass }} font-semibold">{{ $pkg->category }}</span>
                            </td>
                            <td class="px-4 py-3.5 text-right font-bold text-sky-700 tabular-nums">
                                Rp {{ number_format($pkg->price_per_unit) }}
                                <span class="font-normal text-slate-400 text-xs">/{{ $pkg->unit }}</span>
                            </td>
                            <td class="px-4 py-3.5 text-center text-sm text-slate-600 font-medium">
                                <span class="inline-flex items-center gap-1 bg-slate-100 px-2.5 py-1 rounded-md text-xs">
                                    ⏱️ {{ $pkg->estimated_hours }} jam
                                </span>
                            </td>
                            <td class="px-4 py-3.5">
                                <div class="flex items-center justify-center gap-1.5 text-xs font-mono">
                                    <span class="badge badge-sky" title="Deterjen">D: {{ (float)$pkg->detergent_ml_per_unit }}ml</span>
                                    <span class="badge badge-slate" title="Softener">S: {{ (float)$pkg->softener_ml_per_unit }}ml</span>
                                    <span class="badge" style="background:#f5f3ff;color:#5b21b6;box-shadow:0 0 0 1px #ddd6fe" title="Parfum">P: {{ (float)$pkg->perfume_ml_per_unit }}ml</span>
                                </div>
                            </td>
                            <td class="px-4 py-3.5 text-center">
                                @if ($pkg->trashed())
                                    <span class="badge badge-rose text-xs">Dihapus</span>
                                @else
                                    <button type="button"
                                            wire:click="toggleActive({{ $pkg->id }})"
                                            class="transition-transform active:scale-95 inline-block"
                                            title="Klik untuk {{ $pkg->is_active ? 'nonaktifkan' : 'aktifkan' }}">
                                        @if ($pkg->is_active)
                                            <span class="badge badge-green text-xs font-semibold flex items-center gap-1 py-1 px-2.5">
                                                <span class="size-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                                Aktif di POS
                                            </span>
                                        @else
                                            <span class="badge badge-slate text-xs font-medium py-1 px-2.5">
                                                Nonaktif
                                            </span>
                                        @endif
                                    </button>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    @if ($pkg->trashed())
                                        <button wire:click="restorePackage({{ $pkg->id }})" class="btn-secondary text-xs py-1 px-2.5">Pulihkan</button>
                                    @else
                                        <button wire:click="openEdit({{ $pkg->id }})"
                                                class="p-1.5 text-slate-500 hover:text-sky-600 hover:bg-sky-50 rounded-lg transition-colors"
                                                title="Edit Layanan">
                                            <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125"/></svg>
                                        </button>
                                        <button wire:click="confirmDelete({{ $pkg->id }})"
                                                class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors"
                                                title="Hapus Layanan">
                                            <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/></svg>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-12 text-center text-slate-400">
                                <p class="text-3xl mb-1">🔍</p>
                                <p class="font-semibold text-slate-700">Tidak ada layanan yang sesuai</p>
                                <p class="text-xs text-slate-400 mt-1">Coba sesuaikan kata kunci atau filter kategori Anda.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- ══ MOBILE SEARCH RESULTS / GUIDANCE (MOBILE ONLY) ════════════════ --}}
<div class="md:hidden">
    @if ($search !== '' || $filterCat !== '')
        <div class="flex items-center justify-between pt-2 mb-3">
            <p class="text-xs font-semibold text-slate-500">
                Hasil Pencarian: <span class="text-slate-800 font-bold">"{{ $search ?: $filterCat }}"</span> ({{ $this->packages->count() }})
            </p>
            <button wire:click="$set('search', ''); $wire.set('filterCat', '')" class="text-xs text-sky-600 hover:underline">
                Reset
            </button>
        </div>

        <div class="space-y-3">
            @forelse ($this->packages as $pkg)
                <div @class(['card p-4 space-y-3', 'opacity-60' => $pkg->trashed()])>
                    <div class="flex items-start justify-between gap-2">
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold text-slate-800 truncate">{{ $pkg->name }}</p>
                            <p class="text-xs text-slate-400 mt-0.5">{{ $pkg->estimated_hours }} jam · {{ $pkg->unit }}</p>
                        </div>
                        @php $catClass = match($pkg->category) { 'KILOAN' => 'badge-sky', 'EXPRESS' => 'badge-amber', default => 'badge-cyan' }; @endphp
                        <span class="badge {{ $catClass }} shrink-0">{{ $pkg->category }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <p class="text-base font-bold text-sky-700 tabular-nums">Rp {{ number_format($pkg->price_per_unit) }}<span class="text-xs font-normal text-slate-400">/{{ $pkg->unit }}</span></p>
                        @if ($pkg->trashed()) <span class="badge badge-rose">Dihapus</span>
                        @elseif ($pkg->is_active) <span class="badge badge-green">Aktif</span>
                        @else <span class="badge badge-slate">Nonaktif</span> @endif
                    </div>
                    <div class="flex gap-2 pt-1 border-t border-slate-100">
                        @if ($pkg->trashed())
                            <button wire:click="restorePackage({{ $pkg->id }})" class="btn-secondary text-xs py-1.5 flex-1">Pulihkan</button>
                        @else
                            <button wire:click="openEdit({{ $pkg->id }})" class="btn-secondary text-xs py-1.5 flex-1">Edit</button>
                            <button wire:click="toggleActive({{ $pkg->id }})" class="btn-secondary text-xs py-1.5 flex-1">{{ $pkg->is_active ? 'Nonaktifkan' : 'Aktifkan' }}</button>
                        @endif
                    </div>
                </div>
            @empty
                <div class="card p-8 text-center text-slate-400">
                    Tidak ada layanan yang sesuai.
                </div>
            @endforelse
        </div>
    @else
        <div class="card p-6 text-center border-dashed border-2 border-slate-200/80 bg-white/60 mt-3">
            <div class="max-w-md mx-auto space-y-2">
                <span class="text-2xl">🧺</span>
                <h4 class="font-bold text-slate-800 text-sm">Inspeksi Paket</h4>
                <p class="text-xs text-slate-400 leading-relaxed">
                    Ketuk salah satu kartu kategori di atas (<strong class="text-slate-600">Kiloan</strong>, <strong class="text-slate-600">Satuan</strong>, atau <strong class="text-slate-600">Express</strong>) untuk mengelola paket layanan.
                </p>
            </div>
        </div>
    @endif
</div>

{{-- ══ CATEGORY INSPECT MODAL (POPUP DESKTOP & BOTTOM-SHEET MOBILE) ══ --}}
<div x-show="showCategoryModal" x-cloak style="display:none">
    {{-- Mobile Overlay with @touchmove.prevent so background cannot scroll --}}
    <div class="bottom-sheet-overlay md:hidden"
         @click="showCategoryModal = false; $wire.closeCategoryInspect()"
         @touchmove.prevent></div>

    {{-- Desktop Overlay --}}
    <div class="dialog-overlay hidden md:flex"
         @click.self="showCategoryModal = false; $wire.closeCategoryInspect()"></div>

    {{-- Mobile: Slide-Up Bottom Sheet --}}
    <div x-show="showCategoryModal"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="translate-y-full"
         x-transition:enter-end="translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="translate-y-0"
         x-transition:leave-end="translate-y-full"
         class="bottom-sheet md:hidden overscroll-contain"
         @touchmove.stop>
        <div class="bottom-sheet-handle"></div>
        @include('livewire.settings._category-inspect-modal')
    </div>

    {{-- Desktop: Centered Dialog Popup Modal --}}
    <div x-show="showCategoryModal"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="hidden md:flex fixed inset-0 z-50 items-center justify-center p-4 pointer-events-none">
        <div class="pointer-events-auto w-full max-w-4xl bg-white rounded-2xl shadow-2xl overflow-hidden">
            @include('livewire.settings._category-inspect-modal')
        </div>
    </div>
</div>

{{-- ══ FORM MODAL: CREATE / EDIT SERVICE ════════════════════════════ --}}
<div x-show="showForm" x-cloak style="display:none">
    {{-- Mobile Overlay with @touchmove.prevent --}}
    <div class="bottom-sheet-overlay md:hidden"
         @click="showForm = false; $wire.closeForm()"
         @touchmove.prevent></div>

    {{-- Desktop Overlay --}}
    <div class="dialog-overlay hidden md:flex"
         @click.self="showForm = false; $wire.closeForm()"></div>

    {{-- Mobile: Bottom Sheet --}}
    <div x-show="showForm"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="translate-y-full"
         x-transition:enter-end="translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="translate-y-0"
         x-transition:leave-end="translate-y-full"
         class="bottom-sheet md:hidden overscroll-contain"
         @touchmove.stop>
        <div class="bottom-sheet-handle"></div>
        @include('livewire.settings._service-form')
    </div>

    {{-- Desktop: Centered Dialog --}}
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

{{-- ══ DELETE CONFIRM ═══════════════════════════════════════════════ --}}
<div x-show="confirmVoid" x-cloak style="display:none">
    <div class="dialog-overlay" @touchmove.prevent>
        <div x-show="confirmVoid"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             class="dialog-panel p-6">
            <div class="flex items-start gap-4">
                <div class="flex items-center justify-center size-10 rounded-full bg-rose-50 text-rose-500 shrink-0">
                    <svg class="size-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/></svg>
                </div>
                <div>
                    <p class="font-bold text-slate-800">Hapus Layanan?</p>
                    <p class="text-sm text-slate-500 mt-1">Layanan akan di-soft-delete. Histori nota lama tetap aman.</p>
                </div>
            </div>
            <div class="flex gap-3 mt-6">
                <button wire:click="deletePackage" class="btn-danger flex-1">Ya, Hapus</button>
                <button @click="confirmVoid = false" class="btn-secondary flex-1">Batal</button>
            </div>
        </div>
    </div>
</div>

</div>
