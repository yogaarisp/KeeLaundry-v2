<div class="card p-6 space-y-6" x-data="thermalPrinterManager()">
    <div class="flex items-center justify-between border-b border-slate-100 pb-4">
        <div>
            <h3 class="text-sm font-bold text-slate-800">Koneksi Printer Thermal</h3>
            <p class="text-xs text-slate-400 mt-0.5">Hubungkan printer struk kasir via Bluetooth, USB kabel, atau System Driver</p>
        </div>
        <div class="flex items-center gap-2">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold"
                  :class="connectionStatus === 'connected' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-600'">
                <span class="size-2 rounded-full" :class="connectionStatus === 'connected' ? 'bg-emerald-500 animate-pulse' : 'bg-slate-400'"></span>
                <span x-text="connectionStatus === 'connected' ? 'Terhubung' : 'Belum Terhubung'"></span>
            </span>
        </div>
    </div>

    {{-- Mode Pilihan Printer --}}
    <div>
        <label class="form-label mb-2">Metode Koneksi Printer</label>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            {{-- Bluetooth --}}
            <label @click="$wire.set('printer_type', 'BLUETOOTH')"
                   @class([
                       'relative flex flex-col p-4 rounded-2xl border-2 cursor-pointer transition-all',
                       'border-sky-500 bg-sky-50/40 shadow-sm' => $printer_type === 'BLUETOOTH',
                       'border-slate-200 bg-white hover:bg-slate-50' => $printer_type !== 'BLUETOOTH',
                   ])>
                <div class="flex items-center justify-between mb-2">
                    <span class="text-2xl">📶</span>
                    <input type="radio" wire:model.live="printer_type" value="BLUETOOTH" class="text-sky-600 focus:ring-sky-500">
                </div>
                <p class="font-bold text-slate-800 text-sm">Bluetooth</p>
                <p class="text-xs text-slate-500 mt-1 leading-relaxed">Untuk printer portable / mini Bluetooth (Android, Windows, POS mobile).</p>
            </label>

            {{-- USB --}}
            <label @click="$wire.set('printer_type', 'USB')"
                   @class([
                       'relative flex flex-col p-4 rounded-2xl border-2 cursor-pointer transition-all',
                       'border-sky-500 bg-sky-50/40 shadow-sm' => $printer_type === 'USB',
                       'border-slate-200 bg-white hover:bg-slate-50' => $printer_type !== 'USB',
                   ])>
                <div class="flex items-center justify-between mb-2">
                    <span class="text-2xl">🔌</span>
                    <input type="radio" wire:model.live="printer_type" value="USB" class="text-sky-600 focus:ring-sky-500">
                </div>
                <p class="font-bold text-slate-800 text-sm">USB / Serial</p>
                <p class="text-xs text-slate-500 mt-1 leading-relaxed">Kabel USB langsung dari laptop/PC kasir ke printer POS (Epson, Panda, dsb).</p>
            </label>

            {{-- System / Dialog --}}
            <label @click="$wire.set('printer_type', 'SYSTEM')"
                   @class([
                       'relative flex flex-col p-4 rounded-2xl border-2 cursor-pointer transition-all',
                       'border-sky-500 bg-sky-50/40 shadow-sm' => $printer_type === 'SYSTEM',
                       'border-slate-200 bg-white hover:bg-slate-50' => $printer_type !== 'SYSTEM',
                   ])>
                <div class="flex items-center justify-between mb-2">
                    <span class="text-2xl">🖨️</span>
                    <input type="radio" wire:model.live="printer_type" value="SYSTEM" class="text-sky-600 focus:ring-sky-500">
                </div>
                <p class="font-bold text-slate-800 text-sm">System Print Dialog</p>
                <p class="text-xs text-slate-500 mt-1 leading-relaxed">Dialog cetak bawaan browser (Windows / Chrome print dialog 58mm).</p>
            </label>
        </div>
    </div>

    {{-- Pairing Actions Box based on selected mode --}}
    @if ($printer_type === 'BLUETOOTH')
        <div class="rounded-2xl border border-sky-100 bg-sky-50/50 p-4 space-y-3">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-sky-900">Perangkat Bluetooth Terpasang</p>
                    <p class="text-xs text-sky-700 mt-0.5" x-text="deviceName || 'Belum ada printer Bluetooth terhubung'"></p>
                </div>
                <button type="button"
                        @click="pairBluetooth()"
                        :disabled="isConnecting"
                        class="px-3.5 py-2 rounded-xl bg-sky-600 hover:bg-sky-700 text-white text-xs font-bold shadow-sm transition-all flex items-center gap-2">
                    <span x-show="!isConnecting">🔍 Cari & Hubungkan Bluetooth</span>
                    <span x-show="isConnecting">Menghubungkan...</span>
                </button>
            </div>
            <p class="text-[11px] text-sky-600 leading-relaxed">
                💡 Pastikan Bluetooth laptop/ponsel aktif dan printer thermal dalam kondisi <strong>ON</strong>. Dukungan Web Bluetooth memerlukan browser Chrome/Edge.
            </p>
        </div>
    @elseif ($printer_type === 'USB')
        <div class="rounded-2xl border border-indigo-100 bg-indigo-50/50 p-4 space-y-3">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-indigo-900">Perangkat USB / Serial Terpasang</p>
                    <p class="text-xs text-indigo-700 mt-0.5" x-text="deviceName || 'Belum ada printer USB terhubung'"></p>
                </div>
                <button type="button"
                        @click="pairUsb()"
                        :disabled="isConnecting"
                        class="px-3.5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold shadow-sm transition-all flex items-center gap-2">
                    <span x-show="!isConnecting">🔌 Hubungkan Printer USB</span>
                    <span x-show="isConnecting">Mencari port...</span>
                </button>
            </div>
            <p class="text-[11px] text-indigo-600 leading-relaxed">
                💡 Hubungkan kabel USB printer ke port komputer Anda, lalu klik tombol di atas untuk memberikan izin port Serial/USB.
            </p>
        </div>
    @endif

    {{-- Lebar Kertas & Opsi Tambahan --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2 border-t border-slate-100">
        <div>
            <label class="form-label">Lebar Kertas Struk Thermal</label>
            <select wire:model="printer_paper_size" class="form-input text-xs sm:text-sm">
                <option value="58mm">58mm (Standar Mini POS Thermal / Portable)</option>
                <option value="80mm">80mm (Thermal Lebar Kasir Supermarket)</option>
            </select>
            <p class="text-[11px] text-slate-400 mt-1">Mayoritas printer kasir laundry menggunakan kertas roll 58mm.</p>
        </div>

        <div>
            <label class="form-label">Auto-Print Kasir</label>
            <div class="flex items-center gap-3 mt-2">
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" wire:model="printer_auto_print" class="sr-only peer">
                    <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-sky-600"></div>
                </label>
                <span class="text-xs text-slate-600 font-medium">Otomatis cetak setelah simpan order di POS</span>
            </div>
        </div>
    </div>

    {{-- Test Print & Save Actions --}}
    <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 pt-4 border-t border-slate-100">
        <button type="button"
                @click="testPrint()"
                class="px-4 py-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs sm:text-sm font-semibold shadow-sm transition-all flex items-center justify-center gap-2">
            <span>🖨️ Cetak Struk Uji Coba (Test Print)</span>
        </button>

        <button type="button"
                wire:click="savePrinter"
                wire:loading.attr="disabled"
                class="btn-primary justify-center py-2.5 text-xs sm:text-sm">
            <span wire:loading.remove wire:target="savePrinter">Simpan Pengaturan Printer</span>
            <span wire:loading wire:target="savePrinter">Menyimpan...</span>
        </button>
    </div>
</div>

<script>
function thermalPrinterManager() {
    return {
        connectionStatus: localStorage.getItem('thermal_printer_connected') === 'true' ? 'connected' : 'disconnected',
        deviceName: localStorage.getItem('thermal_printer_name') || '{{ $printer_device_name }}',
        isConnecting: false,

        async pairBluetooth() {
            if (!navigator.bluetooth) {
                alert('Web Bluetooth tidak didukung di browser ini. Silakan gunakan Google Chrome atau Microsoft Edge di Android/PC.');
                return;
            }
            this.isConnecting = true;
            try {
                const device = await navigator.bluetooth.requestDevice({
                    acceptAllDevices: true,
                    optionalServices: [
                        '000018f0-0000-1000-8000-00805f9b34fb', // Standard thermal printer service
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

                    // Save to backend
                    @this.updatePairedDevice('BLUETOOTH', this.deviceName);

                    window.dispatchEvent(new CustomEvent('toast', {
                        detail: { type: 'success', title: 'Bluetooth Terhubung', message: 'Berhasil memasangkan printer ' + this.deviceName }
                    }));
                }
            } catch (err) {
                console.warn('Bluetooth pairing cancelled or error:', err);
            } finally {
                this.isConnecting = false;
            }
        },

        async pairUsb() {
            if ('serial' in navigator) {
                this.isConnecting = true;
                try {
                    const port = await navigator.serial.requestPort();
                    await port.open({ baudRate: 9600 });
                    this.deviceName = 'USB Thermal Printer (COM Port)';
                    this.connectionStatus = 'connected';
                    localStorage.setItem('thermal_printer_connected', 'true');
                    localStorage.setItem('thermal_printer_name', this.deviceName);
                    localStorage.setItem('thermal_printer_type', 'USB');

                    @this.updatePairedDevice('USB', this.deviceName);

                    window.dispatchEvent(new CustomEvent('toast', {
                        detail: { type: 'success', title: 'USB Terhubung', message: 'Port USB Serial printer siap digunakan.' }
                    }));
                } catch (err) {
                    console.warn('USB port error:', err);
                } finally {
                    this.isConnecting = false;
                }
            } else if ('usb' in navigator) {
                this.isConnecting = true;
                try {
                    const device = await navigator.usb.requestDevice({ filters: [] });
                    this.deviceName = device.productName || 'USB Printer POS';
                    this.connectionStatus = 'connected';
                    localStorage.setItem('thermal_printer_connected', 'true');
                    localStorage.setItem('thermal_printer_name', this.deviceName);
                    localStorage.setItem('thermal_printer_type', 'USB');

                    @this.updatePairedDevice('USB', this.deviceName);
                } catch (err) {
                    console.warn('Web USB error:', err);
                } finally {
                    this.isConnecting = false;
                }
            } else {
                alert('Web Serial / Web USB tidak didukung di browser ini. Mode System Print Dialog akan digunakan.');
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
                        body {
                            font-family: 'Courier New', monospace;
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
                    <div class="center bold" style="font-size: 14px;">KEE LAUNDRY</div>
                    <div class="center">Kee Laundry Pusat</div>
                    <div class="center">WA: 0812-3456-7890</div>
                    <div class="dashed"></div>
                    <div class="row"><span>Tanggal:</span><span>${new Date().toLocaleDateString('id-ID')}</span></div>
                    <div class="row"><span>Status:</span><span class="bold">TEST PRINT OK</span></div>
                    <div class="row"><span>Printer:</span><span>${this.deviceName || 'System Dialog'}</span></div>
                    <div class="row"><span>Lebar:</span><span>${paper}</span></div>
                    <div class="dashed"></div>
                    <div class="center bold">KONEKSI PRINTER BERHASIL!</div>
                    <div class="center" style="font-size: 10px; margin-top: 4px;">Kee Laundry ERP v2.0</div>
                    <br><br>
                </body>
                </html>
            `;

            const win = window.open('', '_blank', 'width=350,height=450');
            if (win) {
                win.document.write(printHtml);
                win.document.close();
                win.focus();
                setTimeout(() => {
                    win.print();
                    win.close();
                }, 400);
            } else {
                window.print();
            }
        }
    };
}
</script>
