<div class="p-5 space-y-4">
    <div class="flex items-center justify-between">
        <h3 class="font-bold text-slate-800">{{ $editingId ? 'Edit Pelanggan' : 'Tambah Pelanggan Baru' }}</h3>
        <button wire:click="closeForm" class="btn-icon"><svg class="size-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg></button>
    </div>
    <div class="grid grid-cols-2 gap-3">
        <div class="col-span-2">
            <label class="form-label">Nama Lengkap</label>
            <input type="text" wire:model="name" class="form-input" placeholder="cth. Dewi Kusuma">
            @error('name') <p class="form-error">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="form-label">No. HP / WhatsApp</label>
            <input type="text" wire:model="phone" class="form-input" placeholder="08xxxxxxxxxx">
            @error('phone') <p class="form-error">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="form-label">Tier Member</label>
            <select wire:model="tier" class="form-input">
                <option value="REGULAR">Regular</option>
                <option value="SILVER">Silver</option>
                <option value="VIP">VIP</option>
            </select>
        </div>
        <div class="col-span-2">
            <label class="form-label">Alamat Jemput (opsional)</label>
            <input type="text" wire:model="address" class="form-input" placeholder="Jl. ...">
        </div>
        <div class="col-span-2">
            <label class="form-label">Preferensi Cucian (opsional)</label>
            <textarea wire:model="preferences" rows="2" class="form-input resize-none" placeholder="cth. Parfum Sakura, jangan dilipat…"></textarea>
        </div>
    </div>
    <div class="flex gap-3 pt-2 border-t border-slate-100">
        <button wire:click="saveCustomer" wire:loading.attr="disabled" class="btn-primary flex-1">
            <span wire:loading.remove wire:target="saveCustomer">{{ $editingId ? 'Simpan Perubahan' : 'Daftarkan' }}</span>
            <span wire:loading wire:target="saveCustomer">Menyimpan…</span>
        </button>
        <button wire:click="closeForm" class="btn-secondary">Batal</button>
    </div>
</div>
