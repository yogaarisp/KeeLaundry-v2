<div class="p-4 sm:p-6 space-y-4">
    {{-- Header Bar --}}
    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
        <div class="flex items-center gap-2">
            <button type="button"
                    wire:click="closeForm"
                    class="p-1.5 -ml-1 text-slate-600 hover:text-slate-900 hover:bg-slate-100 rounded-xl transition-all md:hidden"
                    title="Kembali">
                <svg class="size-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/></svg>
            </button>
            <div>
                <h3 class="font-bold text-slate-800 text-base sm:text-lg leading-tight">
                    {{ $editingId ? 'Edit Tugas Antar-Jemput' : 'Tugas Antar-Jemput Baru' }}
                </h3>
                <p class="text-xs text-slate-400 hidden sm:block mt-0.5">Jadwalkan penjemputan cucian kotor atau antar cucian bersih</p>
            </div>
        </div>
        <button type="button" wire:click="closeForm" class="btn-icon hidden md:flex" title="Tutup">
            <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
        </button>
    </div>

    {{-- Type Selection: Jemput vs Antar --}}
    <div>
        <label class="form-label mb-1.5">Jenis Layanan</label>
        <div class="grid grid-cols-2 gap-1.5 bg-slate-100 p-1 rounded-xl">
            <button type="button"
                    wire:click="$set('taskType', 'JEMPUT')"
                    @class([
                        'flex items-center justify-center gap-2 py-2 px-3 rounded-lg text-xs sm:text-sm font-medium transition-all',
                        'bg-white text-slate-900 font-semibold shadow-xs' => $taskType === 'JEMPUT',
                        'text-slate-600 hover:text-slate-900' => $taskType !== 'JEMPUT',
                    ])>
                <span>🛵</span>
                <span>Jemput Cucian</span>
            </button>
            <button type="button"
                    wire:click="$set('taskType', 'ANTAR')"
                    @class([
                        'flex items-center justify-center gap-2 py-2 px-3 rounded-lg text-xs sm:text-sm font-medium transition-all',
                        'bg-white text-slate-900 font-semibold shadow-xs' => $taskType === 'ANTAR',
                        'text-slate-600 hover:text-slate-900' => $taskType !== 'ANTAR',
                    ])>
                <span>📦</span>
                <span>Antar Bersih</span>
            </button>
        </div>
    </div>

    {{-- Quick Autofill based on type --}}
    @if ($taskType === 'ANTAR')
        <div>
            <label class="form-label">Pilih Nota Siap Antar (Opsional)</label>
            <select wire:change="onOrderSelected($event.target.value)" class="form-input text-xs sm:text-sm">
                <option value="">-- Pilih dari order siap diambil / packing --</option>
                @foreach ($this->readyOrders as $ro)
                    <option value="{{ $ro->id }}" @selected($orderId === $ro->id)>
                        {{ $ro->invoice_number }} — {{ $ro->customer?->name }} ({{ $ro->quantity }} {{ $ro->unit }}) - Rp {{ number_format($ro->total_price) }}
                    </option>
                @endforeach
            </select>
        </div>
    @else
        <div>
            <label class="form-label">Pilih Pelanggan Terdaftar (Opsional)</label>
            <select wire:change="onCustomerSelected($event.target.value)" class="form-input text-xs sm:text-sm">
                <option value="">-- Cari atau pilih data pelanggan --</option>
                @foreach ($this->customers as $c)
                    <option value="{{ $c->id }}" @selected($customerId === $c->id)>
                        {{ $c->name }} ({{ $c->phone }})
                    </option>
                @endforeach
            </select>
        </div>
    @endif

    {{-- Customer Details --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
        <div>
            <label class="form-label">Nama Pelanggan <span class="text-rose-500">*</span></label>
            <input type="text" wire:model="customerName" class="form-input" placeholder="Nama Pelanggan">
            @error('customerName') <p class="form-error">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="form-label">No. WhatsApp <span class="text-rose-500">*</span></label>
            <input type="text" wire:model="customerPhone" class="form-input" placeholder="08xxxxxxxxxx">
            @error('customerPhone') <p class="form-error">{{ $message }}</p> @enderror
        </div>
    </div>

    {{-- Address & Maps --}}
    <div class="space-y-2">
        <div>
            <label class="form-label">Alamat Lengkap <span class="text-rose-500">*</span></label>
            <textarea wire:model="address" rows="2" class="form-input resize-none" placeholder="Nama jalan, nomor rumah, RT/RW, patokan lokasi..."></textarea>
            @error('address') <p class="form-error">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="form-label">Link Google Maps / Shareloc (Opsional)</label>
            <input type="url" wire:model="mapsUrl" class="form-input text-xs" placeholder="https://maps.app.goo.gl/...">
        </div>
    </div>

    {{-- Schedule & Courier --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
        <div>
            <label class="form-label">Tanggal <span class="text-rose-500">*</span></label>
            <input type="date" wire:model="scheduledDate" class="form-input">
            @error('scheduledDate') <p class="form-error">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="form-label">Waktu / Shift</label>
            <select wire:model="timeSlot" class="form-input">
                <option value="PAGI">Pagi (08:00 - 11:00)</option>
                <option value="SIANG">Siang (11:00 - 14:00)</option>
                <option value="SORE">Sore (14:00 - 17:00)</option>
                <option value="MALAM">Malam (17:00 - 20:00)</option>
            </select>
            @error('timeSlot') <p class="form-error">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="form-label">Petugas Kurir</label>
            <select wire:model="courierId" class="form-input">
                <option value="">-- Pilih Kurir --</option>
                @foreach ($this->couriers as $courier)
                    <option value="{{ $courier->id }}">{{ $courier->name }}</option>
                @endforeach
                @if ($this->couriers->isEmpty())
                    @foreach ($this->allStaff as $staff)
                        <option value="{{ $staff->id }}">{{ $staff->name }} ({{ $staff->role }})</option>
                    @endforeach
                @endif
            </select>
        </div>
    </div>

    {{-- Fees & Est Kg --}}
    <div class="grid grid-cols-2 gap-3">
        <div>
            <label class="form-label">Ongkos Kirim / Jemput (Rp)</label>
            <input type="number" wire:model="deliveryFee" min="0" step="1000" class="form-input" placeholder="0">
        </div>
        <div>
            <label class="form-label">Estimasi Berat / Qty</label>
            <input type="number" wire:model="estimatedKg" step="0.1" min="0" class="form-input" placeholder="cth. 3.5">
        </div>
    </div>

    {{-- Notes --}}
    <div>
        <label class="form-label">Catatan Tambahan</label>
        <input type="text" wire:model="notes" class="form-input text-xs sm:text-sm" placeholder="cth. Taruh di teras bila tidak ada orang...">
    </div>

    {{-- Full-width Action Button (Anti-AI SLOP Standard) --}}
    <div class="pt-2 border-t border-slate-100">
        <button type="button" wire:click="save" wire:loading.attr="disabled" class="btn-primary w-full py-2.5 sm:py-3 justify-center text-sm font-semibold shadow-sm">
            <span wire:loading.remove wire:target="save">{{ $editingId ? 'Simpan Perubahan' : 'Jadwalkan Tugas' }}</span>
            <span wire:loading wire:target="save" class="inline-flex items-center gap-2">
                <svg class="animate-spin size-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                <span>Menyimpan…</span>
            </span>
        </button>
    </div>
</div>
