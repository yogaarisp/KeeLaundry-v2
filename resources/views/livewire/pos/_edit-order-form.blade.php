<div class="p-4 sm:p-5 space-y-4">
    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
        <div class="flex items-center gap-2">
            <button type="button"
                    wire:click="$set('showEditOrder', false)"
                    class="p-1.5 -ml-1 text-slate-600 hover:text-slate-900 hover:bg-slate-100 rounded-xl transition-all md:hidden"
                    title="Kembali">
                <svg class="size-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/></svg>
            </button>
            <h3 class="font-bold text-slate-800 text-base sm:text-lg">Edit Order</h3>
        </div>
        <button type="button" wire:click="$set('showEditOrder', false)" class="btn-icon hidden md:flex" title="Tutup">
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

    <div class="pt-2 border-t border-slate-100">
        <button wire:click="saveEditOrder" wire:loading.attr="disabled" class="btn-primary w-full py-2.5 sm:py-3 justify-center text-sm font-semibold shadow-sm">
            <span wire:loading.remove wire:target="saveEditOrder">Simpan Perubahan</span>
            <span wire:loading wire:target="saveEditOrder" class="inline-flex items-center gap-2">
                <svg class="animate-spin size-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                <span>Menyimpan…</span>
            </span>
        </button>
    </div>
</div>
