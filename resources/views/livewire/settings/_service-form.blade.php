{{-- Shared form partial: used inside both bottom-sheet (mobile) and dialog (desktop) --}}
<div class="p-5 space-y-4">
    {{-- Header with Mobile Back Arrow and Desktop Close Button --}}
    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
        <div class="flex items-center gap-2">
            <button type="button" wire:click="closeForm" class="md:hidden p-1.5 -ml-1 text-slate-400 hover:text-slate-600 rounded-lg hover:bg-slate-100 transition-colors">
                <svg class="size-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/></svg>
            </button>
            <h3 class="font-bold text-slate-800 text-base">
                {{ $editingId ? 'Edit Layanan' : 'Tambah Layanan Baru' }}
            </h3>
        </div>
        <button type="button" wire:click="closeForm" class="hidden md:flex p-1.5 text-slate-400 hover:text-slate-600 rounded-lg hover:bg-slate-100 transition-colors">
            <svg class="size-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
        </button>
    </div>

    {{-- Nama Layanan --}}
    <div>
        <label class="form-label text-xs font-semibold text-slate-700">Nama Layanan</label>
        <input type="text" wire:model="name" placeholder="cth. Cuci Kiloan Reguler" class="form-input text-sm">
        @error('name') <p class="form-error">{{ $message }}</p> @enderror
    </div>

    {{-- Kategori + Unit --}}
    <div class="grid grid-cols-2 gap-3">
        <div>
            <label class="form-label text-xs font-semibold text-slate-700">Kategori</label>
            <select wire:model.live="category" class="form-input text-sm">
                <option value="KILOAN">Kiloan</option>
                <option value="SATUAN">Satuan</option>
                <option value="EXPRESS">Express</option>
            </select>
            @error('category') <p class="form-error">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="form-label text-xs font-semibold text-slate-700">Satuan</label>
            <select wire:model="unit" class="form-input text-sm">
                <option value="Kg">Kg</option>
                <option value="Pcs">Pcs</option>
                <option value="Meter">Meter</option>
            </select>
        </div>
    </div>

    {{-- Harga + Estimasi --}}
    <div class="grid grid-cols-2 gap-3">
        <div>
            <label class="form-label text-xs font-semibold text-slate-700">Harga / {{ $unit }}</label>
            <div class="relative">
                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs font-medium text-slate-400">Rp</span>
                <input type="number" wire:model="price_per_unit" class="form-input pl-9 text-sm font-semibold" placeholder="7000">
            </div>
            @error('price_per_unit') <p class="form-error">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="form-label text-xs font-semibold text-slate-700">Estimasi Selesai</label>
            <div class="relative">
                <input type="number" wire:model="estimated_hours" class="form-input pr-11 text-sm font-semibold" placeholder="48">
                <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-slate-400 font-medium">jam</span>
            </div>
            @error('estimated_hours') <p class="form-error">{{ $message }}</p> @enderror
        </div>
    </div>

    {{-- BOM Resep Sabun --}}
    <div class="rounded-2xl border border-sky-100 bg-sky-50/60 p-3.5 space-y-3">
        <div class="flex items-center gap-2">
            <svg class="size-4 text-sky-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9.75 3.104v5.714a2.25 2.25 0 0 1-.659 1.591L5 14.5M9.75 3.104c-.251.023-.501.05-.75.082m.75-.082a24.301 24.301 0 0 1 4.5 0m0 0v5.714c0 .597.237 1.17.659 1.591L19.8 15.3"/></svg>
            <p class="text-xs font-bold text-sky-800 uppercase tracking-wide">Resep BOM Sabun (ml per {{ $unit }})</p>
        </div>

        <div class="grid grid-cols-3 gap-2.5">
            <div>
                <label class="form-label text-[11px] font-bold text-sky-700 mb-1">Deterjen</label>
                <div class="relative">
                    <input type="number" step="0.5" wire:model.live="detergent_ml" class="form-input text-center pr-7 text-sm font-bold h-10">
                    <span class="absolute right-2 top-1/2 -translate-y-1/2 text-[10px] text-slate-400 font-semibold pointer-events-none">ml</span>
                </div>
                @error('detergent_ml') <p class="form-error text-[10px] mt-0.5">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="form-label text-[11px] font-bold text-cyan-700 mb-1">Softener</label>
                <div class="relative">
                    <input type="number" step="0.5" wire:model.live="softener_ml" class="form-input text-center pr-7 text-sm font-bold h-10">
                    <span class="absolute right-2 top-1/2 -translate-y-1/2 text-[10px] text-slate-400 font-semibold pointer-events-none">ml</span>
                </div>
                @error('softener_ml') <p class="form-error text-[10px] mt-0.5">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="form-label text-[11px] font-bold text-purple-700 mb-1">Parfum</label>
                <div class="relative">
                    <input type="number" step="0.5" wire:model.live="perfume_ml" class="form-input text-center pr-7 text-sm font-bold h-10">
                    <span class="absolute right-2 top-1/2 -translate-y-1/2 text-[10px] text-slate-400 font-semibold pointer-events-none">ml</span>
                </div>
                @error('perfume_ml') <p class="form-error text-[10px] mt-0.5">{{ $message }}</p> @enderror
            </div>
        </div>

        {{-- BOM Preview Pill --}}
        <div class="flex items-center gap-2 text-xs text-sky-800 bg-white/90 border border-sky-200/70 rounded-xl px-3 py-2 font-medium shadow-2xs">
            <svg class="size-4 text-sky-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z"/></svg>
            <span class="truncate">
                Total: <strong>{{ $this->bomPreview['total_ml'] }} ml / {{ $unit }}</strong> (Det {{ $this->bomPreview['deterjen_ml'] }} + Sof {{ $this->bomPreview['softener_ml'] }} + Par {{ $this->bomPreview['parfum_ml'] }})
            </span>
        </div>
    </div>

    {{-- Status toggle --}}
    <div class="flex items-center justify-between py-2 border-t border-slate-100">
        <div>
            <p class="text-sm font-bold text-slate-800">Auto-Approve (Langsung Aktif)</p>
            <p class="text-xs text-slate-400 mt-0.5">Otomatis disetujui &amp; langsung muncul di Kasir POS</p>
        </div>
        <button type="button" wire:click="$toggle('is_active')"
            @class([
                'relative inline-flex h-6 w-11 items-center rounded-full transition-colors duration-200 focus:outline-none shrink-0',
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

    {{-- Actions (Full Width Single Primary Button) --}}
    <div class="pt-2 border-t border-slate-100">
        <button type="button" wire:click="save" wire:loading.attr="disabled" wire:target="save"
                class="btn-primary w-full justify-center py-3 text-sm font-bold shadow-md">
            <span wire:loading.remove wire:target="save">{{ $editingId ? 'Simpan Perubahan' : 'Tambahkan Layanan' }}</span>
            <span wire:loading wire:target="save" class="flex items-center gap-2">
                <svg class="size-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                Menyimpan…
            </span>
        </button>
    </div>
</div>
