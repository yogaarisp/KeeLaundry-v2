<div class="space-y-6">

{{-- ══ HEADER (Clean & Focused on Single Store) ════════════════════ --}}
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
    <div>
        <h2 class="text-xl font-bold text-slate-900 tracking-tight">Pengaturan Toko</h2>
        <p class="text-xs text-slate-500 mt-0.5">Kelola identitas outlet, format struk thermal, koneksi printer POS, &amp; template WhatsApp otomatis</p>
    </div>
    @if ($this->branch)
    <div class="flex items-center gap-2 self-start sm:self-auto bg-emerald-50 border border-emerald-200/80 rounded-full px-3.5 py-1.5 shadow-2xs">
        <span class="size-2 rounded-full bg-emerald-500 animate-pulse"></span>
        <span class="text-xs font-bold text-emerald-800">{{ $this->name ?: $this->branch->name }}</span>
        <span class="text-[10px] bg-emerald-200/60 text-emerald-800 font-semibold px-1.5 py-0.5 rounded-full">Outlet Utama</span>
    </div>
    @endif
</div>

{{-- ══ TABS (4-Column Segmented Control on Mobile, Pill Bar on Desktop) ═════════════════ --}}
<div class="grid grid-cols-4 bg-slate-100/90 p-1 rounded-2xl sm:w-fit sm:flex sm:gap-1.5 border border-slate-200/60 shadow-2xs">
    @php
    $tabs = [
        'profile'  => ['icon' => '🏪', 'short' => 'Profil',  'label' => 'Profil Toko'],
        'receipt'  => ['icon' => '🧾', 'short' => 'Struk',   'label' => 'Format Struk'],
        'printer'  => ['icon' => '🖨️', 'short' => 'Printer', 'label' => 'Printer Thermal'],
        'whatsapp' => ['icon' => '💬', 'short' => 'WA',      'label' => 'Template WA'],
    ];
    @endphp
    @foreach ($tabs as $tabKey => $tab)
    <button wire:click="$set('activeTab','{{ $tabKey }}')"
            @class([
                'py-2.5 px-2 sm:px-4 rounded-xl text-xs font-bold transition-all text-center flex items-center justify-center gap-2',
                'bg-white text-sky-700 shadow-xs border border-slate-200/60' => $activeTab === $tabKey,
                'text-slate-500 hover:text-slate-800 hover:bg-slate-200/50' => $activeTab !== $tabKey,
            ])>
        <span class="text-base sm:text-sm">{{ $tab['icon'] }}</span>
        <span class="sm:hidden text-[11px]">{{ $tab['short'] }}</span>
        <span class="hidden sm:inline">{{ $tab['label'] }}</span>
    </button>
    @endforeach
</div>

{{-- ══ TAB: PROFIL TOKO ════════════════════════════════════════════ --}}
@if ($activeTab === 'profile')
<div class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-start">
    {{-- Kolom Kiri: Branding & Status Operasional (lg:col-span-5 xl:col-span-4) --}}
    <div class="lg:col-span-5 xl:col-span-4 space-y-5">
        {{-- Card 1: Logo Toko & Favicon Browser --}}
        <div class="card p-5 space-y-4 shadow-sm border border-slate-200/80">
            <div class="border-b border-slate-100 pb-3">
                <h3 class="text-sm font-bold text-slate-800">Identitas Visual Toko</h3>
                <p class="text-xs text-slate-400 mt-0.5">Logo aplikasi, nota struk, &amp; icon tab browser</p>
            </div>

            {{-- Logo Toko Card --}}
            <div class="bg-slate-50/80 border border-slate-200/80 rounded-2xl p-4 space-y-3">
                <div class="flex items-center justify-between">
                    <div>
                        <h4 class="text-xs font-bold text-slate-800">Logo Toko</h4>
                        <p class="text-[11px] text-slate-400 mt-0.5">Header navigasi &amp; struk POS</p>
                    </div>
                    @if ($currentLogoUrl)
                        <button type="button" wire:click="deleteLogo" wire:confirm="Hapus logo toko ini?"
                                class="text-[11px] font-semibold text-rose-600 hover:text-rose-700 bg-rose-50 hover:bg-rose-100 px-2.5 py-1 rounded-lg transition-colors">
                            Hapus
                        </button>
                    @endif
                </div>

                <div class="flex items-center gap-3.5">
                    <div class="size-16 rounded-2xl bg-white border border-slate-200 shadow-2xs flex items-center justify-center overflow-hidden shrink-0">
                        @if ($logo)
                            <img src="{{ $logo->temporaryUrl() }}" alt="Preview Logo" class="size-full object-contain p-1">
                        @elseif ($currentLogoUrl)
                            <img src="{{ $currentLogoUrl }}" alt="Logo Toko" class="size-full object-contain p-1">
                        @else
                            <span class="text-2xl">🏪</span>
                        @endif
                    </div>

                    <div class="flex-1 min-w-0 space-y-1.5">
                        <label class="cursor-pointer inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-white border border-slate-200 hover:bg-slate-50 text-xs font-bold text-slate-700 shadow-2xs transition-colors">
                            <svg class="size-3.5 text-sky-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 3m0 0 4.5 4.5M12 3v13.5"/></svg>
                            <span>{{ $currentLogoUrl || $logo ? 'Ganti Logo' : 'Unggah Logo' }}</span>
                            <input type="file" wire:model="logo" accept="image/*" class="sr-only">
                        </label>
                        <p class="text-[10px] text-slate-400 leading-tight">PNG, JPG, SVG, WebP (Maks. 2MB)</p>
                        <div wire:loading wire:target="logo" class="text-[10px] font-semibold text-sky-600 animate-pulse">Mengunggah pratinjau…</div>
                        @error('logo') <p class="text-[10px] text-rose-600 font-semibold">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            {{-- Favicon Toko Card --}}
            <div class="bg-slate-50/80 border border-slate-200/80 rounded-2xl p-4 space-y-3">
                <div class="flex items-center justify-between">
                    <div>
                        <h4 class="text-xs font-bold text-slate-800">Favicon Browser</h4>
                        <p class="text-[11px] text-slate-400 mt-0.5">Icon tab browser laptop &amp; HP</p>
                    </div>
                    @if ($currentFaviconUrl)
                        <button type="button" wire:click="deleteFavicon" wire:confirm="Hapus favicon toko ini?"
                                class="text-[11px] font-semibold text-rose-600 hover:text-rose-700 bg-rose-50 hover:bg-rose-100 px-2.5 py-1 rounded-lg transition-colors">
                            Hapus
                        </button>
                    @endif
                </div>

                <div class="flex items-center gap-3.5">
                    <div class="size-16 rounded-2xl bg-white border border-slate-200 shadow-2xs flex items-center justify-center overflow-hidden shrink-0">
                        @if ($favicon)
                            <img src="{{ $favicon->temporaryUrl() }}" alt="Preview Favicon" class="size-8 object-contain">
                        @elseif ($currentFaviconUrl)
                            <img src="{{ $currentFaviconUrl }}" alt="Favicon Toko" class="size-8 object-contain">
                        @else
                            <span class="text-xl">🌐</span>
                        @endif
                    </div>

                    <div class="flex-1 min-w-0 space-y-1.5">
                        <label class="cursor-pointer inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-white border border-slate-200 hover:bg-slate-50 text-xs font-bold text-slate-700 shadow-2xs transition-colors">
                            <svg class="size-3.5 text-sky-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 3m0 0 4.5 4.5M12 3v13.5"/></svg>
                            <span>{{ $currentFaviconUrl || $favicon ? 'Ganti Favicon' : 'Unggah Favicon' }}</span>
                            <input type="file" wire:model="favicon" accept="image/*,.ico" class="sr-only">
                        </label>
                        <p class="text-[10px] text-slate-400 leading-tight">PNG, ICO, SVG (Ideal 32×32px, maks 1MB)</p>
                        <div wire:loading wire:target="favicon" class="text-[10px] font-semibold text-sky-600 animate-pulse">Mengunggah pratinjau…</div>
                        @error('favicon') <p class="text-[10px] text-rose-600 font-semibold">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- Card 2: Status & Parameter Toko --}}
        <div class="card p-5 space-y-4 shadow-sm border border-slate-200/80">
            <div class="border-b border-slate-100 pb-3">
                <h3 class="text-sm font-bold text-slate-800">Status &amp; Parameter Toko</h3>
                <p class="text-xs text-slate-400 mt-0.5">Operasional penerimaan order &amp; patokan HPP</p>
            </div>

            {{-- Status Operasional Toko --}}
            <div class="flex items-center justify-between p-3.5 rounded-xl border {{ $is_active ? 'bg-emerald-50/60 border-emerald-200/80' : 'bg-slate-50 border-slate-200' }} transition-colors">
                <div>
                    <div class="flex items-center gap-2">
                        <span class="size-2 rounded-full {{ $is_active ? 'bg-emerald-500 animate-pulse' : 'bg-slate-400' }}"></span>
                        <p class="text-xs font-bold {{ $is_active ? 'text-emerald-900' : 'text-slate-700' }}">
                            {{ $is_active ? 'Toko Buka (Aktif)' : 'Toko Tutup Sementara' }}
                        </p>
                    </div>
                    <p class="text-[11px] text-slate-500 mt-0.5">
                        {{ $is_active ? 'Kasir siap menerima order cucian baru' : 'Penerimaan order ditutup sementara' }}
                    </p>
                </div>
                <button type="button" wire:click="$toggle('is_active')"
                        @class(['relative inline-flex h-6 w-11 items-center rounded-full transition-colors duration-200 shrink-0',
                                'bg-emerald-600' => $is_active, 'bg-slate-300' => !$is_active])>
                    <span @class(['inline-block size-4 rounded-full bg-white shadow transform transition-transform duration-200',
                                  'translate-x-6' => $is_active, 'translate-x-1' => !$is_active])></span>
                </button>
            </div>

            {{-- ID / Kode Toko --}}
            <div>
                <label class="form-label text-xs font-semibold text-slate-700">Kode Identifikasi Outlet</label>
                <div class="flex items-center gap-2">
                    <input type="text" value="{{ $this->branch?->code ?? 'STORE-01' }}" disabled
                           class="form-input bg-slate-50 text-slate-500 cursor-not-allowed font-mono text-xs font-bold">
                    <span class="badge-neutral text-[11px] shrink-0">Outlet Utama</span>
                </div>
            </div>

            {{-- Target HPP Utilitas --}}
            <div>
                <label class="form-label text-xs font-semibold text-slate-700">Target HPP Utilitas / Kg</label>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400">Rp</span>
                    <input type="number" wire:model="target_hpp_per_kg" class="form-input pl-9 text-xs sm:text-sm font-bold">
                </div>
                <p class="text-[10px] text-slate-400 mt-1">Standar patokan efisiensi listrik, air, &amp; gas di dashboard analitik.</p>
                @error('target_hpp_per_kg') <p class="form-error">{{ $message }}</p> @enderror
            </div>
        </div>
    </div>

    {{-- Kolom Kanan: Detail Informasi & Kontak (lg:col-span-7 xl:col-span-8) --}}
    <div class="lg:col-span-7 xl:col-span-8 space-y-5">
        <div class="card p-5 sm:p-6 space-y-5 shadow-sm border border-slate-200/80">
            <div class="border-b border-slate-100 pb-3 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-bold text-slate-800">Detail Informasi &amp; Kontak Toko</h3>
                    <p class="text-xs text-slate-400 mt-0.5">Informasi resmi yang tercetak pada nota kasir dan pesan WhatsApp otomatis pelanggan.</p>
                </div>
            </div>

            <div class="space-y-4">
                {{-- Nama Toko --}}
                <div>
                    <label class="form-label text-xs font-bold text-slate-700">Nama Toko Laundry</label>
                    <input type="text" wire:model="name" class="form-input text-base font-bold text-slate-900" placeholder="cth. Kee Laundry Express">
                    <p class="text-[11px] text-slate-400 mt-1">Nama ini dicetak di kop struk thermal, header sistem kasir, &amp; pesan WhatsApp.</p>
                    @error('name') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                {{-- WhatsApp Toko --}}
                <div>
                    <label class="form-label text-xs font-bold text-slate-700">Nomor WhatsApp Kasir / CS Toko</label>
                    <div class="relative">
                        <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-sm text-slate-400">📱</span>
                        <input type="text" wire:model="phone" class="form-input pl-10 text-sm font-semibold" placeholder="081234567890">
                    </div>
                    <p class="text-[11px] text-slate-400 mt-1">Nomor kontak resmi yang dicetak pada nota struk kasir.</p>
                    @error('phone') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                {{-- Jam Operasional Toko (Komponen Terstruktur, Profesional & Simple) --}}
                <div class="bg-slate-50/80 border border-slate-200/90 rounded-2xl p-4 sm:p-5 space-y-3.5 shadow-2xs"
                     x-data="operationalHoursPicker(@js($operational_hours))">

                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2.5 border-b border-slate-200/70 pb-3">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="text-base">⏰</span>
                                <label class="text-xs font-bold text-slate-900">Jam Operasional Toko</label>
                            </div>
                            <p class="text-[11px] text-slate-500 mt-0.5">Jadwal buka layanan terima &amp; ambil cucian</p>
                        </div>

                        {{-- Mode Selector Pills --}}
                        <div class="flex items-center gap-1 bg-white p-1 rounded-xl border border-slate-200 shadow-2xs self-start sm:self-auto">
                            <button type="button" @click="mode = 'split'"
                                    :class="mode === 'split' ? 'bg-sky-600 text-white font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50'"
                                    class="px-2.5 py-1 rounded-lg text-[11px] transition-all">
                                Senin–Sabtu &amp; Minggu
                            </button>
                            <button type="button" @click="mode = 'daily'"
                                    :class="mode === 'daily' ? 'bg-sky-600 text-white font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50'"
                                    class="px-2.5 py-1 rounded-lg text-[11px] transition-all">
                                Setiap Hari Sama
                            </button>
                            <button type="button" @click="mode = '24h'"
                                    :class="mode === '24h' ? 'bg-sky-600 text-white font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50'"
                                    class="px-2.5 py-1 rounded-lg text-[11px] transition-all">
                                24 Jam
                            </button>
                            <button type="button" @click="mode = 'custom'"
                                    :class="mode === 'custom' ? 'bg-sky-600 text-white font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50'"
                                    class="px-2.5 py-1 rounded-lg text-[11px] transition-all">
                                Kustom
                            </button>
                        </div>
                    </div>

                    {{-- Mode 1: Senin-Sabtu & Minggu Berbeda --}}
                    <div x-show="mode === 'split'" x-cloak class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        {{-- Senin - Sabtu --}}
                        <div class="bg-white border border-slate-200/90 rounded-xl p-3.5 space-y-2.5 shadow-2xs">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-bold text-slate-800 flex items-center gap-1.5">
                                    <span class="size-2 rounded-full bg-sky-500"></span>
                                    Senin – Sabtu (Hari Kerja)
                                </span>
                                <span class="text-[10px] text-slate-400 font-medium">6 Hari</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <div class="flex-1">
                                    <span class="text-[10px] text-slate-400 block mb-1">Jam Buka</span>
                                    <input type="time" x-model="weekdayOpen"
                                           class="w-full text-xs font-bold py-1.5 px-2.5 rounded-lg bg-slate-50 border border-slate-200 focus:bg-white focus:border-sky-500 outline-none transition-colors">
                                </div>
                                <span class="text-xs text-slate-400 font-bold mt-4">s/d</span>
                                <div class="flex-1">
                                    <span class="text-[10px] text-slate-400 block mb-1">Jam Tutup</span>
                                    <input type="time" x-model="weekdayClose"
                                           class="w-full text-xs font-bold py-1.5 px-2.5 rounded-lg bg-slate-50 border border-slate-200 focus:bg-white focus:border-sky-500 outline-none transition-colors">
                                </div>
                            </div>
                        </div>

                        {{-- Minggu / Akhir Pekan --}}
                        <div class="bg-white border border-slate-200/90 rounded-xl p-3.5 space-y-2.5 shadow-2xs"
                             :class="sundayClosed ? 'opacity-80 bg-slate-50/80 border-dashed' : ''">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-bold text-slate-800 flex items-center gap-1.5">
                                    <span class="size-2 rounded-full" :class="sundayClosed ? 'bg-rose-500' : 'bg-emerald-500'"></span>
                                    Minggu (Akhir Pekan)
                                </span>
                                <label class="flex items-center gap-1.5 cursor-pointer">
                                    <input type="checkbox" x-model="sundayClosed" class="rounded text-rose-600 focus:ring-rose-500 size-3.5">
                                    <span class="text-[10px] font-bold" :class="sundayClosed ? 'text-rose-600' : 'text-slate-500'">Tutup / Libur</span>
                                </label>
                            </div>
                            <div class="flex items-center gap-2" x-show="!sundayClosed">
                                <div class="flex-1">
                                    <span class="text-[10px] text-slate-400 block mb-1">Jam Buka</span>
                                    <input type="time" x-model="sundayOpen"
                                           class="w-full text-xs font-bold py-1.5 px-2.5 rounded-lg bg-slate-50 border border-slate-200 focus:bg-white focus:border-sky-500 outline-none transition-colors">
                                </div>
                                <span class="text-xs text-slate-400 font-bold mt-4">s/d</span>
                                <div class="flex-1">
                                    <span class="text-[10px] text-slate-400 block mb-1">Jam Tutup</span>
                                    <input type="time" x-model="sundayClose"
                                           class="w-full text-xs font-bold py-1.5 px-2.5 rounded-lg bg-slate-50 border border-slate-200 focus:bg-white focus:border-sky-500 outline-none transition-colors">
                                </div>
                            </div>
                            <div x-show="sundayClosed" class="text-xs text-rose-600 font-semibold py-2 px-3 text-center bg-rose-50/80 rounded-lg border border-rose-100">
                                🚫 Outlet Libur pada hari Minggu
                            </div>
                        </div>
                    </div>

                    {{-- Mode 2: Setiap Hari Sama --}}
                    <div x-show="mode === 'daily'" x-cloak class="bg-white border border-slate-200/90 rounded-xl p-4 shadow-2xs max-w-md">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-bold text-slate-800 flex items-center gap-1.5">
                                <span class="size-2 rounded-full bg-emerald-500"></span>
                                Setiap Hari (Senin – Minggu Jam Sama)
                            </span>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="flex-1">
                                <span class="text-[10px] text-slate-400 block mb-1">Jam Buka</span>
                                <input type="time" x-model="dailyOpen"
                                       class="w-full text-xs font-bold py-2 px-3 rounded-xl bg-slate-50 border border-slate-200 focus:bg-white focus:border-sky-500 outline-none transition-colors">
                            </div>
                            <span class="text-xs text-slate-400 font-bold mt-4">s/d</span>
                            <div class="flex-1">
                                <span class="text-[10px] text-slate-400 block mb-1">Jam Tutup</span>
                                <input type="time" x-model="dailyClose"
                                       class="w-full text-xs font-bold py-2 px-3 rounded-xl bg-slate-50 border border-slate-200 focus:bg-white focus:border-sky-500 outline-none transition-colors">
                            </div>
                        </div>
                    </div>

                    {{-- Mode 3: 24 Jam --}}
                    <div x-show="mode === '24h'" x-cloak class="bg-white border border-sky-200 rounded-xl p-4 shadow-2xs flex items-center gap-3.5">
                        <div class="size-10 rounded-xl bg-sky-50 flex items-center justify-center text-xl shrink-0">⚡</div>
                        <div>
                            <p class="text-xs font-bold text-sky-900">Layanan Buka 24 Jam Non-Stop</p>
                            <p class="text-[11px] text-sky-700 mt-0.5">Cocok untuk laundry koin / laundromat mandiri atau operasional shift 24 jam.</p>
                        </div>
                    </div>

                    {{-- Mode 4: Kustom Manual --}}
                    <div x-show="mode === 'custom'" x-cloak class="space-y-1.5">
                        <input type="text" x-model="raw" @input="@this.set('operational_hours', raw)"
                               class="form-input text-xs sm:text-sm font-semibold"
                               placeholder="cth. Senin–Sabtu 07.00–21.00, Minggu 08.00–18.00">
                        <p class="text-[10px] text-slate-400">Tuliskan teks kustom jika Anda memiliki catatan jadwal khusus.</p>
                    </div>

                    {{-- Bottom Status & Presets --}}
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pt-2.5 border-t border-slate-200/70">
                        <div class="flex items-center gap-2 min-w-0">
                            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider shrink-0">Hasil Jadwal:</span>
                            <span class="text-xs font-bold text-sky-800 bg-sky-50 border border-sky-200/80 px-2.5 py-1 rounded-lg truncate" x-text="raw || 'Belum diatur'"></span>
                        </div>
                        <div class="flex items-center gap-1.5 shrink-0 flex-wrap">
                            <span class="text-[10px] text-slate-400 font-semibold">Preset cepat:</span>
                            <button type="button" @click="applyPreset('default_split')" class="text-[10px] font-bold text-slate-600 hover:text-sky-600 bg-white hover:bg-slate-100 border border-slate-200 px-2 py-0.5 rounded-md transition-colors shadow-2xs">
                                07–21 &amp; 08–18
                            </button>
                            <button type="button" @click="applyPreset('daily_7_21')" class="text-[10px] font-bold text-slate-600 hover:text-sky-600 bg-white hover:bg-slate-100 border border-slate-200 px-2 py-0.5 rounded-md transition-colors shadow-2xs">
                                07–21 Harian
                            </button>
                            <button type="button" @click="applyPreset('daily_8_20')" class="text-[10px] font-bold text-slate-600 hover:text-sky-600 bg-white hover:bg-slate-100 border border-slate-200 px-2 py-0.5 rounded-md transition-colors shadow-2xs">
                                08–20 Harian
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Alamat Lengkap --}}
                <div>
                    <label class="form-label text-xs font-bold text-slate-700">Alamat Lengkap Toko</label>
                    <textarea wire:model="address" rows="3" class="form-input text-sm resize-none leading-relaxed" placeholder="cth. Jl. Danau Sentani No. 45, Sawojajar, Malang"></textarea>
                    <p class="text-[11px] text-slate-400 mt-1">Alamat fisik toko yang tercetak di bagian atas nota thermal kasir.</p>
                    @error('address') <p class="form-error">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Save Action Bar --}}
            <div class="pt-4 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-3">
                <div class="text-xs text-slate-400 flex items-center gap-1.5 order-2 sm:order-1">
                    <svg class="size-4 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
                    <span>Perubahan disimpan langsung ke data outlet utama</span>
                </div>
                <button wire:click="saveProfile" wire:loading.attr="disabled" wire:target="saveProfile"
                        class="btn-primary w-full sm:w-auto px-7 py-2.5 text-sm font-bold shadow-md justify-center order-1 sm:order-2">
                    <span wire:loading.remove wire:target="saveProfile" class="flex items-center gap-2">
                        <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
                        Simpan Profil Toko
                    </span>
                    <span wire:loading wire:target="saveProfile" class="flex items-center gap-2">
                        <svg class="size-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                        Menyimpan…
                    </span>
                </button>
            </div>
        </div>
    </div>
</div>
@endif

{{-- ══ TAB: FORMAT STRUK ════════════════════════════════════════════ --}}
@if ($activeTab === 'receipt')
<div class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-start">
    {{-- Editor & Aturan Struk (lg:col-span-7) --}}
    <div class="lg:col-span-7 space-y-4">
        <div class="card p-5 sm:p-6 space-y-4 shadow-sm border border-slate-200/80">
            <div class="border-b border-slate-100 pb-3">
                <h3 class="text-sm font-bold text-slate-800">Teks Footer Struk Kasir</h3>
                <p class="text-xs text-slate-400 mt-0.5">Pesan atau syarat &amp; ketentuan yang tercetak di bagian paling bawah struk thermal POS.</p>
            </div>

            <div>
                <label class="form-label text-xs font-bold text-slate-700">Pesan Footer</label>
                <textarea wire:model.live="receipt_footer" rows="5" class="form-input resize-none font-mono text-xs leading-relaxed"
                          placeholder="Terima kasih sudah mempercayakan cucian Anda. Komplain maks. 24 jam setelah diambil wajib membawa nota ini. WA: 081234567890"></textarea>
                <div class="flex justify-between items-center text-xs text-slate-400 mt-1.5">
                    <span>Maksimal 300 karakter</span>
                    <span class="font-mono font-bold">{{ strlen($receipt_footer) }}/300</span>
                </div>
                @error('receipt_footer') <p class="form-error">{{ $message }}</p> @enderror
            </div>

            {{-- Template Cepat Footer Struk --}}
            <div class="space-y-1.5 pt-2 border-t border-slate-100">
                <p class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Template Cepat Footer:</p>
                <div class="flex flex-wrap gap-1.5">
                    <button type="button"
                            wire:click="$set('receipt_footer', 'Terima kasih atas kunjungan Anda. Komplain maks. 24 jam setelah cucian diambil dengan membawa nota ini.')"
                            class="text-[11px] px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium transition-colors">
                        Standard Komplain 24 Jam
                    </button>
                    <button type="button"
                            wire:click="$set('receipt_footer', 'Cucian bersih, wangi, & rapi. Simpan nota ini sebagai bukti pengambilan. Terima kasih!')"
                            class="text-[11px] px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium transition-colors">
                        Ringkas &amp; Ramah
                    </button>
                    <button type="button"
                            wire:click="$set('receipt_footer', 'Pakaian tidak diambil >30 hari di luar tanggung jawab laundry. Cek status cucian di keelaundry.test/cek')"
                            class="text-[11px] px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium transition-colors">
                        Batas Pengambilan 30 Hari
                    </button>
                </div>
            </div>

            <div class="pt-3 border-t border-slate-100 flex justify-end">
                <button wire:click="saveReceipt" wire:loading.attr="disabled" wire:target="saveReceipt"
                        class="btn-primary w-full sm:w-auto px-7 py-2.5 text-sm font-bold shadow-md justify-center">
                    <span wire:loading.remove wire:target="saveReceipt">💾 Simpan Format Struk</span>
                    <span wire:loading wire:target="saveReceipt" class="flex items-center gap-2">
                        <svg class="size-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                        Menyimpan…
                    </span>
                </button>
            </div>
        </div>

        {{-- Panduan Cetak Struk --}}
        <div class="card p-5 bg-gradient-to-br from-slate-50 to-white space-y-2 border-slate-200/80 shadow-2xs">
            <h4 class="text-xs font-bold text-slate-800 flex items-center gap-1.5">
                <span>💡</span> Tips Format Struk Kasir
            </h4>
            <p class="text-xs text-slate-500 leading-relaxed">
                Teks footer akan otomatis dicetak pada kertas thermal 58mm maupun 80mm. Pastikan mencantumkan nomor kontak darurat atau tautan lacak resi agar pelanggan mudah mengecek status cucian mereka.
            </p>
        </div>
    </div>

    {{-- Live Preview struk 58mm (lg:col-span-5) --}}
    <div class="lg:col-span-5 lg:sticky lg:top-4">
        <div class="card p-5 space-y-3 shadow-sm border border-slate-200/80">
            <div class="border-b border-slate-100 pb-2 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-bold text-slate-800">Simulasi Tampilan Struk</h3>
                    <p class="text-xs text-slate-400">Pratinjau kertas thermal roll 58mm</p>
                </div>
                <span class="badge-neutral text-[10px] font-mono">58mm Roll</span>
            </div>

            <div class="mx-auto" style="max-width: 250px;">
                <div class="bg-white border-2 border-dashed border-slate-300 rounded-2xl overflow-hidden font-mono text-xs text-center shadow-lg">
                    {{-- Header Struk --}}
                    <div class="bg-slate-900 text-white py-3.5 px-3">
                        @if($currentLogoUrl || $logo)
                            <div class="size-8 bg-white rounded-lg mx-auto mb-1.5 flex items-center justify-center p-0.5">
                                <img src="{{ $logo ? $logo->temporaryUrl() : $currentLogoUrl }}" class="size-full object-contain">
                            </div>
                        @endif
                        <p class="font-extrabold text-sm tracking-wider">{{ strtoupper($name ?: 'KEE LAUNDRY') }}</p>
                        <p class="text-slate-300 text-[10px] mt-0.5">{{ $phone ?: '0812-3456-7890' }}</p>
                        <p class="text-slate-400 text-[9px] mt-0.5 line-clamp-1">{{ $address ?: 'Outlet Utama' }}</p>
                    </div>

                    {{-- Sample content --}}
                    <div class="px-3.5 py-3 space-y-1.5 text-left text-slate-600 text-[11px]">
                        <div class="flex justify-between text-[10px] text-slate-400 border-b border-dashed border-slate-200 pb-1.5 mb-1.5">
                            <span>{{ now()->format('d/m/Y H:i') }}</span>
                            <span class="font-semibold text-slate-700">INV-0001</span>
                        </div>
                        <p class="font-bold text-slate-800">Pelanggan: Dewi Kusuma</p>
                        <div class="flex justify-between text-[10px]"><span>Cuci Kiloan Reguler</span><span></span></div>
                        <div class="flex justify-between text-[10px] text-slate-400"><span>4.5 Kg × Rp 7.000</span><span>Rp 31.500</span></div>
                        <div class="border-t border-dashed border-slate-200 pt-1.5 mt-1.5">
                            <div class="flex justify-between font-bold text-xs text-slate-800">
                                <span>TOTAL</span>
                                <span class="text-sky-700">Rp 31.500</span>
                            </div>
                            <div class="flex justify-between text-[10px] text-slate-400 mt-0.5">
                                <span>TUNAI</span><span class="text-emerald-600 font-semibold">LUNAS</span>
                            </div>
                        </div>
                        <div class="border-t border-dashed border-slate-200 pt-2.5 mt-2">
                            <p class="text-[10px] text-slate-600 leading-relaxed text-center italic">
                                {{ $receipt_footer ?: 'Footer struk akan tampil di sini…' }}
                            </p>
                        </div>
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
    {{-- Variables Help --}}
    <div class="rounded-2xl border border-sky-100 bg-sky-50/70 p-4 space-y-2 shadow-2xs">
        <p class="text-xs font-bold text-sky-900 flex items-center gap-1.5">
            <svg class="size-4 text-sky-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z"/></svg>
            Variabel Otomatis WhatsApp
        </p>
        <p class="text-[11px] text-sky-700">Gunakan tag variabel di bawah ini di dalam teks pesan Anda. Sistem akan menggantinya secara dinamis saat pesan dikirimkan ke pelanggan.</p>
        <div class="flex flex-wrap gap-2 pt-1">
            @foreach (['{nama}' => 'Nama pelanggan', '{invoice}' => 'Nomor nota', '{rak}' => 'Lokasi rak cucian', '{toko}' => 'Nama outlet'] as $var => $desc)
            <div class="flex items-center gap-1.5 bg-white rounded-xl px-2.5 py-1 border border-sky-200/60 shadow-2xs">
                <code class="text-xs font-mono font-bold text-sky-700">{{ $var }}</code>
                <span class="text-[11px] text-slate-500 font-medium">= {{ $desc }}</span>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Preview Variables Bar --}}
    <div class="card p-4 sm:p-5 space-y-3 shadow-sm border border-slate-200/80">
        <div class="flex items-center justify-between">
            <p class="text-xs font-bold text-slate-700 uppercase tracking-wide">Data Simulasi Pratinjau Live</p>
            <span class="text-[11px] text-slate-400">Edit nilai simulasi untuk menguji tampilan balon WhatsApp</span>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div>
                <label class="form-label text-[11px]">Nama Pelanggan</label>
                <input type="text" wire:model.live="previewName" class="form-input text-xs py-2 font-semibold">
            </div>
            <div>
                <label class="form-label text-[11px]">Nomor Invoice</label>
                <input type="text" wire:model.live="previewInvoice" class="form-input text-xs py-2 font-mono">
            </div>
            <div>
                <label class="form-label text-[11px]">Lokasi Rak</label>
                <input type="text" wire:model.live="previewRak" class="form-input text-xs py-2 font-semibold">
            </div>
        </div>
    </div>

    {{-- Template 1: Siap Diambil --}}
    @include('livewire.settings._wa-template-card', [
        'icon'       => '🎉',
        'title'      => 'Template: Cucian Siap Diambil',
        'desc'       => 'Dikirim otomatis saat status pesanan diubah ke SIAP_DIAMBIL di produksi / kasir',
        'model'      => 'wa_template_ready',
        'preview'    => $this->previewReady,
        'resetType'  => 'ready',
    ])

    {{-- Template 2: Selesai Diambil --}}
    @include('livewire.settings._wa-template-card', [
        'icon'       => '✅',
        'title'      => 'Template: Cucian Selesai Diambil',
        'desc'       => 'Dikirim setelah pelanggan mengambil pakaian (ucapan terima kasih & ulasan)',
        'model'      => 'wa_template_done',
        'preview'    => $this->previewDone,
        'resetType'  => 'done',
    ])

    {{-- Template 3: Promo Churn --}}
    @include('livewire.settings._wa-template-card', [
        'icon'       => '💌',
        'title'      => 'Template: Sapa Pelanggan Pasif',
        'desc'       => 'Digunakan di CRM Pelanggan untuk menyapa pelanggan yang sudah lama tidak mencuci',
        'model'      => 'wa_template_promo',
        'preview'    => $this->previewPromo,
        'resetType'  => 'promo',
    ])

    <div class="pt-2 border-t border-slate-100 flex justify-end">
        <button wire:click="saveWhatsApp" wire:loading.attr="disabled" wire:target="saveWhatsApp"
                class="btn-primary w-full sm:w-auto px-7 py-3 text-sm font-bold shadow-md justify-center">
            <span wire:loading.remove wire:target="saveWhatsApp">💾 Simpan Semua Template WA</span>
            <span wire:loading wire:target="saveWhatsApp" class="flex items-center gap-2">
                <svg class="size-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                Menyimpan…
            </span>
        </button>
    </div>
</div>
@endif

</div>

<script>
window.operationalHoursPicker = function(initialValue) {
    return {
        raw: initialValue || '',
        mode: 'split',
        weekdayOpen: '07:00',
        weekdayClose: '21:00',
        sundayOpen: '08:00',
        sundayClose: '18:00',
        sundayClosed: false,
        dailyOpen: '07:00',
        dailyClose: '21:00',
        isInitialized: false,

        init() {
            this.parseInitial(this.raw);
            this.$nextTick(() => {
                this.isInitialized = true;
            });
            this.$watch('mode', () => { if (this.isInitialized) this.updateText(); });
            this.$watch('weekdayOpen', () => { if (this.isInitialized) this.updateText(); });
            this.$watch('weekdayClose', () => { if (this.isInitialized) this.updateText(); });
            this.$watch('sundayOpen', () => { if (this.isInitialized) this.updateText(); });
            this.$watch('sundayClose', () => { if (this.isInitialized) this.updateText(); });
            this.$watch('sundayClosed', () => { if (this.isInitialized) this.updateText(); });
            this.$watch('dailyOpen', () => { if (this.isInitialized) this.updateText(); });
            this.$watch('dailyClose', () => { if (this.isInitialized) this.updateText(); });
        },

        parseInitial(str) {
            if (!str) {
                this.mode = 'split';
                return;
            }
            const s = str.trim();
            if (s.toLowerCase().includes('24 jam')) {
                this.mode = '24h';
                return;
            }
            if (s.toLowerCase().includes('minggu')) {
                this.mode = 'split';
                const parts = s.split(/[,;\n]/);
                if (parts.length >= 1) {
                    const m1 = parts[0].match(/(\d{1,2})[.:](\d{2})\s*[-–]\s*(\d{1,2})[.:](\d{2})/);
                    if (m1) {
                        this.weekdayOpen = `${m1[1].padStart(2, '0')}:${m1[2]}`;
                        this.weekdayClose = `${m1[3].padStart(2, '0')}:${m1[4]}`;
                    }
                }
                if (parts.length >= 2) {
                    if (parts[1].toLowerCase().includes('tutup') || parts[1].toLowerCase().includes('libur')) {
                        this.sundayClosed = true;
                    } else {
                        const m2 = parts[1].match(/(\d{1,2})[.:](\d{2})\s*[-–]\s*(\d{1,2})[.:](\d{2})/);
                        if (m2) {
                            this.sundayOpen = `${m2[1].padStart(2, '0')}:${m2[2]}`;
                            this.sundayClose = `${m2[3].padStart(2, '0')}:${m2[4]}`;
                        }
                    }
                }
                return;
            }
            if (s.toLowerCase().includes('setiap hari')) {
                this.mode = 'daily';
                const m = s.match(/(\d{1,2})[.:](\d{2})\s*[-–]\s*(\d{1,2})[.:](\d{2})/);
                if (m) {
                    this.dailyOpen = `${m[1].padStart(2, '0')}:${m[2]}`;
                    this.dailyClose = `${m[3].padStart(2, '0')}:${m[4]}`;
                }
                return;
            }
            this.mode = 'custom';
        },

        formatHour(val) {
            return (val || '00:00').replace(':', '.');
        },

        updateText() {
            if (this.mode === '24h') {
                this.raw = 'Buka 24 Jam Non-Stop';
            } else if (this.mode === 'daily') {
                this.raw = `Setiap Hari ${this.formatHour(this.dailyOpen)} – ${this.formatHour(this.dailyClose)}`;
            } else if (this.mode === 'split') {
                const sunText = this.sundayClosed 
                    ? 'Minggu Libur' 
                    : `Minggu ${this.formatHour(this.sundayOpen)} – ${this.formatHour(this.sundayClose)}`;
                this.raw = `Senin–Sabtu ${this.formatHour(this.weekdayOpen)} – ${this.formatHour(this.weekdayClose)}, ${sunText}`;
            }
            @this.set('operational_hours', this.raw);
        },

        applyPreset(presetType) {
            this.isInitialized = false;
            if (presetType === 'default_split') {
                this.mode = 'split';
                this.weekdayOpen = '07:00';
                this.weekdayClose = '21:00';
                this.sundayOpen = '08:00';
                this.sundayClose = '18:00';
                this.sundayClosed = false;
            } else if (presetType === 'daily_7_21') {
                this.mode = 'daily';
                this.dailyOpen = '07:00';
                this.dailyClose = '21:00';
            } else if (presetType === 'daily_8_20') {
                this.mode = 'daily';
                this.dailyOpen = '08:00';
                this.dailyClose = '20:00';
            } else if (presetType === '24h') {
                this.mode = '24h';
            }
            this.updateText();
            this.isInitialized = true;
        }
    };
};
</script>
