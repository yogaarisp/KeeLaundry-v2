<div class="p-4 sm:p-5 space-y-4">
    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
        <div class="flex items-center gap-2">
            <button type="button"
                    wire:click="$set('showComplaintForm', false)"
                    class="p-2 -ml-1 text-slate-600 hover:text-slate-900 hover:bg-slate-100 rounded-xl transition-all active:scale-95 flex items-center justify-center shrink-0 md:hidden"
                    title="Kembali">
                <svg class="size-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/></svg>
            </button>
            <h3 class="font-bold text-slate-800 text-base sm:text-lg">{{ $editingComplaintId ? 'Edit Tiket Komplain' : 'Buat Tiket Komplain' }}</h3>
        </div>
        <button wire:click="$set('showComplaintForm', false)" class="btn-icon hidden md:flex" title="Tutup">
            <svg class="size-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
        </button>
    </div>

    <div>
        <label class="form-label">Pelanggan</label>
        <select wire:model="complaintCustomerId" class="form-input">
            <option value="">— Pilih Pelanggan —</option>
            @foreach (\App\Models\Customer::orderBy('name')->get(['id','name']) as $c)
                <option value="{{ $c->id }}">{{ $c->name }}</option>
            @endforeach
        </select>
        @error('complaintCustomerId') <p class="form-error">{{ $message }}</p> @enderror
    </div>
    <div>
        <label class="form-label">Nomor Invoice Terkait</label>
        <input type="text" wire:model="complaintInvoice" class="form-input font-mono" placeholder="INV-YYYYMMDD-XXXX">
        @error('complaintInvoice') <p class="form-error">{{ $message }}</p> @enderror
    </div>
    <div class="grid grid-cols-2 gap-3">
        <div>
            <label class="form-label">Jenis Masalah</label>
            <select wire:model="issueType" class="form-input">
                <option value="">— Pilih —</option>
                <option>Pakaian Rusak / Luntur</option>
                <option>Barang Kurang / Hilang</option>
                <option>Parfum Tidak Sesuai</option>
                <option>Cucian Tidak Bersih</option>
                <option>Lain-lain</option>
            </select>
            @error('issueType') <p class="form-error">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="form-label">Status</label>
            <select wire:model="complaintStatus" class="form-input">
                <option value="OPEN">Open</option>
                <option value="IN_PROGRESS">In Progress</option>
                <option value="RESOLVED">Resolved</option>
            </select>
        </div>
    </div>
    <div>
        <label class="form-label">Deskripsi Masalah</label>
        <textarea wire:model="description" rows="3" class="form-input resize-none" placeholder="Jelaskan detail masalah…"></textarea>
        @error('description') <p class="form-error">{{ $message }}</p> @enderror
    </div>
    <div>
        <label class="form-label">Kompensasi / Solusi (opsional)</label>
        <input type="text" wire:model="compensation" class="form-input" placeholder="cth. Gratis cuci 3 Kg berikutnya">
    </div>
    <div class="pt-2 border-t border-slate-100">
        <button wire:click="saveComplaint" wire:loading.attr="disabled" class="btn-primary w-full py-2.5 sm:py-3 justify-center text-sm font-semibold shadow-sm">
            <span wire:loading.remove wire:target="saveComplaint">{{ $editingComplaintId ? 'Simpan Perubahan' : 'Simpan Tiket' }}</span>
            <span wire:loading wire:target="saveComplaint" class="inline-flex items-center gap-2">
                <svg class="animate-spin size-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                <span>Menyimpan…</span>
            </span>
        </button>
    </div>
</div>
