<div class="p-5 space-y-4">
    <div class="flex items-center justify-between">
        <h3 class="font-bold text-slate-800">{{ $editingComplaintId ? 'Edit Tiket Komplain' : 'Buat Tiket Komplain' }}</h3>
        <button wire:click="$set('showComplaintForm', false)" class="btn-icon"><svg class="size-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg></button>
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
    <div class="flex gap-3 pt-2 border-t border-slate-100">
        <button wire:click="saveComplaint" wire:loading.attr="disabled" class="btn-primary flex-1">
            <span wire:loading.remove wire:target="saveComplaint">Simpan Tiket</span>
            <span wire:loading wire:target="saveComplaint">Menyimpan…</span>
        </button>
        <button wire:click="$set('showComplaintForm', false)" class="btn-secondary">Batal</button>
    </div>
</div>
