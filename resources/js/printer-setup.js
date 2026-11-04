/**
 * Alpine.data('printerSetup') — Printer Thermal Kee Laundry
 *
 * Mendukung:
 *  - Android APK (Capacitor + AndroidInterface native bridge via com.dantsu.escposprinter)
 *  - Web Bluetooth (Chrome/Edge desktop & Android browser)
 *  - USB/Serial (Web Serial API)
 *  - Fallback dialog cetak browser
 */
export function printerSetup() {
    return {
        // ── State ─────────────────────────────────────────────────────────────
        status:       'idle',   // idle | scanning | connecting | connected | error
        modal:        false,
        devices:      [],
        deviceName:   '',
        connType:     '',
        connectingTo: '',
        errMsg:       '',
        paperSize:    '58mm',
        _scanRetry:   0,        // counter retry setelah izin diberikan

        // GATT refs (Web Bluetooth)
        _char:      null,
        _gatt:      null,
        _rawDevice: null,
        _wire:      null,

        // ── Deteksi APK ───────────────────────────────────────────────────────
        // Cek saat runtime (bukan saat init) karena Capacitor inject async
        // Hanya true jika AndroidInterface benar-benar tersedia sebagai object
        _isApk() {
            return typeof window.AndroidInterface !== 'undefined'
                && window.AndroidInterface !== null
                && typeof window.AndroidInterface.scanPrinters !== 'undefined';
        },

        // ── Init ──────────────────────────────────────────────────────────────
        init(wire) {
            this._wire = wire;

            // Baca paper size dari Livewire
            try { this.paperSize = wire.get('printer_paper_size') || '58mm'; } catch (_) {}

            // Restore dari localStorage
            try {
                const saved = JSON.parse(localStorage.getItem('kl_printer') || 'null');
                if (saved?.name) {
                    this.deviceName = saved.name;
                    this.connType   = saved.type || 'Bluetooth';
                    this.status     = 'connected';
                }
            } catch (_) {}
        },

        // ── SCAN — titik masuk utama ───────────────────────────────────────────
        async openScan() {
            this.errMsg    = '';
            this._scanRetry = 0;

            // ── PATH 1: Android APK (AndroidInterface tersedia) ───────────────
            if (this._isApk()) {
                await this._apkScan();
                return;
            }

            // ── PATH 2: Browser Web Bluetooth ─────────────────────────────────
            await this._webBluetoothScan();
        },

        // ── APK scan via AndroidInterface ──────────────────────────────────────
        async _apkScan() {
            this.status  = 'scanning';
            this.modal   = true;
            this.devices = [];

            // Beri waktu 200ms agar:
            // 1. UI sempat render loading state
            // 2. Java thread settle setelah permission dialog (jika ada)
            await new Promise(r => setTimeout(r, 200));

            try {
                const raw  = String(window.AndroidInterface.scanPrinters());
                const list = JSON.parse(raw || '[]');

                if (list.length === 0) {
                    // Kemungkinan: izin belum diberikan, printer belum di-pair, atau BT off
                    // Retry 1x setelah 1 detik (waktu user dismiss dialog permission)
                    if (this._scanRetry < 1) {
                        this._scanRetry++;
                        this.errMsg = 'Mengecek izin Bluetooth dan mencari printer…';
                        await new Promise(r => setTimeout(r, 1200));
                        this.errMsg = '';

                        const raw2  = String(window.AndroidInterface.scanPrinters());
                        const list2 = JSON.parse(raw2 || '[]');

                        if (list2.length > 0) {
                            this._setDevices(list2);
                            return;
                        }
                    }

                    // Benar-benar kosong setelah retry
                    this.status = 'error';
                    this.modal  = false;
                    this.errMsg = 'Tidak ada printer ditemukan.\n\n'
                        + '1. Pastikan Bluetooth di HP aktif\n'
                        + '2. Nyalakan printer thermal\n'
                        + '3. Pairing printer di Pengaturan → Bluetooth HP\n'
                        + '4. Izinkan akses Bluetooth saat diminta\n'
                        + '5. Klik Scan lagi';
                    return;
                }

                this._setDevices(list);

            } catch (e) {
                this.status = 'error';
                this.modal  = false;
                this.errMsg = 'Gagal scan: ' + (e.message || String(e))
                    + '\n\nCoba: restart APK dan scan ulang.';
            }
        },

        // ── Helper: set devices list ───────────────────────────────────────────
        _setDevices(list) {
            this.devices = list.map((d, i) => ({
                id:       d.address || String(i),
                label:    d.name    || 'Printer',
                sub:      d.address || d.type || 'Bluetooth',
                type:     d.type    || 'bluetooth',
                address:  d.address || '',
                isNative: true,
            }));
            this.status = 'idle';
        },

        // ── Web Bluetooth scan ────────────────────────────────────────────────
        async _webBluetoothScan() {
            if (!('bluetooth' in navigator)) {
                const isUnsafeHttp = location.protocol === 'http:'
                    && !['localhost', '127.0.0.1'].includes(location.hostname);
                this.status = 'error';
                this.errMsg = isUnsafeHttp
                    ? 'Web Bluetooth tidak aktif pada HTTP. Buka via HTTPS.'
                    : 'Browser tidak mendukung Web Bluetooth. Gunakan Chrome atau Edge versi terbaru.';
                return;
            }

            // Cek previously-paired devices tanpa popup (Chrome 85+)
            if (navigator.bluetooth.getDevices) {
                try {
                    const paired = await navigator.bluetooth.getDevices();
                    if (paired.length > 0) {
                        this.devices = paired.map(d => ({
                            id:       d.id,
                            label:    d.name || 'Printer Bluetooth',
                            sub:      d.id?.substring(0, 17) || 'Bluetooth',
                            raw:      d,
                            isPaired: true,
                        }));
                        this.status = 'idle';
                        this.modal  = true;
                        return;
                    }
                } catch (_) {}
            }

            // Belum pernah pairing → buka native Bluetooth picker browser
            this.status  = 'scanning';
            this.modal   = true;
            this.devices = [];
            try {
                const dev = await navigator.bluetooth.requestDevice({
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
                this.status = 'idle';
                this.modal  = false;
                await this.doConnect({ id: dev.id, label: dev.name || 'Printer', sub: 'Bluetooth', raw: dev });
            } catch (e) {
                this.status = 'idle';
                this.modal  = false;
                const cancelled = e.name === 'NotFoundError'
                    || String(e).toLowerCase().includes('cancel');
                if (!cancelled) {
                    this.errMsg = e.message || String(e);
                    this.status = 'error';
                }
            }
        },

        // ── CONNECT ke device yang dipilih ────────────────────────────────────
        async doConnect(device) {
            this.status       = 'connecting';
            this.connectingTo = device.label;
            this.errMsg       = '';

            try {
                // ── Android native connect ─────────────────────────────────────
                if (device.isNative) {
                    await new Promise(r => setTimeout(r, 100));

                    let res;
                    if (device.type === 'usb') {
                        res = String(window.AndroidInterface.connectUsbPrinter());
                    } else {
                        res = String(window.AndroidInterface.connectBluetoothPrinter(device.address));
                    }

                    if (res === 'SUCCESS') {
                        return await this._saveConnected(device.label, device.type === 'usb' ? 'USB' : 'Bluetooth');
                    }

                    // Pesan error lebih informatif
                    if (res.includes('Izin Bluetooth')) {
                        throw new Error('Izin Bluetooth belum diberikan. Buka Pengaturan Aplikasi → Izin → Aktifkan Bluetooth, lalu coba lagi.');
                    }
                    if (res.includes('tidak ditemukan')) {
                        throw new Error('Printer tidak ditemukan dalam daftar paired. Pairing ulang di Pengaturan Bluetooth HP.');
                    }
                    throw new Error(res || 'Koneksi gagal. Pastikan printer menyala.');
                }

                // ── Web Bluetooth GATT connect ────────────────────────────────
                const rawDev = device.raw;
                if (!rawDev?.gatt) throw new Error('Device tidak mendukung GATT.');

                if (rawDev.gatt.connected) {
                    try { rawDev.gatt.disconnect(); } catch (_) {}
                    await new Promise(r => setTimeout(r, 500));
                }

                const server = await rawDev.gatt.connect();
                this._gatt   = server;

                const knownUUIDs = [
                    '000018f0-0000-1000-8000-00805f9b34fb',
                    '0000ffe0-0000-1000-8000-00805f9b34fb',
                    '49535343-fe7d-4ae5-8fa9-9fafd205e455',
                    'e7810a71-73ae-499d-8c15-faa9aef0c3f2',
                    '0000ff00-0000-1000-8000-00805f9b34fb',
                    '00001101-0000-1000-8000-00805f9b34fb',
                ];

                let char = null;
                for (const uuid of knownUUIDs) {
                    try {
                        const svc   = await server.getPrimaryService(uuid);
                        const chars = await svc.getCharacteristics();
                        char = chars.find(c => c.properties.write || c.properties.writeWithoutResponse);
                        if (char) break;
                    } catch (_) {}
                }

                if (!char) {
                    try {
                        const allSvcs = await server.getPrimaryServices();
                        for (const svc of allSvcs) {
                            try {
                                const chars = await svc.getCharacteristics();
                                char = chars.find(c => c.properties.write || c.properties.writeWithoutResponse);
                                if (char) break;
                            } catch (_) {}
                        }
                    } catch (_) {}
                }

                if (!char) throw new Error('Printer tidak kompatibel ESC/POS BLE.');

                this._char      = char;
                this._rawDevice = rawDev;

                rawDev.addEventListener('gattserverdisconnected', () => {
                    if (this.status === 'connected') {
                        this.status = 'idle';
                        this._char  = null;
                        this._gatt  = null;
                        window.showToast?.({ type: 'warning', title: 'Printer Terputus', message: this.deviceName + ' terputus.' });
                    }
                });

                await this._saveConnected(rawDev.name || device.label, 'Bluetooth');

            } catch (e) {
                this.status = 'error';
                this.modal  = false;
                this.errMsg = e.message || String(e);
            }
        },

        // ── USB ───────────────────────────────────────────────────────────────
        async connectUsb() {
            this.errMsg = '';
            if (this._isApk()) {
                this.status = 'connecting';
                try {
                    await new Promise(r => setTimeout(r, 100));
                    const res = String(window.AndroidInterface.connectUsbPrinter());
                    if (res === 'SUCCESS') return await this._saveConnected('USB Printer (OTG)', 'USB');
                    throw new Error(res || 'Tidak ada USB printer OTG terhubung.');
                } catch (e) {
                    this.status = 'error';
                    this.errMsg = e.message || String(e);
                }
                return;
            }
            if (!('serial' in navigator)) {
                this.errMsg = 'USB Serial tidak didukung. Gunakan Chrome atau Edge desktop.';
                return;
            }
            try {
                const port = await navigator.serial.requestPort();
                await port.open({ baudRate: 9600 });
                window._kl_serial = port;
                await this._saveConnected('USB Printer', 'USB/Serial');
            } catch (e) {
                if (e.name !== 'NotFoundError' && !String(e).toLowerCase().includes('cancel')) {
                    this.errMsg = 'USB gagal: ' + (e.message || e);
                }
            }
        },

        // ── System dialog ─────────────────────────────────────────────────────
        async useSystemDialog() {
            await this._saveConnected('Dialog Browser', 'SYSTEM');
        },

        // ── Disconnect ────────────────────────────────────────────────────────
        async doDisconnect() {
            try {
                if (this._isApk()) window.AndroidInterface.disconnectPrinter();
                else if (this._gatt?.connected) this._gatt.disconnect();
            } catch (_) {}

            this._gatt = this._char = this._rawDevice = null;
            this.status = 'idle';
            this.deviceName = this.connType = '';
            this.devices = [];
            localStorage.removeItem('kl_printer');
            try { await this._wire?.call('updatePairedDevice', 'SYSTEM', ''); } catch (_) {}
            window.showToast?.({ type: 'info', title: 'Terputus', message: 'Printer dilepas.' });
        },

        // ── Tutup modal ───────────────────────────────────────────────────────
        closeModal() {
            this.modal = false;
            if (['scanning', 'connecting'].includes(this.status)) this.status = 'idle';
        },

        // ── Test Print ────────────────────────────────────────────────────────
        async doTestPrint() {
            const paper = this.paperSize || '58mm';
            const cols  = paper === '80mm' ? 48 : 32;
            const sep   = '-'.repeat(cols);
            const now   = new Date().toLocaleString('id-ID');

            // Android native
            if (this._isApk()) {
                try {
                    const res = String(window.AndroidInterface.printFormattedText(
                        `[C]<b>KEE LAUNDRY</b>\n[C]Test Print\n[C]${sep}\n`
                        + `[L]Waktu  : ${now}\n[L]Printer: ${this.deviceName || 'Native'}\n[L]Lebar  : ${paper}\n`
                        + `[C]${sep}\n[C]<b>PRINTER OK ✓</b>\n\n\n`
                    ));
                    if (res === 'SUCCESS') {
                        window.showToast?.({ type: 'success', title: 'Test Print OK', message: 'Cek struk fisik.' });
                        return;
                    }
                    if (res.includes('belum terhubung')) {
                        this.errMsg = 'Printer belum dipilih. Tap printer dari daftar hasil scan terlebih dahulu.';
                        return;
                    }
                    throw new Error(res);
                } catch (e) {
                    this.errMsg = 'Print gagal: ' + (e.message || String(e));
                    return;
                }
            }

            // Web Bluetooth
            if (this._char) {
                try {
                    await this._escposPrint(this._char, { cols, sep, now, paper });
                    window.showToast?.({ type: 'success', title: 'Test Print OK', message: 'Cek struk fisik.' });
                    return;
                } catch (e) {
                    this.errMsg = 'Print gagal: ' + (e.message || String(e));
                    return;
                }
            }

            this._browserPrint(paper, now);
        },

        // ── ESC/POS raw bytes ─────────────────────────────────────────────────
        async _escposPrint(char, { cols, sep, now, paper }) {
            const enc = new TextEncoder();
            const E = 0x1B, G = 0x1D;
            const cmd = [];
            const add = (b) => b.forEach(x => cmd.push(x));
            const txt = (s) => add([...enc.encode(s)]);

            add([E, 0x40]);
            add([E, 0x61, 0x01]);
            add([E, 0x45, 0x01, G, 0x21, 0x01]);
            txt('KEE LAUNDRY\n');
            add([G, 0x21, 0x00, E, 0x45, 0x00]);
            txt('Test Print\n');
            add([E, 0x61, 0x00]);
            txt(sep + '\n');
            txt(`Waktu  : ${now}\n`);
            txt(`Lebar  : ${paper}\n`);
            txt(sep + '\n');
            add([E, 0x61, 0x01, E, 0x45, 0x01]);
            txt('PRINTER OK ✓\n');
            add([E, 0x45, 0x00, 0x0A, 0x0A]);
            add([G, 0x56, 0x00]);

            const data  = new Uint8Array(cmd);
            const chunk = 512;
            for (let i = 0; i < data.length; i += chunk) {
                await char.writeValue(data.slice(i, i + chunk));
                await new Promise(r => setTimeout(r, 60));
            }
        },

        // ── Browser print fallback ────────────────────────────────────────────
        _browserPrint(paper, now) {
            const w   = paper === '80mm' ? '300px' : '220px';
            const win = window.open('', '_blank', `width=${paper === '80mm' ? 380 : 300},height=430,toolbar=0`);
            if (!win) { this.errMsg = 'Popup diblokir. Izinkan popup di address bar.'; return; }
            win.document.write(
                `<!DOCTYPE html><html><head><title>Test Print</title>`
                + `<style>@page{size:${paper} auto;margin:4mm}body{font-family:Courier New,monospace;width:${w};`
                + `margin:0 auto;font-size:11px;line-height:1.5;color:#000}.c{text-align:center}.b{font-weight:bold}`
                + `hr{border:none;border-top:1px dashed #000;margin:4px 0}</style></head><body>`
                + `<div class="c b" style="font-size:13px">KEE LAUNDRY</div><div class="c">Test Print</div><hr>`
                + `<div>Waktu  : ${now}</div><div>Lebar  : ${paper}</div>`
                + `<div>Status : <b>PRINTER OK ✓</b></div><hr><div class="c">Terima Kasih</div><br><br></body></html>`
            );
            win.document.close();
            win.focus();
            setTimeout(() => { win.print(); setTimeout(() => win.close(), 1200); }, 400);
        },

        // ── Simpan state setelah connect ──────────────────────────────────────
        async _saveConnected(name, type) {
            this.status     = 'connected';
            this.deviceName = name;
            this.connType   = type;
            this.modal      = false;
            this.errMsg     = '';
            localStorage.setItem('kl_printer', JSON.stringify({ name, type }));
            try { await this._wire?.call('updatePairedDevice', type, name); } catch (_) {}
            window.showToast?.({ type: 'success', title: 'Printer Terhubung! 🖨️', message: name + ' siap mencetak.' });
        },
    };
}
