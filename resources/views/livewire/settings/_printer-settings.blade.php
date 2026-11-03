<div class="space-y-5" x-data="thermalPrinterManager()">

    {{-- ══ HEADER ════════════════════════════════════════════════════ --}}
    <div class="flex items-center gap-2.5">
        <div class="size-8 rounded-xl bg-sky-50 border border-sky-200/70 text-sky-600 flex items-center justify-center shadow-2xs">
            <svg class="size-4.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0 1 10.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0 .229 2.523a1.125 1.125 0 0 1-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0 0 21 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 0 0-1.913-.247M6.34 18H5.25A2.25 2.25 0 0 1 3 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 0 1 1.913-.247m10.5 0a48.536 48.536 0 0 0-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5Zm-3 0h.008v.008H15V10.5Z"/>
            </svg>
        </div>
        <div>
            <h3 class="text-sm font-bold text-slate-800 tracking-tight">Printer Thermal</h3>
            <p class="text-[11px] text-slate-400 mt-0.5">Kelola koneksi printer Bluetooth/USB &amp; setelan cetak struk POS</p>
        </div>
    </div>

    {{-- ══ KARTU 1: STATUS & KONEKSI PRINTER ═══════════════════════ --}}
    <div class="card p-5 sm:p-6 space-y-5 shadow-sm border border-slate-200/80">
        <div class="border-b border-slate-100 pb-3">
            <h4 class="text-sm font-bold text-slate-800">Status Koneksi</h4>
            <p class="text-xs text-slate-400 mt-0.5">Hubungkan printer thermal via Bluetooth atau USB Serial</p>
        </div>

        {{-- Status Row --}}
        <div class="flex items-center justify-between p-3.5 rounded-xl border transition-colors"
             :class="connectionStatus === 'connected' ? 'bg-emerald-50/60 border-emerald-200/80' : 'bg-slate-50 border-slate-200'">
            <div class="flex items-center gap-3">
                <div class="size-10 rounded-xl flex items-center justify-center shrink-0 transition-colors"
                     :class="connectionStatus === 'connected' ? 'bg-emerald-100 text-emerald-600' : 'bg-slate-100 text-slate-400'">
                    <svg class="size-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0 1 10.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0 .229 2.523a1.125 1.125 0 0 1-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0 0 21 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 0 0-1.913-.247M6.34 18H5.25A2.25 2.25 0 0 1 3 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 0 1 1.913-.247m10.5 0a48.536 48.536 0 0 0-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5Zm-3 0h.008v.008H15V10.5Z"/>
                    </svg>
                </div>
                <div>
                    <div class="flex items-center gap-1.5">
                        <span class="size-2 rounded-full shrink-0 transition-colors"
                              :class="connectionStatus === 'connected' ? 'bg-emerald-500 animate-pulse' : 'bg-slate-300'"></span>
                        <p class="text-xs font-bold"
                           :class="connectionStatus === 'connected' ? 'text-emerald-800' : 'text-slate-700'"
                           x-text="connectionStatus === 'connected' ? ('Terhubung: ' + (deviceName || 'Printer Bluetooth')) : 'Printer Belum Terhubung'">
                        </p>
                    </div>
                    <p class="text-[11px] mt-0.5"
                       :class="connectionStatus === 'connected' ? 'text-emerald-600' : 'text-slate-400'"
                       x-text="connectionStatus === 'connected' ? 'Siap mencetak struk transaksi' : 'Tap tombol di bawah untuk memulai koneksi'">
                    </p>
                </div>
            </div>

            {{-- Tombol Putuskan --}}
            <button x-show="connectionStatus === 'connected'"
                    x-cloak
                    @click="disconnect()"
                    type="button"
                    class="text-xs font-semibold text-rose-600 hover:text-rose-700 bg-rose-50 hover:bg-rose-100 border border-rose-200/60 px-3 py-1.5 rounded-xl transition-colors shrink-0">
                Putuskan
            </button>
        </div>

        {{-- Action Buttons --}}
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
            <button type="button"
                    @click="scanAndConnect()"
                    :disabled="isConnecting"
                    class="btn-primary flex-1 py-3 justify-center gap-2 text-sm disabled:opacity-60 cursor-pointer">
                <svg x-show="!isConnecting" class="size-4.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.348 14.652a3.75 3.75 0 0 1 0-5.304m5.304 0a3.75 3.75 0 0 1 0 5.304m-7.425 2.121a6.75 6.75 0 0 1 0-9.546m9.546 0a6.75 6.75 0 0 1 0 9.546M5.106 18.894c-3.808-3.808-3.808-9.98 0-13.789m13.788 0c3.808 3.808 3.808 9.98 0 13.788M12 12h.008v.008H12V12Z"/>
                </svg>
                <svg x-show="isConnecting" class="size-4.5 animate-spin shrink-0" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                </svg>
                <span x-text="isConnecting ? 'Mencari Perangkat Bluetooth…' : 'Scan & Hubungkan Printer'"></span>
            </button>

            <button type="button"
                    @click="testPrint()"
                    class="flex items-center justify-center gap-2 px-5 py-3 rounded-2xl border border-slate-200 bg-white hover:bg-slate-50 active:bg-slate-100 text-slate-700 font-bold text-sm shadow-2xs transition-all shrink-0 cursor-pointer">
                <svg class="size-4.5 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0 1 10.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0 .229 2.523a1.125 1.125 0 0 1-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0 0 21 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 0 0-1.913-.247M6.34 18H5.25A2.25 2.25 0 0 1 3 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 0 1 1.913-.247m10.5 0a48.536 48.536 0 0 0-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5Zm-3 0h.008v.008H15V10.5Z"/>
                </svg>
                Test Print
            </button>
        </div>
    </div>

    {{-- ══ KARTU 2: SETELAN CETAK ════════════════════════════════════ --}}
    <div class="card p-5 sm:p-6 space-y-5 shadow-sm border border-slate-200/80">
        <div class="border-b border-slate-100 pb-3">
            <h4 class="text-sm font-bold text-slate-800">Setelan Cetak</h4>
            <p class="text-xs text-slate-400 mt-0.5">Ukuran kertas roll thermal &amp; opsi cetak otomatis</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            {{-- Ukuran Kertas --}}
            <div class="space-y-3">
                <div class="flex items-center gap-1.5">
                    <label class="form-label mb-0 text-xs font-bold text-slate-700">Ukuran Kertas</label>
                    <span class="text-slate-400 hover:text-slate-600 cursor-pointer" title="Pilih ukuran lebar kertas sesuai jenis roll printer thermal kasir Anda">
                        <svg class="size-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z"/>
                        </svg>
                    </span>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    {{-- 58mm --}}
                    <button type="button"
                            wire:click="$set('printer_paper_size', '58mm')"
                            @click="$nextTick(() => @this.savePrinter())"
                            @class([
                                'relative p-4 rounded-2xl text-center transition-all flex flex-col items-center justify-center min-h-[88px] cursor-pointer',
                                'border-2 border-sky-500 bg-sky-50/40 shadow-xs' => $printer_paper_size === '58mm',
                                'border border-slate-200 bg-white hover:border-slate-300 hover:bg-slate-50/60' => $printer_paper_size !== '58mm',
                            ])>
                        <span @class([
                            'text-2xl font-extrabold tracking-tight',
                            'text-sky-600' => $printer_paper_size === '58mm',
                            'text-slate-400' => $printer_paper_size !== '58mm',
                        ])>58mm</span>
                        <span @class([
                            'text-[10px] font-bold tracking-widest mt-0.5 uppercase',
                            'text-sky-500' => $printer_paper_size === '58mm',
                            'text-slate-400' => $printer_paper_size !== '58mm',
                        ])>Standard</span>
                        @if ($printer_paper_size === '58mm')
                            <div class="absolute bottom-2 right-2 size-5 rounded-full bg-sky-100 text-sky-600 flex items-center justify-center text-[11px] font-bold shadow-2xs">✓</div>
                        @endif
                    </button>

                    {{-- 80mm --}}
                    <button type="button"
                            wire:click="$set('printer_paper_size', '80mm')"
                            @click="$nextTick(() => @this.savePrinter())"
                            @class([
                                'relative p-4 rounded-2xl text-center transition-all flex flex-col items-center justify-center min-h-[88px] cursor-pointer',
                                'border-2 border-sky-500 bg-sky-50/40 shadow-xs' => $printer_paper_size === '80mm',
                                'border border-slate-200 bg-white hover:border-slate-300 hover:bg-slate-50/60' => $printer_paper_size !== '80mm',
                            ])>
                        <span @class([
                            'text-2xl font-extrabold tracking-tight',
                            'text-sky-600' => $printer_paper_size === '80mm',
                            'text-slate-400' => $printer_paper_size !== '80mm',
                        ])>80mm</span>
                        <span @class([
                            'text-[10px] font-bold tracking-widest mt-0.5 uppercase',
                            'text-sky-500' => $printer_paper_size === '80mm',
                            'text-slate-400' => $printer_paper_size !== '80mm',
                        ])>Large</span>
                        @if ($printer_paper_size === '80mm')
                            <div class="absolute bottom-2 right-2 size-5 rounded-full bg-sky-100 text-sky-600 flex items-center justify-center text-[11px] font-bold shadow-2xs">✓</div>
                        @endif
                    </button>
                </div>
            </div>

            {{-- Auto Print Struk --}}
            <div class="space-y-3">
                <label class="form-label text-xs font-bold text-slate-700">Auto Print Struk</label>
                <div class="flex items-start justify-between p-4 rounded-xl border transition-colors"
                     :class="$wire.printer_auto_print ? 'bg-sky-50/50 border-sky-200/80' : 'bg-slate-50 border-slate-200'">
                    <div class="space-y-1 pr-4">
                        <p class="text-xs font-bold"
                           :class="$wire.printer_auto_print ? 'text-sky-800' : 'text-slate-700'">
                            Cetak Otomatis
                        </p>
                        <p class="text-[11px] text-slate-400 leading-relaxed">Struk langsung tercetak setelah transaksi selesai diselesaikan</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer shrink-0 mt-0.5">
                        <input type="checkbox"
                               wire:model.live="printer_auto_print"
                               @change="$nextTick(() => @this.savePrinter())"
                               class="sr-only peer">
                        <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-sky-500"></div>
                    </label>
                </div>

                {{-- Metode Saat Ini --}}
                <div class="flex items-center justify-between py-2 px-3.5 rounded-xl bg-slate-50 border border-slate-200/80">
                    <span class="text-xs text-slate-500">Metode cetak aktif:</span>
                    <span class="text-xs font-bold text-slate-700 bg-white border border-slate-200 px-2.5 py-0.5 rounded-lg font-mono shadow-2xs">{{ $printer_type }}</span>
                </div>
            </div>
        </div>

        {{-- Opsi Alternatif --}}
        <div class="pt-3 border-t border-slate-100 flex flex-wrap items-center gap-2">
            <span class="text-[11px] text-slate-400 font-semibold">Metode lain:</span>
            <button type="button" @click="pairUsb()"
                    class="text-[11px] font-semibold text-slate-600 hover:text-sky-600 bg-white hover:bg-sky-50 border border-slate-200 hover:border-sky-200 px-3 py-1 rounded-lg transition-colors shadow-2xs">
                📠 USB Kabel
            </button>
            <button type="button" wire:click="$set('printer_type', 'SYSTEM')" @click="$nextTick(() => @this.savePrinter())"
                    class="text-[11px] font-semibold text-slate-600 hover:text-sky-600 bg-white hover:bg-sky-50 border border-slate-200 hover:border-sky-200 px-3 py-1 rounded-lg transition-colors shadow-2xs">
                🖨️ System Print Dialog
            </button>
        </div>
    </div>

    {{-- ══ KARTU 3: PETUNJUK PENGGUNAAN ════════════════════════════ --}}
    <div class="card p-5 shadow-sm border border-slate-200/80">
        <div class="flex items-start gap-3.5">
            <div class="size-9 rounded-xl bg-sky-50 border border-sky-200/70 text-sky-600 flex items-center justify-center shrink-0 mt-0.5">
                <svg class="size-4.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z"/>
                </svg>
            </div>
            <div class="flex-1 space-y-2.5">
                <p class="text-xs font-bold text-slate-800">Petunjuk Penggunaan Printer</p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-1.5">
                    @foreach ([
                        'Pastikan Bluetooth perangkat menyala',
                        'Printer harus mendukung perintah ESC/POS',
                        'Gunakan Chrome atau Edge (Web Bluetooth)',
                        'Pastikan printer sudah di-pairing dengan perangkat',
                    ] as $hint)
                    <div class="flex items-start gap-2">
                        <span class="size-1.5 rounded-full bg-sky-400 mt-1.5 shrink-0"></span>
                        <span class="text-xs text-slate-600">{{ $hint }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function thermalPrinterManager() {
    return {
        connectionStatus: localStorage.getItem('thermal_printer_connected') === 'true' ? 'connected' : 'disconnected',
        deviceName: localStorage.getItem('thermal_printer_name') || '{{ $printer_device_name }}',
        isConnecting: false,

        async scanAndConnect() {
            if (!navigator.bluetooth) {
                alert('Web Bluetooth tidak didukung di browser ini. Silakan gunakan Google Chrome atau Microsoft Edge, atau buka melalui aplikasi APK Android.');
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
                    this.deviceName = device.name || 'Printer Bluetooth';
                    this.connectionStatus = 'connected';
                    localStorage.setItem('thermal_printer_connected', 'true');
                    localStorage.setItem('thermal_printer_name', this.deviceName);
                    localStorage.setItem('thermal_printer_type', 'BLUETOOTH');
                    await @this.updatePairedDevice('BLUETOOTH', this.deviceName);
                    window.dispatchEvent(new CustomEvent('toast', {
                        detail: { type: 'success', title: 'Printer Terhubung', message: 'Berhasil memasangkan printer ' + this.deviceName }
                    }));
                }
            } catch (err) {
                console.warn('Bluetooth pairing cancelled or error:', err);
            } finally {
                this.isConnecting = false;
            }
        },

        disconnect() {
            this.connectionStatus = 'disconnected';
            this.deviceName = '';
            localStorage.removeItem('thermal_printer_connected');
            localStorage.removeItem('thermal_printer_name');
            @this.updatePairedDevice('SYSTEM', '');
            window.dispatchEvent(new CustomEvent('toast', {
                detail: { type: 'info', title: 'Printer Terputus', message: 'Koneksi printer thermal telah direset.' }
            }));
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
                    window.dispatchEvent(new CustomEvent('toast', {
                        detail: { type: 'success', title: 'USB Terhubung', message: 'Port USB Serial printer siap digunakan.' }
                    }));
                } catch (err) {
                    console.warn('USB port error:', err);
                } finally {
                    this.isConnecting = false;
                }
            } else {
                alert('Web Serial / USB tidak didukung di browser ini.');
            }
        },

        testPrint() {
            const paper = '{{ $printer_paper_size }}';
            const width = paper === '80mm' ? '300px' : '220px';
            const printHtml = `
                <html>
                <head>
                    <title>Test Print Struk</title>
                    <style>
                        @page { size: ${paper} auto; margin: 0; }
                        body { font-family: 'Courier New', monospace; width: ${width}; margin: 0 auto; padding: 10px; font-size: 12px; color: #000; }
                        .center { text-align: center; }
                        .dashed { border-top: 1px dashed #000; margin: 8px 0; }
                        .bold { font-weight: bold; }
                        .row { display: flex; justify-content: space-between; }
                    </style>
                </head>
                <body>
                    <div class="center bold" style="font-size:14px;">KEE LAUNDRY</div>
                    <div class="center">Kee Laundry Pusat</div>
                    <div class="center">WA: 0812-3456-7890</div>
                    <div class="dashed"></div>
                    <div class="row"><span>Tanggal:</span><span>${new Date().toLocaleDateString('id-ID')}</span></div>
                    <div class="row"><span>Status:</span><span class="bold">TEST PRINT OK</span></div>
                    <div class="row"><span>Printer:</span><span>${this.deviceName || 'Thermal Printer'}</span></div>
                    <div class="row"><span>Lebar:</span><span>${paper}</span></div>
                    <div class="dashed"></div>
                    <div class="center bold">KONEKSI PRINTER BERHASIL!</div>
                    <div class="center" style="font-size:10px;margin-top:4px;">Kee Laundry ERP v2.0</div>
                    <br><br>
                </body>
                </html>
            `;
            const win = window.open('', '_blank', 'width=350,height=450');
            if (win) {
                win.document.write(printHtml);
                win.document.close();
                win.focus();
                setTimeout(() => { win.print(); win.close(); }, 400);
            } else {
                window.print();
            }
        }
    };
}
</script>
