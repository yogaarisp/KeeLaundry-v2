{{-- Printer Thermal — Rebuild Total, Simple & Bersih --}}
<div class="space-y-4" x-data="printerSetup()" x-init="init($wire)">

    {{-- ══ KARTU UTAMA ══════════════════════════════════════════════════ --}}
    <div class="card p-6 border border-slate-200 shadow-xs space-y-5">

        {{-- Header --}}
        <div class="flex items-center gap-3 pb-4 border-b border-slate-100">
            <div class="size-10 rounded-xl flex items-center justify-center shrink-0 border"
                 :class="status === 'connected'
                    ? 'bg-emerald-50 border-emerald-200 text-emerald-600'
                    : 'bg-slate-50 border-slate-200 text-slate-400'">
                <svg class="size-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0 1 10.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0 .229 2.523a1.125 1.125 0 0 1-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0 0 21 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 0 0-1.913-.247M6.34 18H5.25A2.25 2.25 0 0 1 3 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 0 1 1.913-.247m10.5 0a48.536 48.536 0 0 0-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5Zm-3 0h.008v.008H15V10.5Z"/>
                </svg>
            </div>
            <div class="flex-1">
                <h3 class="text-sm font-bold text-slate-800">Printer Thermal</h3>
                <p class="text-xs text-slate-400 mt-0.5"
                   x-text="status === 'connected'
                       ? '✓ Terhubung: ' + deviceName
                       : 'Belum ada printer yang terhubung'">
                    {{ $printer_device_name ? '✓ Tersimpan: '.$printer_device_name : 'Belum ada printer yang terhubung' }}
                </p>
            </div>
            {{-- Badge status --}}
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold border shrink-0"
                  :class="status === 'connected'
                      ? 'bg-emerald-50 border-emerald-200 text-emerald-700'
                      : 'bg-slate-100 border-slate-200 text-slate-500'">
                <span class="size-1.5 rounded-full"
                      :class="status === 'connected' ? 'bg-emerald-500 animate-pulse' : 'bg-slate-400'"></span>
                <span x-text="status === 'connected' ? 'Online' : 'Offline'">
                    {{ $printer_device_name ? 'Offline' : 'Offline' }}
                </span>
            </span>
        </div>

        {{-- Tombol SCAN UTAMA --}}
        <button
            type="button"
            @click="openScan()"
            :disabled="status === 'scanning' || status === 'connecting'"
            class="w-full min-h-[54px] rounded-2xl font-bold text-sm flex items-center justify-center gap-3
                   transition-all shadow-sm
                   disabled:opacity-60 disabled:cursor-wait
                   focus:outline-none focus:ring-2 focus:ring-sky-400 focus:ring-offset-2"
            :class="status === 'connected'
                ? 'bg-emerald-600 hover:bg-emerald-700 text-white'
                : 'bg-sky-600 hover:bg-sky-700 text-white'"
        >
            {{-- Ikon scan --}}
            <template x-if="status !== 'scanning' && status !== 'connecting'">
                <svg class="size-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.348 14.652a3.75 3.75 0 0 1 0-5.304m5.304 0a3.75 3.75 0 0 1 0 5.304m-7.425 2.121a6.75 6.75 0 0 1 0-9.546m9.546 0a6.75 6.75 0 0 1 0 9.546M5.106 18.894c-3.808-3.808-3.808-9.98 0-13.789m13.788 0c3.808 3.808 3.808 9.98 0 13.788M12 12h.008v.008H12V12Z"/>
                </svg>
            </template>
            {{-- Ikon spinner --}}
            <template x-if="status === 'scanning' || status === 'connecting'">
                <svg class="size-5 shrink-0 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                </svg>
            </template>
            <span x-text="{
                idle: 'Scan Printer Bluetooth di Sekitar',
                scanning: 'Sedang Mencari Printer…',
                connecting: 'Menghubungkan…',
                connected: '✓ Terhubung — Scan Ulang',
                error: 'Coba Scan Lagi'
            }[status] || 'Scan Printer Bluetooth'">
                Scan Printer Bluetooth di Sekitar
            </span>
        </button>

        {{-- Pesan error --}}
        <div x-show="errMsg" x-cloak x-transition
             class="flex items-start gap-2.5 p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-xs text-rose-700">
            <svg class="size-4 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z"/>
            </svg>
            <span x-text="errMsg"></span>
        </div>

        {{-- Link alternatif (kecil, tidak mencolok) --}}
        <div class="flex items-center justify-center gap-4 text-xs text-slate-400 pt-1">
            <button type="button" @click="connectUsb()"
                    class="hover:text-sky-600 transition-colors underline decoration-dashed underline-offset-2">
                Pakai USB/Serial
            </button>
            <span>·</span>
            <button type="button" @click="useSystemDialog()"
                    class="hover:text-sky-600 transition-colors underline decoration-dashed underline-offset-2">
                Cetak via Dialog Browser
            </button>
            <template x-if="status === 'connected'">
                <span>·</span>
            </template>
            <template x-if="status === 'connected'">
                <button type="button" @click="doDisconnect()"
                        class="hover:text-rose-500 transition-colors underline decoration-dashed underline-offset-2 text-rose-400">
                    Putuskan
                </button>
            </template>
        </div>
    </div>

    {{-- ══ PANEL TERHUBUNG: INFO + TEST PRINT ══════════════════════════ --}}
    <div x-show="status === 'connected'" x-cloak x-transition
         class="card p-5 border border-emerald-200 bg-emerald-50/40 shadow-xs">
        <div class="flex items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="size-10 rounded-xl bg-emerald-100 border border-emerald-200 text-emerald-600 flex items-center justify-center shrink-0">
                    <svg class="size-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0 1 10.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0 .229 2.523a1.125 1.125 0 0 1-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0 0 21 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 0 0-1.913-.247M6.34 18H5.25A2.25 2.25 0 0 1 3 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 0 1 1.913-.247m10.5 0a48.536 48.536 0 0 0-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5Zm-3 0h.008v.008H15V10.5Z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-bold text-emerald-800" x-text="deviceName">Printer</p>
                    <p class="text-[11px] text-emerald-600" x-text="connType + ' · Siap mencetak'">Bluetooth · Siap mencetak</p>
                </div>
            </div>
            <button type="button" @click="doTestPrint()"
                    class="flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white border border-emerald-200
                           text-sm font-bold text-emerald-700 hover:bg-emerald-100 transition-colors shadow-2xs shrink-0">
                <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0 1 10.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0 .229 2.523a1.125 1.125 0 0 1-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0 0 21 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 0 0-1.913-.247M6.34 18H5.25A2.25 2.25 0 0 1 3 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 0 1 1.913-.247m10.5 0a48.536 48.536 0 0 0-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5Zm-3 0h.008v.008H15V10.5Z"/>
                </svg>
                Test Print
            </button>
        </div>
    </div>

    {{-- ══ UKURAN KERTAS + AUTO-PRINT ══════════════════════════════════ --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        {{-- Ukuran kertas --}}
        <div class="card p-5 border border-slate-200 shadow-xs space-y-3">
            <p class="text-sm font-bold text-slate-700">Ukuran Kertas</p>
            <div class="grid grid-cols-2 gap-2.5">
                @foreach(['58mm' => ['Standard', 'Printer mini POS'], '80mm' => ['Large', 'Printer meja lebar']] as $s => [$lbl, $sub])
                <button type="button"
                        wire:click="$set('printer_paper_size','{{ $s }}')"
                        @click="$nextTick(() => @this.savePrinter())"
                        class="relative flex flex-col items-center gap-0.5 py-4 px-3 rounded-2xl border-2 transition-all
                               {{ $printer_paper_size === $s
                                  ? 'border-sky-500 bg-sky-50 text-sky-700'
                                  : 'border-slate-200 bg-white text-slate-500 hover:border-slate-300' }}">
                    <span class="text-xl font-black">{{ $s }}</span>
                    <span class="text-[10px] font-bold uppercase tracking-wider">{{ $lbl }}</span>
                    <span class="text-[9px] text-center opacity-70">{{ $sub }}</span>
                    @if($printer_paper_size === $s)
                    <span class="absolute top-2 right-2 size-5 bg-sky-500 rounded-full text-white text-[10px] flex items-center justify-center font-bold">✓</span>
                    @endif
                </button>
                @endforeach
            </div>
        </div>

        {{-- Auto Print --}}
        <div class="card p-5 border border-slate-200 shadow-xs space-y-3">
            <div class="flex items-center justify-between">
                <p class="text-sm font-bold text-slate-700">Auto Print</p>
                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full
                             {{ $printer_auto_print ? 'bg-sky-100 text-sky-700' : 'bg-slate-100 text-slate-500' }}">
                    {{ $printer_auto_print ? 'Aktif' : 'Nonaktif' }}
                </span>
            </div>
            <div class="flex items-center gap-3 p-4 rounded-xl border transition-all
                        {{ $printer_auto_print ? 'bg-sky-50 border-sky-200' : 'bg-slate-50 border-slate-200' }}">
                <div class="flex-1">
                    <p class="text-xs font-semibold text-slate-700">Cetak otomatis setelah order disimpan</p>
                    <p class="text-[11px] text-slate-500 mt-0.5">Nota langsung keluar saat kasir tekan "Proses Order"</p>
                </div>
                <label class="relative inline-flex cursor-pointer shrink-0">
                    <input type="checkbox" wire:model.live="printer_auto_print"
                           @change="$nextTick(() => @this.savePrinter())" class="sr-only peer">
                    <div class="w-11 h-6 rounded-full bg-slate-300 transition-all
                                peer-checked:bg-sky-600
                                after:absolute after:top-0.5 after:left-0.5
                                after:bg-white after:rounded-full after:size-5 after:transition-all after:content-['']
                                peer-checked:after:translate-x-5"></div>
                </label>
            </div>
        </div>
    </div>

    {{-- ══ PANDUAN SINGKAT ═════════════════════════════════════════════ --}}
    <div class="rounded-xl border border-sky-100 bg-sky-50 p-4 text-xs text-sky-800 space-y-1.5">
        <p class="font-bold text-sky-900">💡 Cara menghubungkan printer:</p>
        <ol class="list-decimal list-inside space-y-1 text-[11px]">
            <li>Nyalakan printer, aktifkan Bluetooth di HP/laptop</li>
            <li>Pairing printer di pengaturan Bluetooth HP dulu (PIN: 0000 atau 1234)</li>
            <li>Klik tombol biru <strong>"Scan Printer"</strong> di atas</li>
            <li>Pilih nama printer dari daftar yang muncul</li>
            <li>Tekan <strong>"Test Print"</strong> untuk verifikasi</li>
        </ol>
        <p class="text-[10px] text-sky-600 pt-1">✦ Gunakan Google Chrome atau Microsoft Edge · Safari tidak mendukung Web Bluetooth</p>
    </div>

    {{-- ══ MODAL DAFTAR PRINTER (x-teleport ke body) ════════════════ --}}
    <template x-teleport="body">
        <div
            x-show="modal"
            x-cloak
            class="fixed inset-0 z-[9999] flex items-end sm:items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            @click.self="modal = false; status = (status === 'scanning' ? 'idle' : status)"
            @keydown.escape.window="modal = false; status = (status === 'scanning' ? 'idle' : status)"
        >
            <div
                class="bg-white w-full max-w-sm rounded-3xl shadow-2xl overflow-hidden"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 translate-y-4 scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                @click.stop
            >
                {{-- Header --}}
                <div class="flex items-center justify-between px-5 pt-5 pb-4 border-b border-slate-100">
                    <div>
                        <h3 class="text-base font-bold text-slate-800">Pilih Printer</h3>
                        <p class="text-xs text-slate-400 mt-0.5"
                           x-text="devices.length
                               ? devices.length + ' printer ditemukan'
                               : (status === 'scanning' ? 'Sedang mencari…' : 'Tap printer untuk konek')">
                        </p>
                    </div>
                    {{-- ✕ CLOSE --}}
                    <button
                        type="button"
                        @click="modal = false; if(status === 'scanning' || status === 'connecting') status = 'idle'"
                        class="size-9 rounded-xl bg-slate-100 hover:bg-slate-200 flex items-center justify-center
                               text-slate-500 hover:text-slate-800 transition-colors"
                    >
                        <svg class="size-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                {{-- Body --}}
                <div class="p-4 max-h-[55vh] overflow-y-auto space-y-2">

                    {{-- Scanning spinner --}}
                    <template x-if="status === 'scanning' && !devices.length">
                        <div class="py-14 flex flex-col items-center gap-4">
                            <div class="relative">
                                <div class="size-16 rounded-full bg-sky-50 border-2 border-sky-200 flex items-center justify-center">
                                    <svg class="size-7 text-sky-500 animate-spin" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                                    </svg>
                                </div>
                                <span class="absolute -top-1 -right-1 size-4 rounded-full bg-sky-400 animate-ping"></span>
                            </div>
                            <div class="text-center">
                                <p class="text-sm font-bold text-slate-700">Memilih Printer…</p>
                                <p class="text-xs text-slate-400 mt-1">Pilih printer di popup browser yang muncul</p>
                            </div>
                        </div>
                    </template>

                    {{-- Connecting --}}
                    <template x-if="status === 'connecting'">
                        <div class="py-14 flex flex-col items-center gap-4">
                            <div class="size-16 rounded-full bg-sky-50 border-2 border-sky-200 flex items-center justify-center">
                                <svg class="size-7 text-sky-500 animate-spin" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                                </svg>
                            </div>
                            <div class="text-center">
                                <p class="text-sm font-bold text-slate-700">Menghubungkan…</p>
                                <p class="text-xs text-slate-400 mt-1" x-text="'Menyambungkan ke ' + (connectingTo || 'printer')"></p>
                            </div>
                        </div>
                    </template>

                    {{-- Daftar device --}}
                    <template x-if="devices.length && status !== 'connecting'">
                        <div class="space-y-2">
                            <template x-for="d in devices" :key="d.id">
                                <button
                                    type="button"
                                    @click="doConnect(d)"
                                    class="w-full flex items-center gap-3.5 p-4 rounded-2xl border border-slate-100
                                           bg-slate-50 hover:bg-sky-50 hover:border-sky-200
                                           transition-all active:scale-[0.98] text-left group"
                                >
                                    <div class="size-11 rounded-xl bg-white border border-slate-200
                                                group-hover:bg-sky-100 group-hover:border-sky-200
                                                flex items-center justify-center shrink-0 transition-colors text-slate-400 group-hover:text-sky-600">
                                        <svg class="size-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0 1 10.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0 .229 2.523a1.125 1.125 0 0 1-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0 0 21 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 0 0-1.913-.247M6.34 18H5.25A2.25 2.25 0 0 1 3 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 0 1 1.913-.247m10.5 0a48.536 48.536 0 0 0-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5Zm-3 0h.008v.008H15V10.5Z"/>
                                        </svg>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="font-bold text-slate-800 text-sm truncate" x-text="d.label"></p>
                                        <p class="text-[10px] text-slate-400 font-mono mt-0.5" x-text="d.sub || 'Bluetooth'"></p>
                                    </div>
                                    <svg class="size-4 text-slate-300 group-hover:text-sky-400 shrink-0 transition-colors" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m9 18 6-6-6-6"/>
                                    </svg>
                                </button>
                            </template>
                        </div>
                    </template>
                </div>

                {{-- Footer --}}
                <div class="px-5 py-3.5 bg-slate-50 border-t border-slate-100 flex items-center justify-between gap-3">
                    <p class="text-[11px] text-slate-400 leading-relaxed">Tidak ada? Pairing dulu di <strong>Pengaturan Bluetooth</strong> HP/laptop.</p>
                    <button type="button" @click="openScan()"
                            :disabled="status === 'scanning' || status === 'connecting'"
                            class="shrink-0 px-4 py-2 rounded-xl bg-white border border-slate-200 text-xs font-bold
                                   text-slate-600 hover:text-sky-600 hover:border-sky-300 transition-all disabled:opacity-50">
                        Scan Ulang
                    </button>
                </div>
            </div>
        </div>
    </template>
</div>

