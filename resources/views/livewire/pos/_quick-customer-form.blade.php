<div class="p-5 space-y-4">
    <div class="flex items-center justify-between">
        <h3 class="font-bold text-slate-800">Pelanggan Baru</h3>
        <button wire:click="$set('showQuickCustomer', false)" class="btn-icon">
            <svg class="size-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
        </button>
    </div>
    <div>
        <label class="form-label">Nama Pelanggan</label>
        <input type="text" wire:model="quickCustomerName" placeholder="cth. Dewi Kusuma" class="form-input">
        @error('quickCustomerName') <p class="form-error">{{ $message }}</p> @enderror
    </div>
    <div>
        <label class="form-label">Nomor HP / WhatsApp</label>
        <input type="text" wire:model="quickCustomerPhone" placeholder="08xxxxxxxxxx" class="form-input">
        @error('quickCustomerPhone') <p class="form-error">{{ $message }}</p> @enderror
    </div>
    <div class="flex gap-3">
        <button wire:click="saveQuickCustomer" class="btn-primary flex-1">Daftar & Pilih</button>
        <button wire:click="$set('showQuickCustomer', false)" class="btn-secondary">Batal</button>
    </div>
</div>
