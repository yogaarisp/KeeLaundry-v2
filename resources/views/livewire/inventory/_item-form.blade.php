<div class="p-5 space-y-4">
    <div class="flex items-center justify-between">
        <h3 class="font-bold text-slate-800">{{ $editingItemId ? 'Edit Item Inventaris' : 'Tambah Item Baru' }}</h3>
        <button wire:click="$set('showItemForm', false)" class="btn-icon"><svg class="size-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg></button>
    </div>
    <div class="grid grid-cols-2 gap-3">
        <div>
            <label class="form-label">SKU / Kode Item</label>
            <input type="text" wire:model="sku" class="form-input font-mono uppercase" placeholder="CHM-DET-001">
            @error('sku') <p class="form-error">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="form-label">Kategori</label>
            <select wire:model.live="category" class="form-input">
                <option value="CHEMICAL">Chemical (Sabun/Softener)</option>
                <option value="PARFUM">Parfum</option>
                <option value="PACKAGING">Packaging</option>
                <option value="GAS">Gas LPG</option>
            </select>
        </div>
        <div class="col-span-2">
            <label class="form-label">Nama Item</label>
            <input type="text" wire:model="itemName" class="form-input" placeholder="cth. Deterjen Cair Attack Pro">
            @error('itemName') <p class="form-error">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="form-label">Satuan</label>
            <select wire:model="unit" class="form-input">
                <option value="ml">ml</option>
                <option value="Pcs">Pcs</option>
                <option value="Tabung">Tabung</option>
                <option value="Liter">Liter</option>
            </select>
        </div>
        @if (!$editingItemId)
        <div>
            <label class="form-label">Stok Awal</label>
            <div class="relative"><input type="number" step="0.1" wire:model="system_stock" class="form-input pr-8"><span class="absolute right-2.5 top-1/2 -translate-y-1/2 text-xs text-slate-400">{{ $unit }}</span></div>
        </div>
        @endif
        <div>
            <label class="form-label">Stok Minimum Alert</label>
            <div class="relative"><input type="number" step="100" wire:model="min_threshold" class="form-input pr-8"><span class="absolute right-2.5 top-1/2 -translate-y-1/2 text-xs text-slate-400">{{ $unit }}</span></div>
            @error('min_threshold') <p class="form-error">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="form-label">Harga Modal / {{ $unit }}</label>
            <div class="relative"><span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm text-slate-400">Rp</span><input type="number" step="0.01" wire:model="cost_per_unit" class="form-input pl-9"></div>
            @error('cost_per_unit') <p class="form-error">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="form-label">Pemakaian / Kg (BOM)</label>
            <div class="relative"><input type="number" step="0.5" wire:model="usage_per_kg" class="form-input pr-8"><span class="absolute right-2.5 top-1/2 -translate-y-1/2 text-xs text-slate-400">{{ $unit }}</span></div>
        </div>
    </div>
    <div class="flex gap-3 pt-2 border-t border-slate-100">
        <button wire:click="saveItem" wire:loading.attr="disabled" class="btn-primary flex-1">
            <span wire:loading.remove wire:target="saveItem">{{ $editingItemId ? 'Simpan' : 'Tambahkan' }}</span>
            <span wire:loading wire:target="saveItem">Menyimpan…</span>
        </button>
        <button wire:click="$set('showItemForm', false)" class="btn-secondary">Batal</button>
    </div>
</div>
