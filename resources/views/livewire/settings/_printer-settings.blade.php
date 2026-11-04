<div class="space-y-5" x-data="printerPage()" x-init="boot()">

    {{-- ══ STATUS CARD ═══════════════════════════════════════════════════ --}}
    <div class="card p-5 sm:p-6 border border-slate-200/90 shadow-xs">

        {{-- Header --}}
        <div class="flex items-start justify-between gap-4 pb-5 border-b border-slate-100">
            <div class="flex items-center gap-3">
                <div class="size-11 rounded-2xl flex items-center justify-center shrink-0 border transition-all"
                     :class="connected ? 'bg-emerald-50 border-emerald-200 text-emerald-600' : 'bg-slate-50 border-slate-200 text-slate-400'">
                    <svg class="size-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0 1 10.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0 .229 2.523a1.125 1.125 0 0 1-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0 0 21 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 0 0-1.913-.247M6.34 18H5.25A2.25 2.25 0 0 1 3 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 0 1 1.913-.247m10.5 0a48.536 48.536 0 0 0-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5Zm-3 0h.008v.008H15V10.5Z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-base font-bold text-slate-800">Printer Thermal POS</h3>
                    <p class="text-xs text-slate-400 mt-0.5" x-text="connected ? 'Terhubung via ' + connType : 'Belum ada printer terhubung'">Belum ada printer terhubung</p>
                </div>
            </div>
            {{-- Status pill --}}
            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-bold border shrink-0 transition-all"
                 :class="connected ? 'bg-emerald-50 border-emerald-200 text-emerald-700' : 'bg-slate-100 border-slate-200 text-slate-500'">
                <span class="size-1.5 rounded-full"
                      :class="connected ? 'bg-emerald-500 animate-pulse' : 'bg-slate-400'"></span>
                <span x-text="connected ? devName : 'Belum Terhubung'">Belum Terhubung</span>
            </div>
        </div>

        {{-- Tombol utama --}}
        <div class="pt-5 space-y-3">

            {{-- SCAN BLUETOOTH --}}
            <button type="button" @click="scan()"
                    :disabled="scanning || connected"
                    class="w-full flex items-center justify-center gap-3 py-4 rounded-2xl font-bold text-sm transition-all
                           bg-sky-600 hover:bg-sky-700 active:bg-sky-800 text-white shadow-sm hover:shadow-md
                           disabled:opacity-50 disabled:cursor-not-allowed">
                <template x-if="!scanning">
                    <svg class="size-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.348 14.652a3.75 3.75 0 0 1 0-5.304m5.304 0a3.75 3.75 0 0 1 0 5.304m-7.425 2.121a6.75 6.75 0 0 1 0-9.546m9.546 0a6.75 6.75 0 0 1 0 9.546M5.106 18.894c-3.808-3.808-3.808-9.98 0-13.789m13.788 0c3.808 3.808 3.808 9.98 0 13.788M12 12h.008v.008H12V12Z"/>
                    </svg>
                </template>
                <template x-if="scanning">
                    <svg class="size-5 animate-spin shrink-0" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                    </svg>
                </template>
                <span x-text="scanning ? 'Sedang Mencari Printer…' : (connected ? '✓ Printer Sudah Terhubung' : 'Scan & Cari Printer Bluetooth')">
                    Scan &amp; Cari Printer Bluetooth
                </span>
            </button>

            {{-- Baris alternatif --}}
            <div class="flex items-center gap-2 justify-center pt-1">
                <button type="button" @click="connectUsb()"
                        class="text-xs font-semibold text-slate-500 hover:text-sky-700 transition-colors flex items-center gap-1.5 px-3 py-2 rounded-lg hover:bg-slate-50">
                    <svg class="size-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 16.5V9.75m0 0 3 3m-3-3-3 3M6.75 19.5a4.5 4.5 0 0 1-1.41-8.775 5.25 5.25 0 0 1 10.233-2.33 3 3 0 0 1 3.758 3.848A3.752 3.752 0 0 1 18 19.5H6.75Z"/>
                    </svg>
                    USB Kabel / Serial
                </button>
                <span class="text-slate-200 text-sm">|</span>
                <button type="button" @click="useSystem()"
                        class="text-xs font-semibold text-slate-500 hover:text-sky-700 transition-colors flex items-center gap-1.5 px-3 py-2 rounded-lg hover:bg-slate-50">
                    <svg class="size-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 17.25v1.007a3 3 0 0 1-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0 1 15 18.257V17.25m6-12V15a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 15V5.25m18 0A2.25 2.25 0 0 0 18.75 3H5.25A2.25 2.25 0 0 0 3 5.25m18 0H3"/>
                    </svg>
                    Dialog Cetak Browser
                </button>
                <template x-if="connected">
                    <span class="text-slate-200 text-sm">|</span>
                </template>
                <template x-if="connected">
                    <button type="button" @click="disconnect()"
                            class="text-xs font-semibold text-rose-500 hover:text-rose-700 transition-colors flex items-center gap-1.5 px-3 py-2 rounded-lg hover:bg-rose-50">
                        <svg class="size-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/>
                        </svg>
                        Putuskan
                    </button>
                </template>
            </div>

            {{-- Error message --}}
            <div x-show="errMsg" x-cloak x-transition
                 class="flex items-start gap-3 p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs">
                <svg class="size-4 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/>
                </svg>
                <span x-text="errMsg"></span>
            </div>
        </div>

        {{-- Connected info --}}
        <template x-if="connected">
            <div class="mt-5 flex items-center justify-between p-4 rounded-xl bg-emerald-50 border border-emerald-200">
                <div class="flex items-center gap-3">
                    <div class="size-9 rounded-xl bg-emerald-100 border border-emerald-200 flex items-center justify-center text-emerald-600">
                        <svg class="size-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0 1 10.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0 .229 2.523a1.125 1.125 0 0 1-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0 0 21 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 0 0-1.913-.247M6.34 18H5.25A2.25 2.25 0 0 1 3 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 0 1 1.913-.247m10.5 0a48.536 48.536 0 0 0-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5Zm-3 0h.008v.008H15V10.5Z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-emerald-800" x-text="devName"></p>
                        <p class="text-[11px] text-emerald-600" x-text="connType + ' · Siap mencetak'"></p>
                    </div>
                </div>
                <button type="button" @click="testPrint()"
                        class="flex items-center gap-2 px-4 py-2 rounded-xl bg-white border border-emerald-200 text-emerald-700 text-xs font-bold hover:bg-emerald-50 transition-colors shadow-2xs">
                    <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0 1 10.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0 .229 2.523a1.125 1.125 0 0 1-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0 0 21 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 0 0-1.913-.247M6.34 18H5.25A2.25 2.25 0 0 1 3 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 0 1 1.913-.247m10.5 0a48.536 48.536 0 0 0-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5Zm-3 0h.008v.008H15V10.5Z"/>
                    </svg>
                    Test Print
                </button>
            </div>
        </template>
    </div>

    {{-- ══ PENGATURAN KERTAS & AUTO-PRINT ═════════════════════════════════ --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

        {{-- Ukuran Kertas --}}
        <div class="card p-5 border border-slate-200/90 shadow-xs space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h4 class="text-sm font-bold text-slate-800">Ukuran Kertas</h4>
                    <p class="text-[11px] text-slate-400 mt-0.5">Sesuaikan dengan roll printer Anda</p>
                </div>
                <span class="text-[10px] font-bold bg-sky-100 text-sky-700 px-2 py-0.5 rounded-full uppercase">
                    Aktif: {{ $printer_paper_size }}
                </span>
            </div>
            <div class="grid grid-cols-2 gap-3">
                @foreach (['58mm' => 'Roll Mini POS (standar)', '80mm' => 'Roll Kasir Lebar'] as $size => $desc)
                    <button type="button"
                            wire:click="$set('printer_paper_size', '{{ $size }}')"
                            @click="$nextTick(() => @this.savePrinter())"
                            class="relative flex flex-col items-center justify-center gap-1 p-4 rounded-2xl border-2 transition-all cursor-pointer
                                   {{ $printer_paper_size === $size
                                       ? 'border-sky-500 bg-sky-50/60'
                                       : 'border-slate-200 bg-white hover:border-slate-300' }}">
                        <span class="text-2xl font-black {{ $printer_paper_size === $size ? 'text-sky-600' : 'text-slate-400' }}">
                            {{ $size }}
                        </span>
                        <span class="text-[10px] text-center {{ $printer_paper_size === $size ? 'text-sky-500' : 'text-slate-400' }}">
                            {{ $desc }}
                        </span>
                        @if ($printer_paper_size === $size)
                            <span class="absolute top-2 right-2 flex items-center justify-center size-5 rounded-full bg-sky-600 text-white text-[10px] font-bold">✓</span>
                        @endif
                    </button>
                @endforeach
            </div>
        </div>

        {{-- Auto Print --}}
        <div class="card p-5 border border-slate-200/90 shadow-xs space-y-4">
            <div>
                <h4 class="text-sm font-bold text-slate-800">Auto Print Struk</h4>
                <p class="text-[11px] text-slate-400 mt-0.5">Cetak otomatis setelah order disimpan</p>
            </div>
            <div class="flex items-center justify-between p-4 rounded-xl border transition-all
                        {{ $printer_auto_print ? 'bg-sky-50 border-sky-200' : 'bg-slate-50 border-slate-200' }}">
                <div class="pr-4">
                    <p class="text-xs font-semibold text-slate-700">Cetak Saat Proses Order</p>
                    <p class="text-[11px] text-slate-500 mt-0.5 leading-relaxed">Nota langsung keluar dari printer saat kasir menekan "Proses Order".</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer shrink-0">
                    <input type="checkbox" wire:model.live="printer_auto_print"
                           @change="$nextTick(() => @this.savePrinter())"
                           class="sr-only peer">
                    <div class="w-11 h-6 bg-slate-300 rounded-full peer
                                peer-checked:bg-sky-600
                                after:content-[''] after:absolute after:top-[2px] after:left-[2px]
                                after:bg-white after:border after:border-slate-300 after:rounded-full
                                after:h-5 after:w-5 after:transition-all
                                peer-checked:after:translate-x-5 peer-checked:after:border-white"></div>
                </label>
            </div>
            <p class="text-[11px] text-slate-400">
                Status: <strong class="{{ $printer_auto_print ? 'text-sky-600' : 'text-slate-500' }}">{{ $printer_auto_print ? 'Aktif — Cetak Otomatis' : 'Nonaktif — Cetak Manual' }}</strong>
            </p>
        </div>
    </div>

    {{-- ══ PETUNJUK ════════════════════════════════════════════════════════ --}}
    <div class="rounded-2xl border border-sky-100 bg-sky-50/60 p-4 space-y-3">
        <p class="text-[11px] font-bold text-sky-800 uppercase tracking-wider">📋 Panduan Menghubungkan Printer</p>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs text-sky-900">
            @foreach ([
                ['1.', 'Nyalakan printer thermal dan aktifkan Bluetooth di HP/laptop Anda.'],
                ['2.', 'Buka Pengaturan Bluetooth HP/laptop → cari nama printer → pairing (PIN: 0000 atau 1234).'],
                ['3.', 'Kembali ke sini dan tekan tombol biru "Scan & Cari Printer Bluetooth".'],
                ['4.', 'Browser akan menampilkan popup daftar device — pilih nama printer Anda.'],
                ['5.', 'Tekan "Test Print" untuk memastikan printer berfungsi.'],
                ['6.', 'Gunakan Chrome atau Edge. Safari & Firefox tidak mendukung Web Bluetooth.'],
            ] as [$num, $step])
                <div class="flex gap-2">
                    <span class="font-bold text-sky-600 shrink-0">{{ $num }}</span>
                    <span>{{ $step }}</span>
                </div>
            @endforeach
        </div>
    </div>

    {{-- ══ MODAL DAFTAR PRINTER — di-teleport ke body ══════════════════ --}}
    <template x-teleport="body">
        <div
            x-show="showModal"
            x-cloak
            x-transition:enter="transition ease-out duration-150"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-100"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-[9999] flex items-end sm:items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm"
            @click="closeModal()"
            @keydown.escape.window="closeModal()"
        >
            <div
                class="bg-white w-full max-w-sm rounded-3xl shadow-2xl overflow-hidden"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                @click.stop
            >
                {{-- Header Modal --}}
                <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
                    <div>
                        <h3 class="text-base font-bold text-slate-800">Pilih Printer</h3>
                        <p class="text-xs text-slate-400 mt-0.5"
                           x-text="devices.length > 0
                               ? devices.length + ' perangkat ditemukan — tap untuk terhubung'
                               : 'Mencari printer di sekitar…'"></p>
                    </div>
                    <button
                        type="button"
                        @click="closeModal()"
                        class="size-9 flex items-center justify-center rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-500 hover:text-slate-700 transition-colors"
                        title="Tutup"
                    >
                        <svg class="size-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                {{-- Isi Modal --}}
                <div class="p-4 space-y-2 max-h-[60vh] overflow-y-auto">

                    {{-- Loading state --}}
                    <template x-if="scanning && devices.length === 0">
                        <div class="py-12 flex flex-col items-center gap-3 text-slate-400">
                            <svg class="size-8 animate-spin text-sky-500" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                            </svg>
                            <div class="text-center">
                                <p class="text-sm font-semibold text-slate-600">Mencari Perangkat…</p>
                                <p class="text-xs mt-1">Pilih printer di popup browser yang muncul</p>
                            </div>
                        </div>
                    </template>

                    {{-- Empty state --}}
                    <template x-if="!scanning && devices.length === 0">
                        <div class="py-12 flex flex-col items-center gap-3 text-slate-400">
                            <span class="text-4xl">🖨️</span>
                            <div class="text-center">
                                <p class="text-sm font-semibold text-slate-600">Tidak ada printer ditemukan</p>
                                <p class="text-xs mt-1">Pastikan printer sudah di-pairing di Bluetooth HP/laptop</p>
                            </div>
                        </div>
                    </template>

                    {{-- Daftar device --}}
                    <template x-for="d in devices" :key="d.address || d.name">
                        <button
                            type="button"
                            :disabled="connecting"
                            @click="connect(d)"
                            class="w-full flex items-center gap-4 p-4 rounded-2xl border border-slate-100 bg-slate-50/80
                                   hover:bg-sky-50 hover:border-sky-200 transition-all active:scale-[0.98] text-left
                                   disabled:opacity-60 disabled:cursor-wait group"
                        >
                            <div class="size-11 rounded-2xl bg-sky-50 border border-sky-100 text-sky-600
                                        group-hover:bg-sky-100 flex items-center justify-center shrink-0 transition-colors">
                                <svg class="size-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0 1 10.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0 .229 2.523a1.125 1.125 0 0 1-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0 0 21 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 0 0-1.913-.247M6.34 18H5.25A2.25 2.25 0 0 1 3 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 0 1 1.913-.247m10.5 0a48.536 48.536 0 0 0-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5Zm-3 0h.008v.008H15V10.5Z"/>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="font-bold text-slate-800 truncate text-sm" x-text="d.name || 'Thermal Printer'"></p>
                                <p class="text-[10px] text-slate-400 font-mono mt-0.5" x-text="d.address || d.type || 'Bluetooth'"></p>
                            </div>
                            <div class="shrink-0">
                                <template x-if="!connecting">
                                    <svg class="size-5 text-slate-300 group-hover:text-sky-400 transition-colors" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m9 18 6-6-6-6"/>
                                    </svg>
                                </template>
                                <template x-if="connecting">
                                    <svg class="size-4 animate-spin text-sky-500" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                                    </svg>
                                </template>
                            </div>
                        </button>
                    </template>
                </div>

                {{-- Footer Modal --}}
                <div class="px-5 py-3.5 bg-slate-50/80 border-t border-slate-100 flex items-center justify-between gap-3">
                    <p class="text-[11px] text-slate-400">Tidak ada dalam daftar? Pastikan printer sudah di-pairing di Bluetooth.</p>
                    <button type="button" @click="scan()"
                            :disabled="scanning"
                            class="shrink-0 flex items-center gap-1.5 px-4 py-2 rounded-xl bg-white border border-slate-200
                                   text-xs font-bold text-slate-600 hover:text-sky-600 hover:border-sky-200 transition-all disabled:opacity-50">
                        <svg class="size-3.5" :class="scanning ? 'animate-spin' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 0 0 4.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 0 1-15.357-2m15.357 2H15"/>
                        </svg>
                        Scan Ulang
                    </button>
                </div>
            </div>
        </div>
    </template>
</div>

{{-- ══ ALPINE COMPONENT ════════════════════════════════════════════════════ --}}
<script>
function printerPage() {
    return {
        // ── Reactive state ──────────────────────────────────────────────────
        connected:  false,
        connecting: false,
        scanning:   false,
        showModal:  false,
        devName:    '',
        connType:   '',
        devices:    [],
        errMsg:     '',

        // ── Boot: baca localStorage + sinkron dengan singleton ──────────────
        boot() {
            this.connected = localStorage.getItem('tp_connected') === 'true';
            this.devName   = localStorage.getItem('tp_name')  || '{{ $printer_device_name ?? '' }}';
            this.connType  = localStorage.getItem('tp_type')  || '{{ $printer_type ?? 'SYSTEM' }}';

            // Sinkron dari singleton thermalPrinter (jika sudah auto-reconnect)
            const tp = window.thermalPrinter;
            if (tp) {
                if (tp.connected) {
                    this.connected = true;
                    if (tp.device?.name) this.devName = tp.device.name;
                }
                tp.addEventListener('connection-changed', (e) => {
                    this.connected = !!e.detail;
                    if (!e.detail) { this.devName = ''; this.connType = ''; }
                });
            }
        },

        // ── SCAN ─────────────────────────────────────────────────────────────
        async scan() {
            this.errMsg = '';
            const tp = window.thermalPrinter;

            // ── Android APK (Native Bridge) ──────────────────────────────────
            if (tp?.isAndroidApp?.()) {
                this.scanning = true;
                this.devices  = [];
                this.showModal = true;
                try {
                    const list = await tp.scan();
                    this.devices = list || [];
                } catch (e) {
                    this.errMsg = 'Scan gagal: ' + (e.message || String(e));
                    this.showModal = false;
                } finally {
                    this.scanning = false;
                }
                return;
            }

            // ── Validasi Web Bluetooth support ───────────────────────────────
            if (!('bluetooth' in navigator)) {
                const onHttp = location.protocol === 'http:'
                    && location.hostname !== 'localhost'
                    && location.hostname !== '127.0.0.1';
                this.errMsg = onHttp
                    ? 'Web Bluetooth tidak aktif di HTTP. Buka halaman ini via HTTPS atau localhost.'
                    : 'Browser tidak mendukung Web Bluetooth. Gunakan Chrome atau Edge versi terbaru.';
                return;
            }

            // ── Cek previously-paired devices dulu (tanpa popup baru) ────────
            if (navigator.bluetooth.getDevices) {
                try {
                    const paired = await navigator.bluetooth.getDevices();
                    if (paired.length > 0) {
                        this.devices = paired.map(d => ({
                            name:        d.name || 'Printer',
                            address:     d.id,
                            type:        'bluetooth',
                            isWebDevice: true,
                            rawDevice:   d,
                        }));
                        this.showModal = true;
                        return;
                    }
                } catch (_) { /* lanjut ke picker */ }
            }

            // ── Buka native browser Bluetooth picker ─────────────────────────
            this.scanning  = true;
            this.showModal = true;
            this.devices   = [];
            try {
                const device = await navigator.bluetooth.requestDevice({
                    acceptAllDevices: true,
                    optionalServices: [
                        '000018f0-0000-1000-8000-00805f9b34fb',
                        '0000ffe0-0000-1000-8000-00805f9b34fb',
                        '49535343-fe7d-4ae5-8fa9-9fafd205e455',
                        'e7810a71-73ae-499d-8c15-faa9aef0c3f2',
                        '0000ff00-0000-1000-8000-00805f9b34fb',
                        '00001101-0000-1000-8000-00805f9b34fb',
                        '00001800-0000-1000-8000-00805f9b34fb',
                        '00001801-0000-1000-8000-00805f9b34fb',
                    ],
                });
                // User sudah pilih device dari picker browser → langsung connect
                this.devices   = [{ name: device.name || 'Printer', address: device.id, type: 'bluetooth', rawDevice: device, isWebDevice: true }];
                this.scanning  = false;
                await this.connect(this.devices[0]);
            } catch (e) {
                this.scanning  = false;
                this.showModal = false;
                // User cancel = NotFoundError atau pesan 'cancelled' → tidak tampilkan error
                if (e.name !== 'NotFoundError' && !String(e).toLowerCase().includes('cancel')) {
                    this.errMsg = e.message || String(e);
                }
            }
        },

        // ── CONNECT ke device spesifik ────────────────────────────────────────
        async connect(device) {
            this.connecting = true;
            this.errMsg     = '';
            try {
                const tp = window.thermalPrinter;
                if (tp) {
                    await tp.connect(device);
                }
                await this._onConnected(device.name || 'Printer Bluetooth', 'BLUETOOTH');
                this.closeModal();
            } catch (e) {
                this.errMsg    = 'Gagal terhubung: ' + (e.message || String(e));
                this.showModal = false;
            } finally {
                this.connecting = false;
            }
        },

        // ── USB Serial ────────────────────────────────────────────────────────
        async connectUsb() {
            if (!('serial' in navigator)) {
                this.errMsg = 'USB Serial tidak didukung. Gunakan Chrome atau Edge desktop via HTTPS/localhost.';
                return;
            }
            this.errMsg = '';
            try {
                const port = await navigator.serial.requestPort();
                await port.open({ baudRate: 9600 });
                window._printerSerialPort = port;
                await this._onConnected('USB Thermal Printer', 'USB');
            } catch (e) {
                if (!String(e).toLowerCase().includes('cancel') && e.name !== 'NotFoundError') {
                    this.errMsg = 'USB gagal: ' + (e.message || String(e));
                }
            }
        },

        // ── Gunakan dialog print browser ──────────────────────────────────────
        async useSystem() {
            this.connected = false;
            this.devName   = 'Dialog Browser';
            this.connType  = 'SYSTEM';
            localStorage.setItem('tp_type', 'SYSTEM');
            localStorage.removeItem('tp_connected');
            localStorage.removeItem('tp_name');
            await @this.updatePairedDevice('SYSTEM', '');
            this._toast('info', 'Mode Browser', 'Struk akan dicetak lewat dialog cetak browser.');
        },

        // ── Disconnect ────────────────────────────────────────────────────────
        async disconnect() {
            try { await window.thermalPrinter?.disconnect(); } catch (_) {}
            this.connected = false;
            this.devName   = '';
            this.connType  = '';
            this.devices   = [];
            localStorage.removeItem('tp_connected');
            localStorage.removeItem('tp_name');
            localStorage.removeItem('tp_type');
            await @this.updatePairedDevice('SYSTEM', '');
            this._toast('info', 'Terputus', 'Koneksi printer telah dilepas.');
        },

        // ── Test Print ────────────────────────────────────────────────────────
        async testPrint() {
            const paper = '{{ $printer_paper_size ?? '58mm' }}';
            const tp    = window.thermalPrinter;

            // Android native
            if (tp?.isAndroidApp?.() && tp.connected) {
                try {
                    const cols = paper === '80mm' ? 48 : 32;
                    const sep  = '-'.repeat(cols);
                    window.AndroidInterface.printFormattedText(
                        `[C]<b>KEE LAUNDRY</b>\n[C]Test Print OK\n[C]${sep}\n` +
                        `[L]Tanggal: ${new Date().toLocaleString('id-ID')}\n` +
                        `[L]Printer: ${this.devName}\n[L]Lebar  : ${paper}\n` +
                        `[C]${sep}\n[C]<b>PRINTER BERHASIL!</b>\n\n\n`
                    );
                    this._toast('success', 'Test Print Terkirim', 'Cek struk fisik pada printer.');
                    return;
                } catch (_) {}
            }

            // Web Bluetooth
            if (tp?.connected && tp.characteristic) {
                try {
                    await tp.testPrint();
                    this._toast('success', 'Test Print Terkirim', 'Cek struk fisik pada printer.');
                    return;
                } catch (e) {
                    this._toast('error', 'Print Gagal', e.message || String(e));
                    return;
                }
            }

            // Fallback: dialog browser
            this._browserPrint(paper);
        },

        // ── Browser print fallback ────────────────────────────────────────────
        _browserPrint(paper) {
            const w   = paper === '80mm' ? '302px' : '218px';
            const now = new Date().toLocaleString('id-ID');
            const html = `<!DOCTYPE html><html><head><title>Test Print</title>
<style>@page{size:${paper} auto;margin:4mm}body{font-family:Courier New,monospace;width:${w};margin:0 auto;font-size:11px;line-height:1.5;color:#000}.c{text-align:center}.b{font-weight:bold}hr{border:none;border-top:1px dashed #000;margin:4px 0}</style>
</head><body>
<div class="c b" style="font-size:14px">KEE LAUNDRY</div>
<div class="c">Test Print — Printer OK</div><hr>
<div>Tanggal : ${now}</div><div>Lebar   : ${paper}</div>
<div>Status  : <b>BERHASIL ✓</b></div><hr>
<div class="c b">Terima Kasih!</div><br><br></body></html>`;
            const win = window.open('', '_blank', `width=${paper === '80mm' ? 380 : 300},height=430`);
            if (win) {
                win.document.write(html);
                win.document.close();
                win.focus();
                setTimeout(() => { win.print(); setTimeout(() => win.close(), 1200); }, 400);
            } else {
                this.errMsg = 'Popup diblokir browser. Izinkan popup untuk mencetak.';
            }
        },

        // ── Tutup modal ────────────────────────────────────────────────────────
        closeModal() {
            this.showModal  = false;
            this.scanning   = false;
            this.connecting = false;
        },

        // ── Setelah connected berhasil ─────────────────────────────────────────
        async _onConnected(name, type) {
            this.connected = true;
            this.devName   = name;
            this.connType  = type;
            localStorage.setItem('tp_connected', 'true');
            localStorage.setItem('tp_name', name);
            localStorage.setItem('tp_type', type);
            await @this.updatePairedDevice(type, name);
            this._toast('success', 'Printer Terhubung! 🖨️', name + ' siap mencetak struk.');
        },

        // ── Toast helper ────────────────────────────────────────────────────────
        _toast(type, title, message) {
            window.showToast
                ? window.showToast({ type, title, message })
                : console.info('[printer]', type, title, message);
        },
    };
}
</script>
