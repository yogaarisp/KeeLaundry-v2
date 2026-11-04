<div
    class="space-y-6"
    x-data="thermalPrinterManager()"
    x-init="init()"
>

    {{-- ══ HERO CARD: STATUS & KONEKSI ════════════════════════════════════ --}}
    <div class="card p-5 sm:p-6 space-y-5 shadow-xs border border-slate-200/90">

        {{-- Header --}}
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

            {{-- Status badge --}}
            <div class="flex items-center gap-2 self-start sm:self-auto">
                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full border text-xs font-bold shadow-2xs transition-all"
                     :class="isConnected ? 'bg-emerald-50 border-emerald-200 text-emerald-700' : 'bg-slate-100 border-slate-200 text-slate-600'">
                    <span class="size-2 rounded-full shrink-0"
                          :class="isConnected ? 'bg-emerald-500 animate-pulse' : 'bg-slate-400'"></span>
                    <span x-text="isConnected ? ('Terhubung: ' + deviceName) : '{{ $printer_device_name ? 'Tersimpan: ' . $printer_device_name : 'Belum Terhubung' }}'"></span>
                </div>
            </div>
        </div>

        {{-- Device info card --}}
        <div class="p-4 sm:p-5 rounded-2xl border transition-all"
             :class="isConnected ? 'bg-emerald-50/40 border-emerald-200/80' : 'bg-slate-50/80 border-slate-200'">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-center gap-3.5">
                    <div class="size-12 rounded-2xl flex items-center justify-center shrink-0 border transition-all shadow-2xs"
                         :class="isConnected ? 'bg-emerald-100 border-emerald-200 text-emerald-600' : 'bg-white border-slate-200 text-slate-400'">
                        <svg class="size-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0 1 10.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0 .229 2.523a1.125 1.125 0 0 1-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0 0 21 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 0 0-1.913-.247M6.34 18H5.25A2.25 2.25 0 0 1 3 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 0 1 1.913-.247m10.5 0a48.536 48.536 0 0 0-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5Zm-3 0h.008v.008H15V10.5Z"/>
                        </svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Status Perangkat</span>
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-md uppercase"
                                  :class="isConnected ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-200 text-slate-600'"
                                  x-text="printerType">{{ $printer_type }}</span>
                        </div>
                        <h4 class="text-sm sm:text-base font-extrabold text-slate-800 mt-0.5"
                            x-text="isConnected ? deviceName : '{{ $printer_device_name ?: 'Belum Ada Printer Terhubung' }}'">
                            {{ $printer_device_name ?: 'Belum Ada Printer Terhubung' }}
                        </h4>
                        <p class="text-xs text-slate-500 mt-0.5">
                            <span x-show="isConnected">Printer aktif dan siap mencetak nota kasir secara instan.</span>
                            <span x-show="!isConnected">Tekan tombol <strong>Scan &amp; Hubungkan</strong> di bawah untuk mendeteksi printer kasir Bluetooth.</span>
                        </p>
                    </div>
                </div>
                <div x-show="isConnected" x-cloak class="shrink-0">
                    <button @click="disconnect()" type="button"
                            class="px-3.5 py-2 rounded-xl text-xs font-bold text-rose-600 hover:text-rose-700 bg-white hover:bg-rose-50 border border-rose-200 shadow-2xs transition-colors">
                        Putuskan Printer
                    </button>
                </div>
            </div>
        </div>

        {{-- Tombol Aksi --}}
        <div class="space-y-3 pt-1">
            <div class="flex flex-col sm:flex-row items-stretch gap-3">
                {{-- SCAN BLUETOOTH --}}
                <button type="button" @click="scanBluetooth()" :disabled="isConnecting"
                        class="flex-1 min-h-[52px] bg-gradient-to-r from-sky-600 to-blue-600 hover:from-sky-700 hover:to-blue-700 text-white font-bold text-sm px-6 py-3.5 rounded-2xl shadow-sm hover:shadow-md transition-all flex items-center justify-center gap-2.5 disabled:opacity-60 cursor-pointer">
                    <svg x-show="!isConnecting" class="size-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.348 14.652a3.75 3.75 0 0 1 0-5.304m5.304 0a3.75 3.75 0 0 1 0 5.304m-7.425 2.121a6.75 6.75 0 0 1 0-9.546m9.546 0a6.75 6.75 0 0 1 0 9.546M5.106 18.894c-3.808-3.808-3.808-9.98 0-13.789m13.788 0c3.808 3.808 3.808 9.98 0 13.788M12 12h.008v.008H12V12Z"/>
                    </svg>
                    <svg x-show="isConnecting" x-cloak class="size-4 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                    </svg>
                    <span x-text="isConnecting ? 'Mencari Perangkat Bluetooth…' : 'Scan & Hubungkan Printer Bluetooth'">
                        Scan &amp; Hubungkan Printer Bluetooth
                    </span>
                </button>

                {{-- TEST PRINT --}}
                <button type="button" @click="testPrint()" :disabled="isConnecting"
                        class="min-h-[52px] px-5 py-3.5 rounded-2xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 font-bold text-sm shadow-2xs transition-all flex items-center justify-center gap-2 shrink-0 cursor-pointer disabled:opacity-50">
                    <svg class="size-4.5 text-slate-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0 1 10.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0 .229 2.523a1.125 1.125 0 0 1-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0 0 21 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 0 0-1.913-.247M6.34 18H5.25A2.25 2.25 0 0 1 3 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 0 1 1.913-.247m10.5 0a48.536 48.536 0 0 0-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5Zm-3 0h.008v.008H15V10.5Z"/>
                    </svg>
                    Test Print Struk
                </button>
            </div>

            {{-- Opsi alternatif --}}
            <div class="flex flex-wrap items-center justify-between text-xs text-slate-500 pt-1 px-1 gap-2">
                <span class="text-slate-400 font-medium">Opsi koneksi lainnya:</span>
                <div class="flex items-center gap-3">
                    <button type="button" @click="connectUsb()"
                            class="hover:text-sky-600 font-semibold underline decoration-slate-300 transition-colors cursor-pointer">
                        🔌 Sambungkan via USB Kabel (PC)
                    </button>
                    <span class="text-slate-300">•</span>
                    <button type="button" @click="useSystemPrint()"
                            class="hover:text-sky-600 font-semibold underline decoration-slate-300 transition-colors cursor-pointer">
                        💻 Gunakan Dialog Cetak Browser
                    </button>
                </div>
            </div>
        </div>

        {{-- Daftar Printer Nearby (inline, tanpa modal) --}}
        <div x-show="nearbyPrinters.length > 0 && !isConnected" x-cloak class="space-y-2 pt-1">
            <p class="text-xs font-bold uppercase tracking-wider text-slate-400 px-1">Printer Ditemukan</p>
            <template x-for="p in nearbyPrinters" :key="p.address || p.name">
                <button @click="connectToPrinter(p)" :disabled="isConnecting"
                        class="w-full flex items-center gap-3.5 p-3.5 rounded-2xl border border-slate-100 bg-slate-50 hover:bg-sky-50 hover:border-sky-200 transition-all active:scale-[0.98] text-left disabled:opacity-50">
                    <div class="size-9 rounded-xl flex items-center justify-center shrink-0 bg-sky-100 text-sky-600">
                        <svg class="size-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0 1 10.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0 .229 2.523a1.125 1.125 0 0 1-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0 0 21 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 0 0-1.913-.247M6.34 18H5.25A2.25 2.25 0 0 1 3 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 0 1 1.913-.247m10.5 0a48.536 48.536 0 0 0-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5Zm-3 0h.008v.008H15V10.5Z"/>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-bold text-slate-800 truncate text-sm" x-text="p.name || 'Printer Bluetooth'"></p>
                        <p class="text-[10px] text-slate-400 font-mono" x-text="p.address || '—'"></p>
                    </div>
                    <svg class="size-4 text-slate-300 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m9 18 6-6-6-6"/>
                    </svg>
                </button>
            </template>
        </div>
    </div>

    {{-- ══ DUA KOLOM: UKURAN KERTAS & AUTO-PRINT ═══════════════════════════ --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

        {{-- UKURAN KERTAS --}}
        <div class="card p-5 sm:p-6 space-y-4 shadow-xs border border-slate-200/90">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div class="flex items-center gap-2">
                    <div class="size-7 rounded-lg bg-sky-50 border border-sky-200 text-sky-600 flex items-center justify-center shrink-0 text-base">📄</div>
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
                @foreach (['58mm' => ['STANDARD', 'Roll Mini POS'], '80mm' => ['LARGE', 'Roll Kasir Lebar']] as $size => [$label, $desc])
                    <button type="button"
                            wire:click="$set('printer_paper_size', '{{ $size }}')"
                            @click="$nextTick(() => @this.savePrinter())"
                            @class([
                                'relative p-4 rounded-2xl text-center transition-all flex flex-col items-center justify-center min-h-[105px] cursor-pointer',
                                'border-2 border-sky-600 bg-sky-50/30 shadow-xs'                                => $printer_paper_size === $size,
                                'border border-slate-200 bg-white hover:border-slate-300 hover:bg-slate-50' => $printer_paper_size !== $size,
                            ])>
                        <span @class(['text-2xl sm:text-3xl font-black tracking-tight leading-none', 'text-sky-600' => $printer_paper_size === $size, 'text-slate-400' => $printer_paper_size !== $size])>{{ $size }}</span>
                        <span @class(['text-[10px] font-extrabold tracking-widest mt-1.5 uppercase', 'text-sky-600' => $printer_paper_size === $size, 'text-slate-400' => $printer_paper_size !== $size])>{{ $label }}</span>
                        <span class="text-[10px] text-slate-400 mt-0.5 font-medium">{{ $desc }}</span>
                        @if ($printer_paper_size === $size)
                            <div class="absolute top-2.5 right-2.5 size-5 rounded-full bg-sky-600 text-white flex items-center justify-center text-xs font-bold shadow-2xs">✓</div>
                        @endif
                    </button>
                @endforeach
            </div>
            <p class="text-[11px] text-slate-400 text-center leading-relaxed">
                💡 Mayoritas printer portable kasir laundry menggunakan kertas <strong>58mm</strong>.
            </p>
        </div>

        {{-- AUTO-PRINT --}}
        <div class="card p-5 sm:p-6 space-y-4 shadow-xs border border-slate-200/90 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <div class="flex items-center gap-2">
                        <div class="size-7 rounded-lg bg-sky-50 border border-sky-200 text-sky-600 flex items-center justify-center shrink-0 text-base">⚡</div>
                        <div>
                            <h4 class="text-sm font-bold text-slate-800">Auto Print Struk</h4>
                            <p class="text-[11px] text-slate-400">Otomasi cetak struk di menu kasir POS</p>
                        </div>
                    </div>
                    <span @class(['text-[10px] font-bold px-2.5 py-0.5 rounded-full uppercase', 'bg-sky-100 text-sky-800' => $printer_auto_print, 'bg-slate-100 text-slate-500' => !$printer_auto_print])>
                        {{ $printer_auto_print ? 'Aktif' : 'Nonaktif' }}
                    </span>
                </div>
                <div class="flex items-center justify-between p-4 rounded-2xl border transition-all mt-3.5"
                     :class="$wire.printer_auto_print ? 'bg-sky-50/40 border-sky-200' : 'bg-slate-50/80 border-slate-200'">
                    <div class="space-y-0.5 pr-3">
                        <p class="text-xs font-bold text-slate-800">Cetak Otomatis Setelah Simpan Order</p>
                        <p class="text-[11px] text-slate-500 leading-relaxed">Nota fisik langsung keluar dari printer saat kasir menekan tombol "Proses Order".</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer shrink-0">
                        <input type="checkbox" wire:model.live="printer_auto_print"
                               @change="$nextTick(() => @this.savePrinter())" class="sr-only peer">
                        <div class="w-12 h-6 bg-slate-300 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-sky-600"></div>
                    </label>
                </div>
            </div>
            <p class="text-[11px] text-slate-400 leading-relaxed">
                Jika dinonaktifkan, kasir tetap dapat mencetak struk secara manual dengan menekan tombol <strong>"Cetak Struk"</strong> di pop-up nota.
            </p>
        </div>
    </div>

    {{-- ══ PETUNJUK ════════════════════════════════════════════════════════ --}}
    <div class="rounded-2xl border border-sky-100 bg-sky-50/80 p-5 shadow-2xs">
        <div class="flex items-start gap-3.5">
            <div class="size-8 rounded-full bg-sky-100 border border-sky-200 text-sky-700 flex items-center justify-center shrink-0 mt-0.5">
                <svg class="size-4.5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z"/>
                </svg>
            </div>
            <div class="space-y-2 flex-1">
                <p class="text-[11px] font-extrabold text-sky-900 tracking-wider uppercase">Petunjuk Penggunaan Printer Kasir</p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-2 text-xs text-sky-950 font-medium">
                    <div class="flex items-start gap-2"><span class="text-sky-500 font-bold shrink-0">•</span><span><strong>Bluetooth ON:</strong> Pastikan Bluetooth di smartphone atau laptop dalam posisi aktif.</span></div>
                    <div class="flex items-start gap-2"><span class="text-sky-500 font-bold shrink-0">•</span><span><strong>Pairing Awal:</strong> Pasangkan printer di menu Bluetooth HP/PC terlebih dahulu (PIN: <code>0000</code> atau <code>1234</code>).</span></div>
                    <div class="flex items-start gap-2"><span class="text-sky-500 font-bold shrink-0">•</span><span><strong>Browser:</strong> Gunakan <strong>Chrome</strong> atau <strong>Edge</strong> di Android/PC. Safari & Firefox tidak mendukung Web Bluetooth.</span></div>
                    <div class="flex items-start gap-2"><span class="text-sky-500 font-bold shrink-0">•</span><span><strong>ESC/POS:</strong> Mendukung semua printer thermal 58mm &amp; 80mm (Panda, VSC, Iware, Eppos, Zjiang, dll).</span></div>
                    <div class="flex items-start gap-2"><span class="text-sky-500 font-bold shrink-0">•</span><span><strong>HTTP Lokal:</strong> Web Bluetooth hanya berjalan di <code>localhost</code> atau HTTPS. Tidak bisa di IP LAN via HTTP.</span></div>
                    <div class="flex items-start gap-2"><span class="text-sky-500 font-bold shrink-0">•</span><span><strong>Fallback:</strong> Jika tidak ada printer BLE, gunakan "Dialog Cetak Browser" untuk mencetak via driver sistem.</span></div>
                </div>
            </div>
        </div>
    </div>

    {{-- ══ MODAL: PILIH PRINTER — di-teleport ke body agar bebas dari re-render Livewire ══ --}}
    <template x-teleport="body">
        <div
            x-show="$store.printerModal.show"
            x-cloak
            x-transition:enter="transition ease-out duration-150"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-100"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-[9999] flex items-end sm:items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm"
            @click="$store.printerModal.show = false"
            @keydown.escape.window="$store.printerModal.show = false"
        >
            <div
                class="bg-white rounded-3xl w-full max-w-sm overflow-hidden shadow-2xl"
                x-transition:enter="transition ease-out duration-150"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                @click.stop
            >
                {{-- Header modal --}}
                <div class="flex items-center justify-between p-5 border-b border-slate-100">
                    <div>
                        <h3 class="font-bold text-slate-800">Pilih Printer</h3>
                        <p class="text-xs text-slate-400 mt-0.5"
                           x-text="$store.printerModal.printers.length > 0
                               ? $store.printerModal.printers.length + ' printer ditemukan'
                               : 'Tap printer untuk menghubungkan'"></p>
                    </div>
                    {{-- TOMBOL CLOSE ✕ --}}
                    <button
                        type="button"
                        @click.stop="$store.printerModal.show = false"
                        class="flex items-center justify-center size-9 rounded-xl bg-slate-100 hover:bg-rose-50 hover:text-rose-600 text-slate-500 transition-colors"
                        title="Tutup"
                    >
                        <svg class="size-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                {{-- Daftar printer --}}
                <div class="p-4 max-h-[55vh] overflow-y-auto space-y-2">
                    <template x-if="$store.printerModal.printers.length === 0">
                        <div class="text-center py-10 text-slate-400">
                            <p class="text-3xl mb-3">🖨️</p>
                            <p class="text-sm font-semibold">Tidak ada printer ditemukan.</p>
                            <p class="text-xs mt-1">Pastikan Bluetooth aktif &amp; printer sudah di-pairing di pengaturan HP.</p>
                        </div>
                    </template>
                    <template x-for="p in $store.printerModal.printers" :key="p.address || p.name">
                        <button type="button"
                                @click.stop="$store.printerModal.onSelect(p); $store.printerModal.show = false"
                                class="w-full flex items-center gap-3.5 p-4 rounded-2xl border border-slate-100 bg-slate-50 hover:bg-sky-50 hover:border-sky-200 transition-all active:scale-[0.98] text-left">
                            <div class="size-10 rounded-xl flex items-center justify-center shrink-0 bg-sky-100 text-sky-600">
                                <svg class="size-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0 1 10.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0 .229 2.523a1.125 1.125 0 0 1-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0 0 21 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 0 0-1.913-.247M6.34 18H5.25A2.25 2.25 0 0 1 3 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 0 1 1.913-.247m10.5 0a48.536 48.536 0 0 0-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5Zm-3 0h.008v.008H15V10.5Z"/>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="font-bold text-slate-800 truncate" x-text="p.name || 'Printer Bluetooth'"></p>
                                <p class="text-[10px] text-slate-400 font-mono" x-text="p.address || '—'"></p>
                            </div>
                            <svg class="size-4 text-slate-300 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m9 18 6-6-6-6"/>
                            </svg>
                        </button>
                    </template>
                </div>

                {{-- Footer: scan ulang --}}
                <div class="p-4 bg-slate-50 border-t border-slate-100">
                    <button type="button" @click.stop="$store.printerModal.onRescan()"
                            class="w-full py-2.5 text-xs font-bold text-slate-500 hover:text-sky-600 flex items-center justify-center gap-2 transition-colors">
                        <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 0 0 4.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 0 1-15.357-2m15.357 2H15"/>
                        </svg>
                        Scan Ulang
                    </button>
                </div>
            </div>
        </div>
    </template>
</div>

<script>
// ── Alpine Store: hidup di luar Livewire scope, tidak di-reset saat re-render ──
document.addEventListener('alpine:init', () => {
    if (!Alpine.store('printerModal')) {
        Alpine.store('printerModal', {
            show:     false,
            printers: [],
            onSelect: () => {},
            onRescan: () => {},
            open(printers, onSelect, onRescan) {
                this.printers = printers || [];
                this.onSelect = onSelect || (() => {});
                this.onRescan = onRescan || (() => {});
                this.show = true;
            },
        });
    }
});

function thermalPrinterManager() {
    return {
        isConnected:    localStorage.getItem('thermal_printer_connected') === 'true',
        isConnecting:   false,
        deviceName:     localStorage.getItem('thermal_printer_name') || '{{ $printer_device_name ?? '' }}',
        printerType:    localStorage.getItem('thermal_printer_type') || '{{ $printer_type ?? 'SYSTEM' }}',
        nearbyPrinters: [],

        init() {
            const tp = window.thermalPrinter;
            if (tp) {
                this.isConnected = tp.connected;
                tp.addEventListener('connection-changed', (e) => {
                    this.isConnected = e.detail;
                    if (!e.detail) { this.deviceName = ''; this.printerType = 'SYSTEM'; }
                });
            }
            window.addEventListener('printer-status-changed', (e) => { this.isConnected = e.detail; });
        },

        async scanBluetooth() {
            const tp   = window.thermalPrinter;
            const self = this;

            // Android APK
            if (tp?.isAndroidApp?.()) {
                this.isConnecting = true;
                try {
                    const list = await tp.scan();
                    Alpine.store('printerModal').open(
                        list || [],
                        (p) => self.connectToPrinter(p),
                        ()  => self.scanBluetooth()
                    );
                } catch (e) {
                    this._toast('error', 'Scan Gagal', e.message || String(e));
                } finally {
                    this.isConnecting = false;
                }
                return;
            }

            // Browser
            if (!('bluetooth' in navigator)) {
                const isHttp = location.protocol === 'http:' &&
                               location.hostname !== 'localhost' &&
                               location.hostname !== '127.0.0.1';
                this._toast('error',
                    isHttp ? 'Web Bluetooth Tidak Aktif' : 'Browser Tidak Didukung',
                    isHttp
                        ? 'Dinonaktifkan di HTTP (' + location.hostname + '). Buka via HTTPS, localhost, atau APK Android.'
                        : 'Gunakan Google Chrome atau Microsoft Edge. Safari & Firefox tidak mendukung Web Bluetooth.'
                );
                return;
            }

            // Previously-paired devices?
            if (navigator.bluetooth.getDevices) {
                try {
                    const devs = await navigator.bluetooth.getDevices();
                    if (devs.length > 0) {
                        const list = devs.map(d => ({ name: d.name || 'Printer', address: d.id, raw: d, isWebDevice: true }));
                        this.nearbyPrinters = list;
                        Alpine.store('printerModal').open(
                            list,
                            (p) => self.connectToPrinter(p),
                            ()  => self.scanBluetooth()
                        );
                        return;
                    }
                } catch (e) { /* fallthrough */ }
            }

            // Buka browser picker
            this.isConnecting = true;
            try {
                await tp.connect(null);
                await this._onConnected(tp.device?.name || 'Printer Bluetooth', 'BLUETOOTH');
            } catch (e) {
                if (e.name !== 'NotFoundError' && !String(e).includes('cancelled')) {
                    this._toast('error', 'Koneksi Gagal', e.message || String(e));
                }
            } finally {
                this.isConnecting = false;
            }
        },

        async connectToPrinter(printer) {
            const tp = window.thermalPrinter;
            this.nearbyPrinters = [];
            this.isConnecting   = true;
            try {
                if (tp) {
                    await tp.connect(printer);
                    await this._onConnected(printer.name || 'Printer Bluetooth', 'BLUETOOTH');
                }
            } catch (e) {
                this._toast('error', 'Koneksi Gagal', e.message || String(e));
            } finally {
                this.isConnecting = false;
            }
        },

        async connectUsb() {
            if (!('serial' in navigator)) {
                this._toast('error', 'USB Serial Tidak Didukung', 'Gunakan Chrome/Edge desktop via HTTPS atau localhost.');
                return;
            }
            this.isConnecting = true;
            try {
                const port = await navigator.serial.requestPort();
                await port.open({ baudRate: 9600 });
                window._printerSerialPort = port;
                await this._onConnected('USB Thermal Printer', 'USB');
            } catch (e) {
                if (!String(e).includes('cancelled') && e.name !== 'NotFoundError') {
                    this._toast('error', 'USB Gagal', e.message || String(e));
                }
            } finally {
                this.isConnecting = false;
            }
        },

        async useSystemPrint() {
            this.isConnected = false;
            this.deviceName  = 'System / Browser';
            this.printerType = 'SYSTEM';
            localStorage.setItem('thermal_printer_type', 'SYSTEM');
            localStorage.removeItem('thermal_printer_connected');
            localStorage.removeItem('thermal_printer_name');
            await @this.updatePairedDevice('SYSTEM', '');
            this._toast('info', 'Mode Cetak Browser', 'Struk akan dicetak menggunakan dialog print browser.');
        },

        async disconnect() {
            try { await window.thermalPrinter?.disconnect(); } catch (e) {}
            this.isConnected    = false;
            this.deviceName     = '';
            this.printerType    = 'SYSTEM';
            this.nearbyPrinters = [];
            localStorage.removeItem('thermal_printer_connected');
            localStorage.removeItem('thermal_printer_name');
            localStorage.removeItem('thermal_printer_type');
            await @this.updatePairedDevice('SYSTEM', '');
            this._toast('info', 'Printer Terputus', 'Koneksi printer telah direset.');
        },

        async testPrint() {
            const paper = '{{ $printer_paper_size ?? '58mm' }}';
            const tp    = window.thermalPrinter;

            if (tp?.isAndroidApp?.() && tp.connected) {
                try {
                    const cols = paper === '80mm' ? 48 : 32;
                    const sep  = '-'.repeat(cols);
                    const text = '[C]<b>KEE LAUNDRY</b>\n[C]Test Print OK\n[C]' + sep + '\n' +
                                 '[L]Tanggal : ' + new Date().toLocaleString('id-ID') + '\n' +
                                 '[L]Printer : ' + (this.deviceName || 'Thermal') + '\n' +
                                 '[L]Lebar   : ' + paper + '\n[C]' + sep + '\n[C]<b>BERHASIL!</b>\n\n\n';
                    window.AndroidInterface.printFormattedText(text);
                    this._toast('success', 'Test Print Terkirim', 'Cek struk fisik pada printer Anda.');
                    return;
                } catch (e) { /* fallthrough */ }
            }

            if (tp?.connected && tp.characteristic) {
                try {
                    await tp.testPrint();
                    this._toast('success', 'Test Print Terkirim', 'Cek struk fisik pada printer Anda.');
                    return;
                } catch (e) {
                    this._toast('error', 'Print Gagal', e.message || String(e));
                    return;
                }
            }

            this._browserPrint(paper);
        },

        _browserPrint(paper) {
            const width = paper === '80mm' ? '302px' : '218px';
            const now   = new Date().toLocaleString('id-ID');
            const html  = '<!DOCTYPE html><html><head><title>Test Print</title>' +
                '<style>@page{size:' + paper + ' auto;margin:4mm}' +
                'body{font-family:Courier New,monospace;width:' + width + ';margin:0 auto;font-size:11px;line-height:1.5;color:#000}' +
                '.c{text-align:center}.b{font-weight:bold}hr{border:none;border-top:1px dashed #000;margin:5px 0}' +
                '</style></head><body>' +
                '<div class="c b" style="font-size:14px">KEE LAUNDRY</div>' +
                '<div class="c">Test Print — Printer OK</div><hr>' +
                '<div>Tanggal : ' + now + '</div>' +
                '<div>Lebar   : ' + paper + '</div>' +
                '<div>Status  : <b>BERHASIL ✓</b></div><hr>' +
                '<div class="c b">Terima Kasih!</div><br><br></body></html>';
            const win = window.open('', '_blank', 'width=' + (paper === '80mm' ? 380 : 300) + ',height=420');
            if (win) {
                win.document.write(html);
                win.document.close();
                win.focus();
                setTimeout(() => { win.print(); setTimeout(() => win.close(), 1000); }, 400);
            } else {
                this._toast('error', 'Popup Diblokir', 'Izinkan popup di browser untuk mencetak struk.');
            }
        },

        async _onConnected(name, type) {
            this.isConnected = true;
            this.deviceName  = name;
            this.printerType = type;
            localStorage.setItem('thermal_printer_connected', 'true');
            localStorage.setItem('thermal_printer_name', name);
            localStorage.setItem('thermal_printer_type', type);
            await @this.updatePairedDevice(type, name);
            this._toast('success', 'Printer Terhubung 🖨️', name + ' siap mencetak.');
        },

        _toast(type, title, message) {
            window.showToast ? window.showToast({ type, title, message }) : console.info('[printer]', type, title, message);
        },
    };
}
</script>
