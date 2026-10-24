<div class="p-5 space-y-4">
    {{-- Header with Mobile Back Arrow and Desktop Close Button --}}
    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
        <div class="flex items-center gap-2">
            <button type="button" wire:click="$set('showForm', false)" class="md:hidden p-1.5 -ml-1 text-slate-400 hover:text-slate-600 rounded-lg hover:bg-slate-100 transition-colors">
                <svg class="size-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/></svg>
            </button>
            <h3 class="font-bold text-slate-800 text-base">{{ $editingId ? 'Edit Data Karyawan' : 'Tambah Karyawan Baru' }}</h3>
        </div>
        <button type="button" wire:click="$set('showForm', false)" class="hidden md:flex p-1.5 text-slate-400 hover:text-slate-600 rounded-lg hover:bg-slate-100 transition-colors">
            <svg class="size-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
        </button>
    </div>

    {{-- Form Fields --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
        <div class="sm:col-span-2">
            <label class="form-label text-xs font-semibold text-slate-700">Nama Lengkap</label>
            <input type="text" wire:model="name" class="form-input text-sm" placeholder="cth. Budi Santoso">
            @error('name') <p class="form-error">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="form-label text-xs font-semibold text-slate-700">No. WhatsApp / HP</label>
            <input type="text" wire:model="phone" class="form-input text-sm" placeholder="08xxxxxxxxxx">
            @error('phone') <p class="form-error">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="form-label text-xs font-semibold text-slate-700">Role / Jabatan</label>
            <select wire:model="role" class="form-input text-sm">
                <option value="KASIR">Kasir</option>
                <option value="OPERATOR_CUCI">Operator Cuci</option>
                <option value="SETRIKA">Operator Setrika</option>
                <option value="KURIR">Kurir Antar-Jemput</option>
            </select>
        </div>

        <div>
            <label class="form-label text-xs font-semibold text-slate-700">Gaji Pokok Bulanan</label>
            <div class="relative">
                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs font-medium text-slate-400">Rp</span>
                <input type="number" wire:model="base_salary" class="form-input pl-9 text-sm font-semibold" placeholder="1800000">
            </div>
            @error('base_salary') <p class="form-error">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="form-label text-xs font-semibold text-slate-700">Uang Makan / Hari</label>
            <div class="relative">
                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs font-medium text-slate-400">Rp</span>
                <input type="number" wire:model="daily_allowance" class="form-input pl-9 text-sm font-semibold" placeholder="25000">
            </div>
        </div>

        <div class="sm:col-span-2">
            <label class="form-label text-xs font-semibold text-slate-700">Tarif Komisi Produksi (Rp / Kg atau per Trip)</label>
            <div class="relative">
                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs font-medium text-slate-400">Rp</span>
                <input type="number" wire:model="commission_rate" class="form-input pl-9 text-sm font-semibold" placeholder="500">
            </div>
            <p class="text-[11px] text-slate-400 mt-1">Dihitung otomatis berdasarkan total Kg cucian yang dikerjakan atau diantar.</p>
            @error('commission_rate') <p class="form-error">{{ $message }}</p> @enderror
        </div>
    </div>

    {{-- Status Toggle --}}
    <div class="flex items-center justify-between py-2 border-t border-slate-100">
        <div>
            <p class="text-sm font-bold text-slate-800">Status Karyawan Aktif</p>
            <p class="text-xs text-slate-400 mt-0.5">Karyawan aktif muncul di daftar operator Kanban &amp; Kurir</p>
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

    {{-- Action Button (Full Width Single Primary Button) --}}
    <div class="pt-2 border-t border-slate-100">
        <button type="button" wire:click="saveEmployee" wire:loading.attr="disabled" wire:target="saveEmployee"
                class="btn-primary w-full justify-center py-3 text-sm font-bold shadow-md">
            <span wire:loading.remove wire:target="saveEmployee">{{ $editingId ? 'Simpan Perubahan Karyawan' : 'Tambahkan Karyawan' }}</span>
            <span wire:loading wire:target="saveEmployee" class="flex items-center gap-2">
                <svg class="size-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                Menyimpan…
            </span>
        </button>
    </div>
</div>
