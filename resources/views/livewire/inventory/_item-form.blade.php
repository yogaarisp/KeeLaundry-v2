<div class="p-5 space-y-4">
    {{-- Header --}}
    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
        <div class="flex items-center gap-2">
            <button type="button" wire:click="$set('showItemForm', false)" class="md:hidden p-1.5 -ml-1 text-slate-400 hover:text-slate-600 rounded-lg hover:bg-slate-100 transition-colors">
                <svg class="size-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/></svg>
            </button>
            <h3 class="font-bold text-slate-800 text-base">{{ $editingItemId ? 'Edit Item Inventaris' : 'Tambah Item Baru' }}</h3>
        </div>
        <button type="button" wire:click="$set('showItemForm', false)" class="hidden md:flex p-1.5 text-slate-400 hover:text-slate-600 rounded-lg hover:bg-slate-100 transition-colors">
            <svg class="size-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
        </button>
    </div>

    {{-- Form Fields --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
        <div>
            <label class="form-label text-xs font-semibold text-slate-700">SKU / Kode Item</label>
            <input type="text" wire:model="sku" class="form-input font-mono uppercase text-sm" placeholder="CHM-DET-001">
            @error('sku') <p class="form-error">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="form-label text-xs font-semibold text-slate-700">Kategori</label>
            <select wire:model.live="category" class="form-input text-sm">
                <option value="CHEMICAL">Chemical (Sabun/Softener)</option>
                <option value="PARFUM">Parfum</option>
                <option value="PACKAGING">Packaging</option>
                <option value="GAS">Gas LPG</option>
            </select>
        </div>

        <div class="sm:col-span-2">
            <label class="form-label text-xs font-semibold text-slate-700">Nama Item</label>
            <input type="text" wire:model="itemName" class="form-input text-sm" placeholder="cth. Deterjen Cair Attack Pro">
            @error('itemName') <p class="form-error">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="form-label text-xs font-semibold text-slate-700">Satuan</label>
            <select wire:model="unit" class="form-input text-sm">
                <option value="ml">ml</option>
                <option value="Pcs">Pcs</option>
                <option value="Tabung">Tabung</option>
                <option value="Liter">Liter</option>
            </select>
        </div>

        @if (!$editingItemId)
        <div>
            <label class="form-label text-xs font-semibold text-slate-700">Stok Awal</label>
            <div class="relative">
                <input type="number" step="0.1" wire:model="system_stock" class="form-input pr-10 text-sm">
                <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-slate-400 font-medium">{{ $unit }}</span>
            </div>
            @error('system_stock') <p class="form-error">{{ $message }}</p> @enderror
        </div>
        @endif

        <div>
            <label class="form-label text-xs font-semibold text-slate-700">Stok Minimum Alert</label>
            <div class="relative">
                <input type="number" step="100" wire:model="min_threshold" class="form-input pr-10 text-sm">
                <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-slate-400 font-medium">{{ $unit }}</span>
            </div>
            @error('min_threshold') <p class="form-error">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="form-label text-xs font-semibold text-slate-700">Harga Modal / {{ $unit }}</label>
            <div class="relative">
                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs font-medium text-slate-400">Rp</span>
                <input type="number" step="0.01" wire:model="cost_per_unit" class="form-input pl-9 text-sm">
            </div>
            @error('cost_per_unit') <p class="form-error">{{ $message }}</p> @enderror
        </div>

        <div class="sm:col-span-2">
            <label class="form-label text-xs font-semibold text-slate-700">Pemakaian Standar / Kg (BOM)</label>
            <div class="relative">
                <input type="number" step="0.5" wire:model="usage_per_kg" class="form-input pr-10 text-sm">
                <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-slate-400 font-medium">{{ $unit }}</span>
            </div>
            <p class="text-[11px] text-slate-400 mt-1">Dipotong otomatis dari stok setiap order laundry selesai diproses.</p>
        </div>
    </div>

    {{-- Actions --}}
    <div class="pt-3 border-t border-slate-100">
        <button type="button" wire:click="saveItem" wire:loading.attr="disabled" wire:target="saveItem"
                class="btn-primary w-full justify-center py-3 text-sm font-bold shadow-md">
            <span wire:loading.remove wire:target="saveItem">{{ $editingItemId ? 'Simpan Perubahan' : 'Tambahkan Item Inventaris' }}</span>
            <span wire:loading wire:target="saveItem" class="flex items-center gap-2">
                <svg class="size-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                Menyimpan…
            </span>
        </button>
    </div>
</div>
