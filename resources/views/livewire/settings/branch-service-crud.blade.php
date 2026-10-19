<div x-data="{ showForm: @entangle('showForm'), confirmVoid: @entangle('confirmVoid') }" class="space-y-5">

{{-- ══ HEADER ══════════════════════════════════════════════════════ --}}
<div class="flex flex-col sm:flex-row sm:items-center gap-3">
    <div>
        <h2 class="text-lg font-bold text-slate-800">Master Layanan &amp; Resep BOM</h2>
        <p class="text-xs text-slate-400 mt-0.5">Kelola paket layanan dan takaran otomatis sabun per Kg</p>
    </div>
    <div class="flex items-center gap-2 sm:ml-auto">
        {{-- Search --}}
        <div class="relative flex-1 min-w-0 sm:w-56">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 size-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/></svg>
            <input type="search" wire:model.live.debounce.300ms="search"
                   placeholder="Cari layanan…"
                   class="form-input pl-9 py-2 text-sm">
        </div>
        {{-- Category filter --}}
        <select wire:model.live="filterCat" class="form-input py-2 text-sm w-auto">
            <option value="">Semua</option>
            <option value="KILOAN">Kiloan</option>
            <option value="SATUAN">Satuan</option>
            <option value="EXPRESS">Express</option>
        </select>
        {{-- Add button --}}
        <button wire:click="openCreate" class="btn-primary whitespace-nowrap">
            <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
            <span class="hidden sm:inline">Tambah Layanan</span>
        </button>
    </div>
</div>

{{-- ══ CATEGORY STATS ═══════════════════════════════════════════════ --}}
<div class="grid grid-cols-3 gap-3">
    @foreach (['KILOAN' => ['bg-sky-50', 'text-sky-700', 'ring-sky-200'], 'SATUAN' => ['bg-cyan-50', 'text-cyan-700', 'ring-cyan-200'], 'EXPRESS' => ['bg-amber-50', 'text-amber-700', 'ring-amber-200']] as $cat => $colors)
    @php $stat = $this->categoryStats[$cat] ?? ['total' => 0, 'active' => 0]; @endphp
    <div class="card p-4">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide">{{ $cat }}</p>
                <p class="text-2xl font-bold text-slate-800 mt-1">{{ $stat['total'] ?? 0 }}</p>
                <p class="text-xs text-slate-400 mt-0.5">{{ $stat['active'] ?? 0 }} aktif</p>
            </div>
            <span class="badge {{ $colors[0] }} {{ $colors[1] }}" style="box-shadow: 0 0 0 1px {{ $colors[2] }}">{{ $cat }}</span>
        </div>
    </div>
    @endforeach
</div>

{{-- ══ TABLE (Desktop) ══════════════════════════════════════════════ --}}
<div class="card hidden md:block overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-slate-50/80 border-b border-slate-200/80">
                <tr>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wide">Layanan</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wide">Kategori</th>
                    <th class="text-right px-4 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wide">Harga / Unit</th>
                    <th class="text-center px-4 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wide">Estimasi</th>
                    <th class="text-center px-4 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wide">BOM (ml/unit)</th>
                    <th class="text-center px-4 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wide">Status</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($this->packages as $pkg)
                <tr @class(['hover:bg-slate-50/60 transition-colors', 'opacity-50' => $pkg->trashed()])>
                    <td class="px-4 py-3">
                        <p class="font-semibold text-slate-800">{{ $pkg->name }}</p>
                        <p class="text-xs text-slate-400">{{ $pkg->unit }}</p>
                    </td>
                    <td class="px-4 py-3">
                        @php $catClass = match($pkg->category) {
                            'KILOAN'  => 'badge-sky',
                            'EXPRESS' => 'badge-amber',
                            default   => 'badge-cyan',
                        }; @endphp
                        <span class="badge {{ $catClass }}">{{ $pkg->category }}</span>
                    </td>
                    <td class="px-4 py-3 text-right font-semibold text-slate-800 tabular-nums">
                        Rp {{ number_format($pkg->price_per_unit) }}
                        <span class="font-normal text-slate-400 text-xs">/ {{ $pkg->unit }}</span>
                    </td>
                    <td class="px-4 py-3 text-center text-sm text-slate-600">{{ $pkg->estimated_hours }} jam</td>
                    <td class="px-4 py-3">
                        <div class="flex items-center justify-center gap-2 text-xs">
                            <span class="badge badge-sky" title="Deterjen">D {{ $pkg->detergent_ml_per_unit }}ml</span>
                            <span class="badge badge-slate" title="Softener">S {{ $pkg->softener_ml_per_unit }}ml</span>
                            <span class="badge" style="background:#f5f3ff;color:#5b21b6;box-shadow:0 0 0 1px #ddd6fe" title="Parfum">P {{ $pkg->perfume_ml_per_unit }}ml</span>
                        </div>
                    </td>
                    <td class="px-4 py-3 text-center">
                        @if ($pkg->trashed())
                            <span class="badge badge-rose">Dihapus</span>
                        @elseif ($pkg->is_active)
                            <span class="badge badge-green">Aktif</span>
                        @else
                            <span class="badge badge-slate">Nonaktif</span>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex items-center justify-end gap-1">
                            @if ($pkg->trashed())
                                <button wire:click="restorePackage({{ $pkg->id }})" class="btn-secondary text-xs py-1 px-2.5">Pulihkan</button>
                            @else
                                <button wire:click="openEdit({{ $pkg->id }})" class="btn-icon" title="Edit">
                                    <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125"/></svg>
                                </button>
                                <button wire:click="toggleActive({{ $pkg->id }})" class="btn-icon" title="{{ $pkg->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                    @if ($pkg->is_active)
                                        <svg class="size-4 text-amber-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88"/></svg>
                                    @else
                                        <svg class="size-4 text-emerald-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/></svg>
                                    @endif
                                </button>
                                <button wire:click="confirmDelete({{ $pkg->id }})" class="btn-icon" title="Hapus">
                                    <svg class="size-4 text-rose-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/></svg>
                                </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="px-4 py-12 text-center text-slate-400">
                    <p class="text-2xl mb-2">🧴</p>
                    <p class="font-medium">Belum ada layanan</p>
                    <p class="text-xs mt-1">Klik "Tambah Layanan" untuk mulai</p>
                </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- ══ CARD LIST (Mobile) ═══════════════════════════════════════════ --}}
<div class="md:hidden space-y-3">
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
            <p class="text-lg font-bold text-sky-600 tabular-nums">Rp {{ number_format($pkg->price_per_unit) }}<span class="text-xs font-normal text-slate-400">/{{ $pkg->unit }}</span></p>
            @if ($pkg->trashed()) <span class="badge badge-rose">Dihapus</span>
            @elseif ($pkg->is_active) <span class="badge badge-green">Aktif</span>
            @else <span class="badge badge-slate">Nonaktif</span> @endif
        </div>
        <div class="flex gap-1.5 flex-wrap text-xs">
            <span class="badge badge-sky">D {{ $pkg->detergent_ml_per_unit }}ml</span>
            <span class="badge badge-slate">S {{ $pkg->softener_ml_per_unit }}ml</span>
            <span class="badge" style="background:#f5f3ff;color:#5b21b6;box-shadow:0 0 0 1px #ddd6fe">P {{ $pkg->perfume_ml_per_unit }}ml</span>
        </div>
        <div class="flex gap-2 pt-1 border-t border-slate-100">
            @if ($pkg->trashed())
                <button wire:click="restorePackage({{ $pkg->id }})" class="btn-secondary text-xs py-1.5 flex-1">Pulihkan</button>
            @else
                <button wire:click="openEdit({{ $pkg->id }})" class="btn-secondary text-xs py-1.5 flex-1">Edit</button>
                <button wire:click="toggleActive({{ $pkg->id }})" class="btn-secondary text-xs py-1.5 flex-1">{{ $pkg->is_active ? 'Nonaktifkan' : 'Aktifkan' }}</button>
                <button wire:click="confirmDelete({{ $pkg->id }})" class="btn-danger text-xs py-1.5 px-3">Hapus</button>
            @endif
        </div>
    </div>
    @empty
    <div class="card p-12 text-center text-slate-400">
        <p class="text-3xl mb-2">🧴</p>
        <p class="font-medium">Belum ada layanan</p>
    </div>
    @endforelse
</div>

{{-- ══ FORM MODAL ═══════════════════════════════════════════════════ --}}
<div x-show="showForm" x-cloak style="display:none">
    {{-- Overlay --}}
    <div class="bottom-sheet-overlay md:hidden" @click="showForm = false"></div>
    <div class="dialog-overlay hidden md:flex" @click.self="showForm = false"></div>

    {{-- Mobile: Bottom Sheet --}}
    <div x-show="showForm"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="translate-y-full"
         x-transition:enter-end="translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="translate-y-0"
         x-transition:leave-end="translate-y-full"
         class="bottom-sheet md:hidden">
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
    <div class="dialog-overlay">
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
