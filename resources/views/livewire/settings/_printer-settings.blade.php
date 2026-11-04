<div class="space-y-6" x-data="thermalPrinterManager()" x-init="init()">

    {{-- ══ KARTU UTAMA: STATUS PRINTER & KONEKSI (Hero Card) ═════════════ --}}
    <div class="card p-5 sm:p-6 space-y-5 shadow-xs border border-slate-200/90">
        {{-- Header Bar --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-4 border-b border-slate-100 gap-3">
            <div class="flex items-center gap-3">
                <div class="size-10 rounded-2xl bg-sky-50 border border-sky-200/80 text-sky-600 flex items-center justify-center shrink-0 shadow-2xs">
                    <svg class="size-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0 1 10.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0 .229 2.523a1.125 1.125 0 0 1-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0 0 21 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 0 0-1.913-.247M6.34 18H5.25A2.25 2.25 0 0 1 3 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 0 1 1.913-.247m10.5 0a48.536 48.536 0 0 0-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5Zm-3 0h.008v.008H15V10.5Z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-base font-bold text-slate-800 leading-tight">Printer Thermal POS</h3>
                    <p class="text-xs text-slate-400 mt-0.5">Koneksi printer kasir via Bluetooth, USB kabel, atau System Driver</p>
                </div>
            </div>

            {{-- Status Badge Kanan Atas --}}
            <div class="flex items-center gap-2 self-start sm:self-auto">
                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full border text-xs font-bold transition-all shadow-2xs"
                     :class="connectionStatus === 'connected' ? 'bg-emerald-50 border-emerald-200 text-emerald-700' : 'bg-slate-100 border-slate-200 text-slate-600'">
                    <span class="size-2 rounded-full shrink-0"
                          :class="connectionStatus === 'connected' ? 'bg-emerald-500 animate-pulse' : 'bg-slate-400'"></span>
                    <span x-show="connectionStatus === 'connected'">Terhubung</span>
                    <span x-show="connectionStatus !== 'connected'">
                        {{ $printer_device_name ? 'Tersimpan: ' . $printer_device_name : 'Belum Terhubung' }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Kartu Info Perangkat Saat Ini --}}
        <div class="p-4 sm:p-5 rounded-2xl border transition-all"
             :class="connectionStatus === 'connected' ? 'bg-emerald-50/40 border-emerald-200/80' : 'bg-slate-50/80 border-slate-200'">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-center gap-3.5">
                    <div class="size-12 rounded-2xl flex items-center justify-center shrink-0 border transition-all shadow-2xs"
                         :class="connectionStatus === 'connected' ? 'bg-emerald-100 border-emerald-200 text-emerald-600' : 'bg-white border-slate-200 text-slate-400'">
                        <svg class="size-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0 1 10.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0 .229 2.523a1.125 1.125 0 0 1-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0 0 21 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 0 0-1.913-.247M6.34 18H5.25A2.25 2.25 0 0 1 3 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 0 1 1.913-.247m10.5 0a48.536 48.536 0 0 0-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5Zm-3 0h.008v.008H15V10.5Z"/>
                        </svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Status Perangkat</span>
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-md uppercase"
                                  :class="connectionStatus === 'connected' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-200 text-slate-600'">
                                {{ $printer_type }}
                            </span>
                        </div>
                        <h4 class="text-sm sm:text-base font-extrabold text-slate-800 mt-0.5">
                            <span x-show="connectionStatus === 'connected'" x-text="deviceName || 'Printer Bluetooth Siap'">
                                {{ $printer_device_name ?: 'Printer Bluetooth Siap' }}
                            </span>
                            <span x-show="connectionStatus !== 'connected'">
                                {{ $printer_device_name ? 'Tersimpan: ' . $printer_device_name : 'Belum Ada Printer Terhubung' }}
                            </span>
                        </h4>
                        <p class="text-xs text-slate-500 mt-0.5">
                            <span x-show="connectionStatus === 'connected'">Printer aktif dan siap mencetak nota kasir secara instan.</span>
                            <span x-show="connectionStatus !== 'connected'">Tekan tombol Scan &amp; Hubungkan di bawah untuk mendeteksi printer kasir.</span>
                        </p>
                    </div>
                </div>

                {{-- Tombol Putuskan --}}
                <div class="shrink-0 flex items-center gap-2">
                    <button x-show="connectionStatus === 'connected'"
                            x-cloak
                            @click="disconnect()"
                            type="button"
                            class="px-3.5 py-2 rounded-xl text-xs font-bold text-rose-600 hover:text-rose-700 bg-white hover:bg-rose-50 border border-rose-200 shadow-2xs transition-colors">
                        Putuskan Printer
                    </button>
                </div>
            </div>
        </div>

        {{-- Baris Tombol Aksi Utama --}}
        <div class="space-y-3 pt-1">
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                {{-- Tombol Scan Biru Utama (Besar, Tegas & Terbaca Jelas) --}}
                <button type="button"
                        @click="scanAndConnect()"
                        :disabled="isConnecting"
                        class="flex-1 min-h-[52px] bg-gradient-to-r from-sky-600 via-sky-600 to-blue-600 hover:from-sky-700 hover:to-blue-700 active:scale-[0.99] text-white font-bold text-sm px-6 py-3.5 rounded-2xl shadow-sm hover:shadow-md transition-all flex items-center justify-center gap-2.5 disabled:opacity-60 cursor-pointer">
                    <svg x-show="!isConnecting" class="size-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.348 14.652a3.75 3.75 0 0 1 0-5.304m5.304 0a3.75 3.75 0 0 1 0 5.304m-7.425 2.121a6.75 6.75 0 0 1 0-9.546m9.546 0a6.75 6.75 0 0 1 0 9.546M5.106 18.894c-3.808-3.808-3.808-9.98 0-13.789m13.788 0c3.808 3.808 3.808 9.98 0 13.788M12 12h.008v.008H12V12Z"/>
                    </svg>
                    <svg x-show="isConnecting" x-cloak class="size-5 animate-spin shrink-0" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                    </svg>
                    <span x-show="!isConnecting">Scan &amp; Hubungkan Printer Bluetooth</span>
                    <span x-show="isConnecting" x-cloak>Mencari Perangkat Bluetooth…</span>
                </button>

                {{-- Tombol Test Print --}}
                <button type="button"
                        @click="testPrint()"
                        class="min-h-[52px] px-6 py-3.5 rounded-2xl border border-slate-200 bg-white hover:bg-slate-50 active:bg-slate-100 text-slate-700 font-bold text-sm shadow-2xs transition-all flex items-center justify-center gap-2 shrink-0 cursor-pointer">
                    <svg class="size-4.5 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0 1 10.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0 .229 2.523a1.125 1.125 0 0 1-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0 0 21 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 0 0-1.913-.247M6.34 18H5.25A2.25 2.25 0 0 1 3 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 0 1 1.913-.247m10.5 0a48.536 48.536 0 0 0-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5Zm-3 0h.008v.008H15V10.5Z"/>
                    </svg>
                    <span>Test Print Struk</span>
                </button>
            </div>

            {{-- Opsi Alternatif: USB Kabel & System Dialog --}}
            <div class="flex flex-wrap items-center justify-between text-xs text-slate-500 pt-2 px-1 gap-2">
                <span class="text-slate-400 font-medium">Opsi koneksi lainnya:</span>
                <div class="flex items-center gap-3">
                    <button type="button" @click="pairUsb()" class="hover:text-sky-600 font-semibold underline decoration-slate-300 transition-colors flex items-center gap-1.5 cursor-pointer">
                        <span>🔌 Sambungkan via USB Kabel (PC)</span>
                    </button>
                    <span class="text-slate-300">•</span>
                    <button type="button" wire:click="$set('printer_type', 'SYSTEM')" @click="$nextTick(() => @this.savePrinter())" class="hover:text-sky-600 font-semibold underline decoration-slate-300 transition-colors flex items-center gap-1.5 cursor-pointer">
                        <span>💻 Gunakan Dialog Cetak Browser</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- ══ DUA KOLOM: UKURAN KERTAS & AUTO-PRINT ═══════════════════════ --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

        {{-- KOLOM KIRI: Ukuran Kertas Thermal --}}
        <div class="card p-5 sm:p-6 space-y-4 shadow-xs border border-slate-200/90">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div class="flex items-center gap-2">
                    <div class="size-7 rounded-lg bg-sky-50 border border-sky-200 text-sky-600 flex items-center justify-center shrink-0">
                        📄
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-slate-800">Ukuran Kertas Thermal</h4>
                        <p class="text-[11px] text-slate-400">Pilih lebar roll kertas yang Anda gunakan</p>
                    </div>
                </div>
                <span class="text-[10px] bg-slate-100 text-slate-600 font-bold px-2 py-0.5 rounded-full uppercase">
                    Aktif: {{ $printer_paper_size }}
                </span>
            </div>

            <div class="grid grid-cols-2 gap-3.5">
                {{-- 58mm Option --}}
                <button type="button"
                        wire:click="$set('printer_paper_size', '58mm')"
                        @click="$nextTick(() => @this.savePrinter())"
                        @class([
                            'relative p-4 rounded-2xl text-center transition-all flex flex-col items-center justify-center min-h-[105px] cursor-pointer',
                            'border-2 border-sky-600 bg-sky-50/30 shadow-xs' => $printer_paper_size === '58mm',
                            'border border-slate-200 bg-white hover:border-slate-300 hover:bg-slate-50' => $printer_paper_size !== '58mm',
                        ])>
                    <span @class([
                        'text-2xl sm:text-3xl font-black tracking-tight leading-none',
                        'text-sky-600' => $printer_paper_size === '58mm',
                        'text-slate-400' => $printer_paper_size !== '58mm',
                    ])>58mm</span>
                    <span @class([
                        'text-[10px] font-extrabold tracking-widest mt-1.5 uppercase',
                        'text-sky-600' => $printer_paper_size === '58mm',
                        'text-slate-400' => $printer_paper_size !== '58mm',
                    ])>STANDARD</span>
                    <span class="text-[10px] text-slate-400 mt-0.5 font-medium">Roll Mini POS</span>

                    @if ($printer_paper_size === '58mm')
                    <div class="absolute top-2.5 right-2.5 size-5 rounded-full bg-sky-600 text-white flex items-center justify-center text-xs font-bold shadow-2xs">
                        ✓
                    </div>
                    @endif
                </button>

                {{-- 80mm Option --}}
                <button type="button"
                        wire:click="$set('printer_paper_size', '80mm')"
                        @click="$nextTick(() => @this.savePrinter())"
                        @class([
                            'relative p-4 rounded-2xl text-center transition-all flex flex-col items-center justify-center min-h-[105px] cursor-pointer',
                            'border-2 border-sky-600 bg-sky-50/30 shadow-xs' => $printer_paper_size === '80mm',
                            'border border-slate-200 bg-white hover:border-slate-300 hover:bg-slate-50' => $printer_paper_size !== '80mm',
                        ])>
                    <span @class([
                        'text-2xl sm:text-3xl font-black tracking-tight leading-none',
                        'text-sky-600' => $printer_paper_size === '80mm',
                        'text-slate-400' => $printer_paper_size !== '80mm',
                    ])>80mm</span>
                    <span @class([
                        'text-[10px] font-extrabold tracking-widest mt-1.5 uppercase',
                        'text-sky-600' => $printer_paper_size === '80mm',
                        'text-slate-400' => $printer_paper_size !== '80mm',
                    ])>LARGE</span>
                    <span class="text-[10px] text-slate-400 mt-0.5 font-medium">Roll Kasir Lebar</span>

                    @if ($printer_paper_size === '80mm')
                    <div class="absolute top-2.5 right-2.5 size-5 rounded-full bg-sky-600 text-white flex items-center justify-center text-xs font-bold shadow-2xs">
                        ✓
                    </div>
                    @endif
                </button>
            </div>
            <p class="text-[11px] text-slate-400 text-center leading-relaxed">
                💡 Mayoritas printer portable kasir laundry menggunakan kertas ukuran <strong>58mm</strong>.
            </p>
        </div>

        {{-- KOLOM KANAN: Auto-Print Struk Kasir --}}
        <div class="card p-5 sm:p-6 space-y-4 shadow-xs border border-slate-200/90 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <div class="flex items-center gap-2">
                        <div class="size-7 rounded-lg bg-sky-50 border border-sky-200 text-sky-600 flex items-center justify-center shrink-0">
                            ⚡
                        </div>
                        <div>
                            <h4 class="text-sm font-bold text-slate-800">Auto Print Struk</h4>
                            <p class="text-[11px] text-slate-400">Otomasi cetak struk di menu kasir POS</p>
                        </div>
                    </div>
                    <span @class([
                        'text-[10px] font-bold px-2.5 py-0.5 rounded-full uppercase',
                        'bg-sky-100 text-sky-800' => $printer_auto_print,
                        'bg-slate-100 text-slate-500' => !$printer_auto_print,
                    ])>
                        {{ $printer_auto_print ? 'Aktif' : 'Nonaktif' }}
                    </span>
                </div>

                {{-- Toggle Box --}}
                <div class="flex items-center justify-between p-4 rounded-2xl border transition-all mt-3.5"
                     :class="$wire.printer_auto_print ? 'bg-sky-50/40 border-sky-200' : 'bg-slate-50/80 border-slate-200'">
                    <div class="space-y-0.5 pr-3">
                        <p class="text-xs font-bold text-slate-800">Cetak Otomatis Setelah Simpan Order</p>
                        <p class="text-[11px] text-slate-500 leading-relaxed">
                            Nota fisik langsung keluar dari printer saat kasir menekan tombol "Proses Order".
                        </p>
                    </div>

                    <label class="relative inline-flex items-center cursor-pointer shrink-0">
                        <input type="checkbox"
                               wire:model.live="printer_auto_print"
                               @change="$nextTick(() => @this.savePrinter())"
                               class="sr-only peer">
                        <div class="w-12 h-6 bg-slate-300 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-sky-600"></div>
                    </label>
                </div>
            </div>

            <p class="text-[11px] text-slate-400 leading-relaxed">
                Jika dinonaktifkan, kasir tetap dapat mencetak struk secara manual dengan menekan tombol <strong>"Cetak Struk"</strong> di pop-up nota.
            </p>
        </div>
    </div>

    {{-- ══ KOTAK BIRU: PETUNJUK & PANDUAN PENGGUNAAN PRINTER ═══════════════ --}}
    <div class="rounded-2xl border border-sky-100 bg-sky-50/80 p-5 shadow-2xs">
        <div class="flex items-start gap-3.5">
            <div class="size-8 rounded-full bg-sky-100 border border-sky-200 text-sky-700 flex items-center justify-center shrink-0 mt-0.5">
                <svg class="size-4.5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z"/>
                </svg>
            </div>
            <div class="space-y-2 flex-1">
                <p class="text-[11px] font-extrabold text-sky-900 tracking-wider uppercase">PETUNJUK PENGGUNAAN PRINTER KASIR:</p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-2 text-xs text-sky-950 font-medium">
                    <div class="flex items-start gap-2">
                        <span class="text-sky-500 font-bold">•</span>
                        <span><strong>Bluetooth ON:</strong> Pastikan Bluetooth di smartphone atau laptop dalam posisi aktif.</span>
                    </div>
                    <div class="flex items-start gap-2">
                        <span class="text-sky-500 font-bold">•</span>
                        <span><strong>Pairing Awal:</strong> Pasangkan printer di menu Bluetooth HP/PC terlebih dahulu (PIN: <code>0000</code> / <code>1234</code>).</span>
                    </div>
                    <div class="flex items-start gap-2">
                        <span class="text-sky-500 font-bold">•</span>
                        <span><strong>Peramban / App:</strong> Gunakan aplikasi APK Android Kee Laundry atau Chrome/Edge di desktop.</span>
                    </div>
                    <div class="flex items-start gap-2">
                        <span class="text-sky-500 font-bold">•</span>
                        <span><strong>Standar ESC/POS:</strong> Mendukung semua merk printer thermal 58mm &amp; 80mm (Panda, VSC, Iware, Eppos, dsb).</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ══ MODAL: DAFTAR PRINTER NATIVE ANDROID ════════════════════════════ --}}
    <div x-show="showPrinterList"
         x-cloak
         class="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm"
         @click.self="showPrinterList = false">
        <div class="bg-white rounded-3xl w-full max-w-sm overflow-hidden shadow-2xl">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="font-bold text-slate-800">Pilih Printer</h3>
                    <p class="text-xs text-slate-400 mt-0.5">Tap printer untuk menghubungkan</p>
                </div>
                <button @click="showPrinterList = false" class="btn-icon text-slate-400">
                    <svg class="size-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="p-4 max-h-[55vh] overflow-y-auto space-y-2">
                <template x-if="discoveredPrinters.length === 0">
                    <div class="text-center py-10 text-slate-400">
                        <p class="text-3xl mb-3">🖨️</p>
                        <p class="text-sm font-semibold">Tidak ada printer ditemukan.</p>
                        <p class="text-xs mt-1">Pastikan Bluetooth aktif &amp; printer sudah di-pairing.</p>
                    </div>
                </template>
                <template x-for="printer in discoveredPrinters" :key="printer.address">
                    <button @click="connectTo(printer)"
                            class="w-full flex items-center gap-3.5 p-4 rounded-2xl border border-slate-100 bg-slate-50 hover:bg-sky-50 hover:border-sky-200 transition-all active:scale-[0.98] text-left">
                        <div class="size-10 rounded-xl flex items-center justify-center shrink-0"
                             :class="printer.type === 'usb' ? 'bg-blue-100 text-blue-600' : 'bg-sky-100 text-sky-600'">
                            <svg class="size-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0 1 10.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0 .229 2.523a1.125 1.125 0 0 1-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0 0 21 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 0 0-1.913-.247M6.34 18H5.25A2.25 2.25 0 0 1 3 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 0 1 1.913-.247m10.5 0a48.536 48.536 0 0 0-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5Zm-3 0h.008v.008H15V10.5Z"/>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-bold text-slate-800 truncate" x-text="printer.name || 'Printer'"></p>
                            <p class="text-[10px] text-slate-400 font-mono" x-text="printer.address"></p>
                        </div>
                        <svg class="size-4 text-slate-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m9 18 6-6-6-6"/></svg>
                    </button>
                </template>
            </div>
            <div class="p-4 bg-slate-50 border-t border-slate-100">
                <button @click="scanAndConnect()" class="w-full py-2.5 text-xs font-bold text-slate-500 hover:text-sky-600 flex items-center justify-center gap-2 transition-colors">
                    <svg class="size-4" :class="isConnecting ? 'animate-spin' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 0 0 4.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 0 1-15.357-2m15.357 2H15"/></svg>
                    Scan Ulang
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function thermalPrinterManager() {
    return {
        connectionStatus: (typeof localStorage !== 'undefined' && localStorage.getItem('thermal_printer_connected') === 'true') ? 'connected' : 'disconnected',
        deviceName: (typeof localStorage !== 'undefined' ? localStorage.getItem('thermal_printer_name') : '') || '{{ $printer_device_name ?? "" }}',
        isConnecting: false,
        discoveredPrinters: [],
        showPrinterList: false,

        init() {
            // Sinkronkan status dari singleton printer
            const tp = window.thermalPrinter;
            if (tp) {
                this.connectionStatus = tp.connected ? 'connected' : 'disconnected';
                window.addEventListener('printer-status-changed', (e) => {
                    this.connectionStatus = e.detail ? 'connected' : 'disconnected';
                });
            }
        },

        async scanAndConnect() {
            const tp = window.thermalPrinter;
            if (tp && typeof tp.scan === 'function') {
                this.isConnecting = true;
                try {
                    const printers = await tp.scan();
                    this.discoveredPrinters = printers || [];

                    if (printers && printers.length > 0 && tp.isAndroidApp && tp.isAndroidApp()) {
                        this.showPrinterList = true;
                        this.isConnecting = false;
                        return;
                    }

                    await tp.connect(printers && printers.length > 0 && printers[0].isWebDevice ? printers[0] : null);
                    await this._onConnected(tp.device?.name || 'Printer Bluetooth');
                    return;
                } catch (err) {
                    if (err.name !== 'NotFoundError' && err.name !== 'AbortError' && !String(err).toLowerCase().includes('cancel')) {
                        alert('Koneksi printer: ' + (err.message || String(err)));
                    }
                } finally {
                    this.isConnecting = false;
                }
            }

            // Fallback: Web Bluetooth standard browser API
            if (!navigator.bluetooth) {
                alert('Web Bluetooth tidak didukung di browser ini. Silakan gunakan Google Chrome, Microsoft Edge, atau APK Android Kee Laundry.');
                return;
            }

            this.isConnecting = true;
            try {
                const device = await navigator.bluetooth.requestDevice({
                    acceptAllDevices: true,
                    optionalServices: [
                        '000018f0-0000-1000-8000-00805f9b34fb',
                        'e7810a71-73ae-499d-8c15-faa9aef0c3f2',
                        '49535343-fe7d-4ae5-8fa9-9fafd205e455'
                    ]
                });

                if (device) {
                    await this._onConnected(device.name || 'Printer Bluetooth');
                }
            } catch (err) {
                if (err && err.name !== 'NotFoundError' && !String(err).includes('cancelled')) {
                    alert('Gagal menghubungkan printer: ' + (err.message || err));
                }
            } finally {
                this.isConnecting = false;
            }
        },

        async _onConnected(name) {
            this.deviceName = name;
            this.connectionStatus = 'connected';
            localStorage.setItem('thermal_printer_connected', 'true');
            localStorage.setItem('thermal_printer_name', name);
            localStorage.setItem('thermal_printer_type', 'BLUETOOTH');
            await @this.updatePairedDevice('BLUETOOTH', name);
            window.showToast?.({ type: 'success', title: 'Printer Terhubung', message: 'Berhasil: ' + name });
        },

        async connectTo(printer) {
            const tp = window.thermalPrinter;
            this.showPrinterList = false;
            this.isConnecting = true;
            try {
                if (tp) {
                    await tp.connect(printer);
                    await this._onConnected(printer.name || 'Printer');
                }
            } catch (err) {
                alert('Gagal: ' + (err.message || err));
            } finally {
                this.isConnecting = false;
            }
        },

        disconnect() {
            try { window.thermalPrinter?.disconnect(); } catch (e) {}
            this.connectionStatus = 'disconnected';
            this.deviceName = '';
            localStorage.removeItem('thermal_printer_connected');
            localStorage.removeItem('thermal_printer_name');
            @this.updatePairedDevice('SYSTEM', '');
            window.showToast?.({ type: 'info', title: 'Printer Terputus', message: 'Koneksi printer telah direset.' });
        },

        async pairUsb() {
            if ('serial' in navigator) {
                this.isConnecting = true;
                try {
                    const port = await navigator.serial.requestPort();
                    await port.open({ baudRate: 9600 });
                    this.deviceName = 'USB Thermal Printer (COM)';
                    this.connectionStatus = 'connected';
                    localStorage.setItem('thermal_printer_connected', 'true');
                    localStorage.setItem('thermal_printer_name', this.deviceName);
                    localStorage.setItem('thermal_printer_type', 'USB');
                    await @this.updatePairedDevice('USB', this.deviceName);
                    window.showToast?.({ type: 'success', title: 'USB Terhubung', message: 'Port serial printer USB siap.' });
                } catch (err) {
                    console.warn('USB error:', err);
                } finally {
                    this.isConnecting = false;
                }
            } else {
                alert('Web Serial / USB tidak didukung di browser ini.');
            }
        },

        testPrint() {
            const paper = '{{ $printer_paper_size ?? "58mm" }}';
            const width = paper === '80mm' ? '300px' : '220px';
            const name = this.deviceName || 'Thermal Printer POS';

            const printHtml = `
                <html>
                <head>
                    <title>Test Print Struk</title>
                    <style>
                        @page { size: ${paper} auto; margin: 0; }
                        body {
                            font-family: 'Courier New', Courier, monospace;
                            width: ${width};
                            margin: 0 auto;
                            padding: 10px;
                            font-size: 12px;
                            color: #000;
                        }
                        .center { text-align: center; }
                        .dashed { border-top: 1px dashed #000; margin: 8px 0; }
                        .bold { font-weight: bold; }
                        .row { display: flex; justify-content: space-between; }
                    </style>
                </head>
                <body>
                    <div class="center bold" style="font-size: 15px;">KEE LAUNDRY</div>
                    <div class="center" style="font-size: 11px;">Kee Laundry Pusat</div>
                    <div class="center" style="font-size: 11px;">WA: 0812-3456-7890</div>
                    <div class="dashed"></div>
                    <div class="row"><span>Tanggal:</span><span>${new Date().toLocaleDateString('id-ID')}</span></div>
                    <div class="row"><span>Status:</span><span class="bold">TEST PRINT OK</span></div>
                    <div class="row"><span>Printer:</span><span>${name}</span></div>
                    <div class="row"><span>Lebar Kertas:</span><span>${paper}</span></div>
                    <div class="dashed"></div>
                    <div class="center bold" style="margin: 6px 0;">KONEKSI PRINTER BERHASIL!</div>
                    <div class="center" style="font-size: 10px;">Printer siap cetak nota kasir.</div>
                    <div class="center" style="font-size: 9px; margin-top: 6px;">Kee Laundry ERP v2.0</div>
                    <br><br>
                </body>
                </html>
            `;

            const win = window.open('', '_blank', 'width=350,height=480');
            if (win) {
                win.document.write(printHtml);
                win.document.close();
                win.focus();
                setTimeout(() => {
                    win.print();
                    win.close();
                }, 350);
            } else {
                window.print();
            }
        }
    };
}
</script>
