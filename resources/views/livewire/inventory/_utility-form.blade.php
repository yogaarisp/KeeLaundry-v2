<div class="p-5 space-y-4">
    <div class="flex items-center justify-between">
        <h3 class="font-bold text-slate-800">{{ $editingUtilityId ? 'Edit Log Utilitas' : 'Catat Utilitas Baru' }}</h3>
        <button wire:click="$set('showUtilityForm', false)" class="btn-icon"><svg class="size-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg></button>
    </div>
    <div class="grid grid-cols-2 gap-3">
        <div>
            <label class="form-label">Jenis Utilitas</label>
            <select wire:model.live="utilityType" class="form-input">
                <option value="LISTRIK_PLN">⚡ Listrik PLN</option>
                <option value="AIR_PDAM">💧 Air PDAM</option>
                <option value="GAS_LPG">🔥 Gas LPG</option>
            </select>
        </div>
        <div>
            <label class="form-label">Periode</label>
            <input type="date" wire:model="periodDate" class="form-input">
            @error('periodDate') <p class="form-error">{{ $message }}</p> @enderror
        </div>
        @if ($utilityType !== 'GAS_LPG')
        <div>
            <label class="form-label">Meteran Awal ({{ $utilityUnit }})</label>
            <input type="number" step="0.01" wire:model.live="meterStart" class="form-input">
        </div>
        <div>
            <label class="form-label">Meteran Akhir ({{ $utilityUnit }})</label>
            <input type="number" step="0.01" wire:model.live="meterEnd" class="form-input">
        </div>
        @endif
        <div @class(['col-span-2' => $utilityType === 'GAS_LPG'])>
            <label class="form-label">Total Pemakaian ({{ $utilityUnit }})</label>
            <div class="relative">
                <input type="number" step="0.01" wire:model.live="usageVolume" class="form-input pr-14">
                <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-slate-400">{{ $utilityUnit }}</span>
            </div>
            @error('usageVolume') <p class="form-error">{{ $message }}</p> @enderror
        </div>
        <div class="col-span-2">
            <label class="form-label">Total Biaya (Rp)</label>
            <div class="relative">
                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm text-slate-400">Rp</span>
                <input type="number" wire:model.live="totalCost" class="form-input pl-9">
            </div>
            @error('totalCost') <p class="form-error">{{ $message }}</p> @enderror
            @if ($usageVolume > 0 && $totalCost > 0)
            <p class="text-xs text-sky-600 mt-1">Biaya per {{ $utilityUnit }}: <strong>Rp {{ number_format(round($totalCost / $usageVolume, 0)) }}</strong></p>
            @endif
        </div>
        <div>
            <label class="form-label">Dicatat oleh</label>
            <input type="text" wire:model="recordedBy" class="form-input" placeholder="Admin">
            @error('recordedBy') <p class="form-error">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="form-label">Catatan (opsional)</label>
            <input type="text" wire:model="utilityNotes" class="form-input" placeholder="cth. Token listrik PLN">
        </div>
    </div>
    <div class="flex gap-3 pt-2 border-t border-slate-100">
        <button wire:click="saveUtility" wire:loading.attr="disabled" class="btn-primary flex-1">
            <span wire:loading.remove wire:target="saveUtility">{{ $editingUtilityId ? 'Simpan Perubahan' : 'Catat Log' }}</span>
            <span wire:loading wire:target="saveUtility">Menyimpan…</span>
        </button>
        <button wire:click="$set('showUtilityForm', false)" class="btn-secondary">Batal</button>
    </div>
</div>
