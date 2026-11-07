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

    {{-- Default Layanan: pre-fill Cuci/Setrika di POS --}}
    <div class="rounded-2xl border border-slate-200 bg-slate-50/60 p-3.5 space-y-2.5">
        <div class="flex items-center gap-2">
            <svg class="size-4 text-slate-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 0 1 0 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 0 1-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 0 1-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 0 1-1.369-.49l-1.297-2.247a1.125 1.125 0 0 1 .26-1.431l1.004-.827c.292-.241.437-.613.43-.992a7.723 7.723 0 0 1 0-.255c.007-.378-.138-.75-.43-.991l-1.004-.827a1.125 1.125 0 0 1-.26-1.43l1.297-2.247a1.125 1.125 0 0 1 1.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.28Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/></svg>
            <p class="text-xs font-bold text-slate-700 uppercase tracking-wide">Default Layanan di POS</p>
        </div>
        <p class="text-[11px] text-slate-400 -mt-1">Saat paket ini dipilih kasir, toggle Cuci/Setrika otomatis terisi sesuai di bawah.</p>
        <div class="grid grid-cols-2 gap-2.5">
            {{-- Default Cuci --}}
            <label class="flex items-center gap-2.5 p-2.5 rounded-xl border cursor-pointer transition-all select-none
                          {{ $default_include_wash ? 'border-sky-400 bg-sky-50/70' : 'border-slate-200 bg-white' }}">
                <input type="checkbox" wire:model.live="default_include_wash" class="sr-only">
                <div class="shrink-0 size-7 rounded-lg flex items-center justify-center
                            {{ $default_include_wash ? 'bg-sky-500 text-white' : 'bg-slate-100 text-slate-400' }}">
                    <svg class="size-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5c0-1.657 3.134-3 7-3s7 1.343 7 3m0 0v4.5A2.25 2.25 0 0 1 15.5 20h-7a2.25 2.25 0 0 1-2.25-2.25V13.5"/></svg>
                </div>
                <span class="text-xs font-bold {{ $default_include_wash ? 'text-sky-700' : 'text-slate-500' }}">Cuci</span>
            </label>
            {{-- Default Setrika --}}
            <label class="flex items-center gap-2.5 p-2.5 rounded-xl border cursor-pointer transition-all select-none
                          {{ $default_include_iron ? 'border-amber-400 bg-amber-50/70' : 'border-slate-200 bg-white' }}">
                <input type="checkbox" wire:model.live="default_include_iron" class="sr-only">
                <div class="shrink-0 size-7 rounded-lg flex items-center justify-center
                            {{ $default_include_iron ? 'bg-amber-400 text-white' : 'bg-slate-100 text-slate-400' }}">
                    <svg class="size-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6.633 10.25c.806 0 1.533-.446 2.031-1.08a9.041 9.041 0 0 1 2.861-2.4c.723-.384 1.35-.956 1.653-1.715a4.498 4.498 0 0 0 .322-1.672V2.75a.75.75 0 0 1 .75-.75 2.25 2.25 0 0 1 2.25 2.25c0 1.152-.26 2.243-.723 3.218-.266.558.107 1.282.725 1.282m0 0h3.126c1.026 0 1.945.694 2.054 1.715.045.422.068.85.068 1.285a11.95 11.95 0 0 1-2.649 7.521c-.388.482-.987.729-1.605.729H13.48c-.483 0-.964-.078-1.423-.23l-3.114-1.04a4.501 4.501 0 0 0-1.423-.23H5.904"/></svg>
                </div>
                <span class="text-xs font-bold {{ $default_include_iron ? 'text-amber-700' : 'text-slate-500' }}">Setrika</span>
            </label>
        </div>
        @if (! $default_include_wash && ! $default_include_iron)
            <p class="text-[11px] text-rose-600 flex items-center gap-1">
                <svg class="size-3 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/></svg>
                Default minimal satu layanan harus aktif.
            </p>
        @endif
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
