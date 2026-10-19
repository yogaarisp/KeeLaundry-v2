<div class="p-5 space-y-4">
    <div class="flex items-center justify-between">
        <h3 class="font-bold text-slate-800">{{ $editingId ? 'Edit Karyawan' : 'Tambah Karyawan Baru' }}</h3>
        <button wire:click="$set('showForm', false)" class="btn-icon"><svg class="size-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg></button>
    </div>
    <div class="grid grid-cols-2 gap-3">
        <div class="col-span-2">
            <label class="form-label">Nama Lengkap</label>
            <input type="text" wire:model="name" class="form-input" placeholder="cth. Budi Santoso">
            @error('name') <p class="form-error">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="form-label">No. HP</label>
            <input type="text" wire:model="phone" class="form-input" placeholder="08xxxxxxxxxx">
            @error('phone') <p class="form-error">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="form-label">Role / Jabatan</label>
            <select wire:model="role" class="form-input">
                <option value="KASIR">Kasir</option>
                <option value="OPERATOR_CUCI">Operator Cuci</option>
                <option value="SETRIKA">Setrika</option>
                <option value="KURIR">Kurir</option>
            </select>
        </div>
        <div>
            <label class="form-label">Gaji Pokok</label>
            <div class="relative"><span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm text-slate-400">Rp</span><input type="number" wire:model="base_salary" class="form-input pl-9" placeholder="1800000"></div>
            @error('base_salary') <p class="form-error">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="form-label">Uang Makan / Hari</label>
            <div class="relative"><span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm text-slate-400">Rp</span><input type="number" wire:model="daily_allowance" class="form-input pl-9" placeholder="25000"></div>
        </div>
        <div class="col-span-2">
            <label class="form-label">Tarif Komisi (Rp / Kg atau per Trip)</label>
            <div class="relative"><span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm text-slate-400">Rp</span><input type="number" wire:model="commission_rate" class="form-input pl-9" placeholder="500"></div>
            @error('commission_rate') <p class="form-error">{{ $message }}</p> @enderror
        </div>
    </div>
    <div class="flex items-center justify-between py-2">
        <div><p class="text-sm font-semibold text-slate-700">Karyawan Aktif</p><p class="text-xs text-slate-400">Tampil di daftar klaim komisi Kanban</p></div>
        <button type="button" wire:click="$toggle('is_active')" @class(['relative inline-flex h-6 w-11 items-center rounded-full transition-colors duration-200 focus:outline-none', 'bg-sky-600' => $is_active, 'bg-slate-200' => !$is_active])>
            <span @class(['inline-block size-4 rounded-full bg-white shadow transform transition-transform duration-200', 'translate-x-6' => $is_active, 'translate-x-1' => !$is_active])></span>
        </button>
    </div>
    <div class="flex gap-3 pt-2 border-t border-slate-100">
        <button wire:click="saveEmployee" wire:loading.attr="disabled" class="btn-primary flex-1">
            <span wire:loading.remove wire:target="saveEmployee">{{ $editingId ? 'Simpan Perubahan' : 'Tambahkan' }}</span>
            <span wire:loading wire:target="saveEmployee">Menyimpan…</span>
        </button>
        <button wire:click="$set('showForm', false)" class="btn-secondary">Batal</button>
    </div>
</div>
