{{-- Shared form partial: used inside both bottom-sheet (mobile) and dialog (desktop) --}}
<div class="p-5 space-y-4">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <h3 class="font-bold text-slate-800 text-base">
            {{ $editingId ? 'Edit Layanan' : 'Tambah Layanan Baru' }}
        </h3>
        <button wire:click="closeForm" class="btn-icon">
            <svg class="size-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
        </button>
    </div>

    {{-- Nama Layanan --}}
    <div>
        <label class="form-label">Nama Layanan</label>
        <input type="text" wire:model="name" placeholder="cth. Cuci Kiloan Reguler" class="form-input">
        @error('name') <p class="form-error">{{ $message }}</p> @enderror
    </div>

    {{-- Kategori + Unit --}}
    <div class="grid grid-cols-2 gap-3">
        <div>
            <label class="form-label">Kategori</label>
            <select wire:model.live="category" class="form-input">
                <option value="KILOAN">Kiloan</option>
                <option value="SATUAN">Satuan</option>
                <option value="EXPRESS">Express</option>
            </select>
            @error('category') <p class="form-error">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="form-label">Satuan</label>
            <select wire:model="unit" class="form-input">
                <option value="Kg">Kg</option>
                <option value="Pcs">Pcs</option>
                <option value="Meter">Meter</option>
            </select>
        </div>
    </div>

    {{-- Harga + Estimasi --}}
    <div class="grid grid-cols-2 gap-3">
        <div>
            <label class="form-label">Harga / {{ $unit }}</label>
            <div class="relative">
                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm text-slate-400">Rp</span>
                <input type="number" wire:model="price_per_unit" class="form-input pl-9" placeholder="7000">
            </div>
            @error('price_per_unit') <p class="form-error">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="form-label">Estimasi Selesai</label>
            <div class="relative">
                <input type="number" wire:model="estimated_hours" class="form-input pr-10" placeholder="48">
                <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-slate-400">jam</span>
            </div>
            @error('estimated_hours') <p class="form-error">{{ $message }}</p> @enderror
        </div>
    </div>

    {{-- BOM Resep Sabun --}}
    <div class="rounded-xl border border-sky-100 bg-sky-50/50 p-4 space-y-3">
        <div class="flex items-center gap-2">
            <svg class="size-4 text-sky-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9.75 3.104v5.714a2.25 2.25 0 0 1-.659 1.591L5 14.5M9.75 3.104c-.251.023-.501.05-.75.082m.75-.082a24.301 24.301 0 0 1 4.5 0m0 0v5.714c0 .597.237 1.17.659 1.591L19.8 15.3"/></svg>
            <p class="text-xs font-bold text-sky-700 uppercase tracking-wide">Resep BOM (ml per {{ $unit }})</p>
        </div>
        <div class="grid grid-cols-3 gap-3">
            <div>
                <label class="form-label text-sky-600">Deterjen</label>
                <div class="relative">
                    <input type="number" step="0.5" wire:model.live="detergent_ml" class="form-input pr-7">
                    <span class="absolute right-2.5 top-1/2 -translate-y-1/2 text-[10px] text-slate-400">ml</span>
                </div>
                @error('detergent_ml') <p class="form-error">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="form-label text-slate-500">Softener</label>
                <div class="relative">
                    <input type="number" step="0.5" wire:model.live="softener_ml" class="form-input pr-7">
                    <span class="absolute right-2.5 top-1/2 -translate-y-1/2 text-[10px] text-slate-400">ml</span>
                </div>
                @error('softener_ml') <p class="form-error">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="form-label" style="color:#7c3aed">Parfum</label>
                <div class="relative">
                    <input type="number" step="0.5" wire:model.live="perfume_ml" class="form-input pr-7">
                    <span class="absolute right-2.5 top-1/2 -translate-y-1/2 text-[10px] text-slate-400">ml</span>
                </div>
                @error('perfume_ml') <p class="form-error">{{ $message }}</p> @enderror
            </div>
        </div>
        {{-- BOM Preview pill --}}
        <div class="flex items-center gap-2 text-xs text-sky-700 font-medium">
            <svg class="size-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z"/></svg>
            Total: {{ $this->bomPreview['total_ml'] }} ml / {{ $unit }}
            &nbsp;·&nbsp; Deterjen {{ $this->bomPreview['deterjen_ml'] }}
            + Softener {{ $this->bomPreview['softener_ml'] }}
            + Parfum {{ $this->bomPreview['parfum_ml'] }} ml
        </div>
    </div>

    {{-- Status toggle --}}
    <div class="flex items-center justify-between py-2">
        <div>
            <p class="text-sm font-semibold text-slate-700">Layanan Aktif</p>
            <p class="text-xs text-slate-400">Tampil di POS Kasir</p>
        </div>
        <button type="button" wire:click="$toggle('is_active')"
            @class([
                'relative inline-flex h-6 w-11 items-center rounded-full transition-colors duration-200 focus:outline-none',
                'bg-sky-600' => $is_active,
                'bg-slate-200' => ! $is_active,
            ])>
            <span @class([
                'inline-block size-4 rounded-full bg-white shadow transform transition-transform duration-200',
                'translate-x-6' => $is_active,
                'translate-x-1' => ! $is_active,
            ])></span>
        </button>
    </div>

    {{-- Actions --}}
    <div class="flex gap-3 pt-2 border-t border-slate-100">
        <button wire:click="save" wire:loading.attr="disabled" class="btn-primary flex-1">
            <span wire:loading.remove wire:target="save">{{ $editingId ? 'Simpan Perubahan' : 'Tambah Layanan' }}</span>
            <span wire:loading wire:target="save" class="flex items-center gap-2">
                <svg class="size-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                Menyimpan…
            </span>
        </button>
        <button wire:click="closeForm" class="btn-secondary">Batal</button>
    </div>
</div>
