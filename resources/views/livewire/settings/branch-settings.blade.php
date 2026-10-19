<div class="space-y-5 max-w-3xl">

{{-- ══ HEADER ══════════════════════════════════════════════════════ --}}
<div class="flex items-start gap-4">
    <div class="flex items-center justify-center size-14 rounded-2xl bg-sky-600 text-white shrink-0 shadow-sm shadow-sky-600/30">
        <svg class="size-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 0 1 .75-.75h3a.75.75 0 0 1 .75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349M3.75 21V9.349m0 0a3.001 3.001 0 0 0 3.75-.615A2.993 2.993 0 0 0 9.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 0 0 2.25 1.016c.896 0 1.7-.393 2.25-1.015a3.001 3.001 0 0 0 3.75.614m-16.5 0a3.004 3.004 0 0 1-.621-4.72l1.189-1.19A1.5 1.5 0 0 1 5.378 3h13.243a1.5 1.5 0 0 1 1.06.44l1.19 1.189a3 3 0 0 1-.621 4.72M6.75 18h3.75a.75.75 0 0 0 .75-.75V13.5a.75.75 0 0 0-.75-.75H6.75a.75.75 0 0 0-.75.75v3.75c0 .414.336.75.75.75Z"/>
        </svg>
    </div>
    <div>
        <h2 class="text-lg font-bold text-slate-800">Pengaturan Toko</h2>
        <p class="text-xs text-slate-400 mt-0.5">Profil toko, format struk thermal, dan template pesan WhatsApp</p>
        @if ($this->branch)
        <div class="flex items-center gap-2 mt-2">
            <span class="size-2 rounded-full bg-emerald-400 animate-pulse"></span>
            <span class="text-xs font-semibold text-sky-700">{{ $this->branch->name }}</span>
            <span class="badge badge-sky text-[10px]">{{ $this->branch->code }}</span>
        </div>
        @endif
    </div>
</div>

{{-- ══ TABS ════════════════════════════════════════════════════════ --}}
<div class="flex gap-1 bg-slate-100 rounded-xl p-1 w-fit flex-wrap">
    @foreach (['profile' => '🏪 Profil Toko', 'receipt' => '🧾 Format Struk', 'printer' => '🖨️ Printer Thermal', 'whatsapp' => '💬 Template WA'] as $tab => $label)
    <button wire:click="$set('activeTab','{{ $tab }}')"
            @class(['px-4 py-2 rounded-lg text-xs font-semibold transition-all duration-150',
                    'bg-white shadow-sm text-sky-700' => $activeTab === $tab,
                    'text-slate-500 hover:text-slate-700' => $activeTab !== $tab])>
        {{ $label }}
    </button>
    @endforeach
</div>

{{-- ══ TAB: PROFIL TOKO ════════════════════════════════════════════ --}}
@if ($activeTab === 'profile')
<div class="card p-6 space-y-5">
    <h3 class="text-sm font-bold text-slate-800">Informasi Cabang</h3>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label class="form-label">Kode Cabang</label>
            <input type="text" value="{{ $this->branch?->code }}" disabled
                   class="form-input bg-slate-50 text-slate-400 cursor-not-allowed font-mono">
            <p class="text-xs text-slate-400 mt-1">Kode tidak dapat diubah</p>
        </div>
        <div>
            <label class="form-label">Nomor WhatsApp Admin</label>
            <div class="relative">
                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm text-slate-400">📱</span>
                <input type="text" wire:model="phone" class="form-input pl-9" placeholder="081234567890">
            </div>
            @error('phone') <p class="form-error">{{ $message }}</p> @enderror
        </div>
        <div class="sm:col-span-2">
            <label class="form-label">Nama Toko / Cabang</label>
            <input type="text" wire:model="name" class="form-input" placeholder="Kee Laundry – Pusat">
            @error('name') <p class="form-error">{{ $message }}</p> @enderror
        </div>
        <div class="sm:col-span-2">
            <label class="form-label">Alamat Lengkap</label>
            <textarea wire:model="address" rows="2" class="form-input resize-none" placeholder="Jl. ..."></textarea>
            @error('address') <p class="form-error">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="form-label">Jam Operasional</label>
            <input type="text" wire:model="operational_hours" class="form-input"
                   placeholder="Senin-Sabtu 07.00-21.00, Minggu 08.00-18.00">
        </div>
        <div>
            <label class="form-label">Target HPP / Kg</label>
            <div class="relative">
                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm text-slate-400">Rp</span>
                <input type="number" wire:model="target_hpp_per_kg" class="form-input pl-9">
            </div>
            <p class="text-xs text-slate-400 mt-1">Dipakai sebagai patokan efisiensi di Dashboard HPP</p>
            @error('target_hpp_per_kg') <p class="form-error">{{ $message }}</p> @enderror
        </div>
    </div>

    {{-- Active toggle --}}
    <div class="flex items-center justify-between py-3 px-4 rounded-xl bg-slate-50 border border-slate-200">
        <div>
            <p class="text-sm font-semibold text-slate-700">Status Cabang</p>
            <p class="text-xs text-slate-400">Nonaktifkan untuk menyembunyikan dari daftar cabang</p>
        </div>
        <button type="button" wire:click="$toggle('is_active')"
                @class(['relative inline-flex h-6 w-11 items-center rounded-full transition-colors duration-200',
                        'bg-sky-600' => $is_active, 'bg-slate-200' => !$is_active])>
            <span @class(['inline-block size-4 rounded-full bg-white shadow transform transition-transform duration-200',
                          'translate-x-6' => $is_active, 'translate-x-1' => !$is_active])></span>
        </button>
    </div>

    <div class="flex justify-end pt-2 border-t border-slate-100">
        <button wire:click="saveProfile" wire:loading.attr="disabled" class="btn-primary">
            <span wire:loading.remove wire:target="saveProfile">💾 Simpan Profil</span>
            <span wire:loading wire:target="saveProfile" class="flex items-center gap-2">
                <svg class="size-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                Menyimpan…
            </span>
        </button>
    </div>
</div>
@endif

{{-- ══ TAB: FORMAT STRUK ════════════════════════════════════════════ --}}
@if ($activeTab === 'receipt')
<div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
    {{-- Editor --}}
    <div class="card p-5 space-y-4">
        <h3 class="text-sm font-bold text-slate-800">Footer Struk Thermal (58mm)</h3>
        <div>
            <label class="form-label">Teks Footer Struk</label>
            <textarea wire:model.live="receipt_footer" rows="4" class="form-input resize-none font-mono text-xs"
                      placeholder="Terima kasih sudah mempercayakan cucian Anda. Komplain maks. 24 jam setelah diambil. WA: 081234567890"></textarea>
            <p class="text-xs text-slate-400 mt-1">Maks 300 karakter · {{ strlen($receipt_footer) }}/300</p>
            @error('receipt_footer') <p class="form-error">{{ $message }}</p> @enderror
        </div>
        <div class="flex justify-end">
            <button wire:click="saveReceipt" wire:loading.attr="disabled" class="btn-primary">
                <span wire:loading.remove wire:target="saveReceipt">💾 Simpan Footer</span>
                <span wire:loading wire:target="saveReceipt">Menyimpan…</span>
            </button>
        </div>
    </div>

    {{-- Live Preview struk 58mm --}}
    <div class="card p-5">
        <h3 class="text-sm font-bold text-slate-800 mb-4">Preview Struk Thermal</h3>
        <div class="mx-auto" style="max-width: 220px;">
            <div class="bg-white border-2 border-dashed border-slate-200 rounded-xl overflow-hidden font-mono text-xs text-center shadow-sm">
                {{-- Header --}}
                <div class="bg-sky-600 text-white py-3 px-3">
                    <p class="font-extrabold text-sm tracking-widest">{{ strtoupper($name ?: 'KEE LAUNDRY') }}</p>
                    <p class="text-sky-200 text-[10px] mt-0.5">{{ $this->branch?->name ?? 'Kee Laundry – Pusat' }}</p>
                </div>
                {{-- Sample content --}}
                <div class="px-3 py-3 space-y-1 text-left text-slate-600">
                    <div class="flex justify-between text-[10px] text-slate-400 border-b border-dashed pb-1.5 mb-1.5">
                        <span>19/10/2026 09:30</span>
                        <span class="font-semibold">INV-20261019-0001</span>
                    </div>
                    <p class="font-semibold text-slate-700">Dewi Kusuma</p>
                    <div class="flex justify-between text-[10px]"><span>Cuci Kiloan</span><span></span></div>
                    <div class="flex justify-between text-[10px] text-slate-400"><span>4.5 Kg × Rp 7.000</span><span>Rp 31.500</span></div>
                    <div class="border-t border-dashed border-slate-200 pt-1.5 mt-1.5">
                        <div class="flex justify-between font-bold text-sm">
                            <span>TOTAL</span>
                            <span class="text-sky-600">Rp 31.500</span>
                        </div>
                        <div class="flex justify-between text-[10px] text-slate-400 mt-0.5">
                            <span>TUNAI</span><span class="text-emerald-600 font-semibold">LUNAS</span>
                        </div>
                    </div>
                    <div class="border-t border-dashed border-slate-200 pt-2 mt-2">
                        <p class="text-[9px] text-slate-400 leading-relaxed text-center">
                            {{ $receipt_footer ?: 'Footer struk akan muncul di sini…' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

{{-- ══ TAB: PRINTER THERMAL ══════════════════════════════════════════ --}}
@if ($activeTab === 'printer')
    @include('livewire.settings._printer-settings')
@endif

{{-- ══ TAB: TEMPLATE WHATSAPP ══════════════════════════════════════ --}}
@if ($activeTab === 'whatsapp')
<div class="space-y-5">
    {{-- Variables help --}}
    <div class="rounded-2xl border border-sky-100 bg-sky-50/50 px-4 py-3">
        <p class="text-xs font-bold text-sky-700 mb-2">Variabel yang bisa digunakan dalam template:</p>
        <div class="flex flex-wrap gap-2">
            @foreach (['{nama}' => 'Nama pelanggan', '{invoice}' => 'Nomor nota', '{rak}' => 'Lokasi rak', '{toko}' => 'Nama toko'] as $var => $desc)
            <div class="flex items-center gap-1.5 bg-white rounded-lg px-2.5 py-1.5 border border-sky-100 shadow-sm">
                <code class="text-xs font-mono font-bold text-sky-700">{{ $var }}</code>
                <span class="text-[11px] text-slate-500">= {{ $desc }}</span>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Preview variables --}}
    <div class="card p-4">
        <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-3">Data Preview (untuk test)</p>
        <div class="grid grid-cols-3 gap-3">
            <div>
                <label class="form-label">Nama</label>
                <input type="text" wire:model.live="previewName" class="form-input text-sm py-2">
            </div>
            <div>
                <label class="form-label">Invoice</label>
                <input type="text" wire:model.live="previewInvoice" class="form-input text-sm py-2 font-mono">
            </div>
            <div>
                <label class="form-label">Lokasi Rak</label>
                <input type="text" wire:model.live="previewRak" class="form-input text-sm py-2">
            </div>
        </div>
    </div>

    {{-- Template 1: Siap Diambil --}}
    @include('livewire.settings._wa-template-card', [
        'icon'       => '🎉',
        'title'      => 'Template: Siap Diambil',
        'desc'       => 'Dikirim saat cucian sudah di rak dan siap diambil pelanggan',
        'model'      => 'wa_template_ready',
        'preview'    => $this->previewReady,
        'resetType'  => 'ready',
    ])

    {{-- Template 2: Selesai Diambil --}}
    @include('livewire.settings._wa-template-card', [
        'icon'       => '✅',
        'title'      => 'Template: Sudah Diambil',
        'desc'       => 'Dikirim setelah pelanggan mengambil cucian (follow-up & rating)',
        'model'      => 'wa_template_done',
        'preview'    => $this->previewDone,
        'resetType'  => 'done',
    ])

    {{-- Template 3: Promo Churn --}}
    @include('livewire.settings._wa-template-card', [
        'icon'       => '💌',
        'title'      => 'Template: Promo Pelanggan Pasif',
        'desc'       => 'Tombol WA di halaman Churn Alert menggunakan template ini',
        'model'      => 'wa_template_promo',
        'preview'    => $this->previewPromo,
        'resetType'  => 'promo',
    ])

    <div class="flex justify-end">
        <button wire:click="saveWhatsApp" wire:loading.attr="disabled" class="btn-primary">
            <span wire:loading.remove wire:target="saveWhatsApp">💾 Simpan Semua Template</span>
            <span wire:loading wire:target="saveWhatsApp" class="flex items-center gap-2">
                <svg class="size-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                Menyimpan…
            </span>
        </button>
    </div>
</div>
@endif

</div>
