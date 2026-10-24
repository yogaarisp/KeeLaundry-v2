<div class="p-5 space-y-4">
    {{-- Header --}}
    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
        <div class="flex items-center gap-2">
            <button type="button" wire:click="$set('showUtilityForm', false)" class="md:hidden p-1.5 -ml-1 text-slate-400 hover:text-slate-600 rounded-lg hover:bg-slate-100 transition-colors">
                <svg class="size-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/></svg>
            </button>
            <h3 class="font-bold text-slate-800 text-base">{{ $editingUtilityId ? 'Edit Log Utilitas' : 'Catat Utilitas Baru' }}</h3>
        </div>
        <button type="button" wire:click="$set('showUtilityForm', false)" class="hidden md:flex p-1.5 text-slate-400 hover:text-slate-600 rounded-lg hover:bg-slate-100 transition-colors">
            <svg class="size-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
        </button>
    </div>

    {{-- Form Fields --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
        <div>
            <label class="form-label text-xs font-semibold text-slate-700">Jenis Utilitas</label>
            <select wire:model.live="utilityType" class="form-input text-sm">
                <option value="LISTRIK_PLN">⚡ Listrik PLN</option>
                <option value="AIR_PDAM">💧 Air PDAM</option>
                <option value="GAS_LPG">🔥 Gas LPG</option>
            </select>
        </div>

        <div>
            <label class="form-label text-xs font-semibold text-slate-700">Periode Tagihan</label>
            <input type="date" wire:model="periodDate" class="form-input text-sm">
            @error('periodDate') <p class="form-error">{{ $message }}</p> @enderror
        </div>

        @if ($utilityType !== 'GAS_LPG')
        <div>
            <label class="form-label text-xs font-semibold text-slate-700">Meteran Awal ({{ $utilityUnit }})</label>
            <input type="number" step="0.01" wire:model.live="meterStart" class="form-input text-sm">
        </div>

        <div>
            <label class="form-label text-xs font-semibold text-slate-700">Meteran Akhir ({{ $utilityUnit }})</label>
            <input type="number" step="0.01" wire:model.live="meterEnd" class="form-input text-sm">
        </div>
        @endif

        <div @class(['sm:col-span-2' => $utilityType === 'GAS_LPG'])>
            <label class="form-label text-xs font-semibold text-slate-700">Total Pemakaian ({{ $utilityUnit }})</label>
            <div class="relative">
                <input type="number" step="0.01" wire:model.live="usageVolume" class="form-input pr-14 text-sm">
                <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-slate-400 font-medium">{{ $utilityUnit }}</span>
            </div>
            @error('usageVolume') <p class="form-error">{{ $message }}</p> @enderror
        </div>

        <div class="sm:col-span-2">
            <label class="form-label text-xs font-semibold text-slate-700">Total Biaya (Rp)</label>
            <div class="relative">
                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs font-medium text-slate-400">Rp</span>
                <input type="number" wire:model.live="totalCost" class="form-input pl-9 text-sm">
            </div>
            @error('totalCost') <p class="form-error">{{ $message }}</p> @enderror
            @if ($usageVolume > 0 && $totalCost > 0)
            <div class="flex items-center gap-1.5 mt-2 bg-sky-50 border border-sky-100 rounded-xl px-3 py-2 text-xs text-sky-700">
                <svg class="size-4 text-sky-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                <span>Tarif rata-rata: <strong>Rp {{ number_format(round($totalCost / $usageVolume, 0)) }} / {{ $utilityUnit }}</strong></span>
            </div>
            @endif
        </div>

        <div>
            <label class="form-label text-xs font-semibold text-slate-700">Dicatat oleh</label>
            <input type="text" wire:model="recordedBy" class="form-input text-sm" placeholder="Admin">
            @error('recordedBy') <p class="form-error">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="form-label text-xs font-semibold text-slate-700">Catatan (opsional)</label>
            <input type="text" wire:model="utilityNotes" class="form-input text-sm" placeholder="cth. Token listrik PLN">
        </div>
    </div>

    {{-- Actions --}}
    <div class="pt-3 border-t border-slate-100">
        <button type="button" wire:click="saveUtility" wire:loading.attr="disabled" wire:target="saveUtility"
                class="btn-primary w-full justify-center py-3 text-sm font-bold shadow-md">
            <span wire:loading.remove wire:target="saveUtility">{{ $editingUtilityId ? 'Simpan Perubahan Log' : 'Catat Log Utilitas' }}</span>
            <span wire:loading wire:target="saveUtility" class="flex items-center gap-2">
                <svg class="size-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                Menyimpan…
            </span>
        </button>
    </div>
</div>
