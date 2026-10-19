<div class="p-5 space-y-4">
    <div class="flex items-center justify-between">
        <h3 class="font-bold text-slate-800">Edit Order</h3>
        <button wire:click="$set('showEditOrder', false)" class="btn-icon">
            <svg class="size-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
        </button>
    </div>

    <div class="grid grid-cols-2 gap-3">
        <div>
            <label class="form-label">Berat / Jumlah</label>
            <input type="number" step="0.5" min="0.1" wire:model="editQuantity" class="form-input">
            @error('editQuantity') <p class="form-error">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="form-label">Paket Layanan</label>
            <select wire:model="editPackageId" class="form-input">
                @foreach ($this->activePackages as $pkg)
                    <option value="{{ $pkg->id }}">{{ $pkg->name }}</option>
                @endforeach
            </select>
            @error('editPackageId') <p class="form-error">{{ $message }}</p> @enderror
        </div>
    </div>

    <div class="grid grid-cols-2 gap-3">
        <div>
            <label class="form-label">Status Bayar</label>
            <select wire:model="editPaymentStatus" class="form-input">
                <option value="BELUM_LUNAS">Belum Lunas</option>
                <option value="LUNAS">Lunas</option>
            </select>
        </div>
        <div>
            <label class="form-label">Metode Bayar</label>
            <select wire:model="editPaymentMethod" class="form-input">
                <option value="TUNAI">Tunai</option>
                <option value="QRIS">QRIS</option>
                <option value="TRANSFER">Transfer</option>
                <option value="DEPOSIT_KG">Deposit Kg</option>
            </select>
        </div>
    </div>

    <div>
        <label class="form-label">Catatan</label>
        <textarea wire:model="editNotes" rows="2" class="form-input resize-none"></textarea>
    </div>

    <div class="flex gap-3 pt-2 border-t border-slate-100">
        <button wire:click="saveEditOrder" wire:loading.attr="disabled" class="btn-primary flex-1">
            <span wire:loading.remove wire:target="saveEditOrder">Simpan Perubahan</span>
            <span wire:loading wire:target="saveEditOrder">Menyimpan…</span>
        </button>
        <button wire:click="$set('showEditOrder', false)" class="btn-secondary">Batal</button>
    </div>
</div>
