/**
 * Alpine.data('printerSetup') — Fitur koneksi Printer Thermal
 *
 * Dipanggil dari blade via:
 *   x-data="printerSetup()"
 *   x-init="init($wire)"
 *
 * Tidak bergantung pada singleton thermalPrinter.
 * Web Bluetooth logic langsung di sini.
 */
export function printerSetup() {
    return {
        // ── Reactive state ─────────────────────────────────────────────────
        status:       'idle',  // idle | scanning | connecting | connected | error
        modal:        false,
        devices:      [],
        deviceName:   '',
        connType:     '',
        connectingTo: '',
        errMsg:       '',
        paperSize:    '58mm',

        // Internal refs
        _char:      null,
        _gatt:      null,
        _rawDevice: null,
        _wire:      null,

        // ── Init ────────────────────────────────────────────────────────────
        init(wire) {
            this._wire    = wire;
            this.paperSize = wire.get('printer_paper_size') || '58mm';

            // Baca state dari localStorage
            try {
                const saved = JSON.parse(localStorage.getItem('kl_printer') || '{}');
                if (saved.name) {
                    this.deviceName = saved.name;
                    this.connType   = saved.type || 'Bluetooth';
                    this.status     = 'connected';
                }
            } catch (_) {}

            // Sync dari singleton jika sudah auto-reconnect di halaman lain
            const tp = window.thermalPrinter;
            if (tp?.connected && tp.device?.name) {
                this.deviceName = tp.device.name;
                this.connType   = 'Bluetooth';
                this.status     = 'connected';
                this._char      = tp.characteristic || null;
                this._gatt      = tp.device?.gatt   || null;
                this._rawDevice = tp.device          || null;
            }
        },

        // ── SCAN — titik masuk utama ────────────────────────────────────────
        async openScan() {
            this.errMsg = '';

            // ── Android APK Native ──────────────────────────────────────────
            const tp = window.thermalPrinter;
            if (tp?.isAndroidApp?.()) {
                this.status  = 'scanning';
                this.modal   = true;
                this.devices = [];
                try {
                    const list   = await tp.scan();
                    this.devices = (list || []).map((d, i) => ({
                        id:       d.address || String(i),
                        label:    d.name || 'Printer',
                        sub:      d.address || 'Bluetooth',
                        raw:      d,
                        isNative: true,
                    }));
                } catch (e) {
                    this.errMsg = 'Scan gagal: ' + (e.message || e);
                    this.modal  = false;
                } finally {
                    this.status = this.devices.length ? 'idle' : 'error';
                }
                return;
            }

            // ── Cek Web Bluetooth support ───────────────────────────────────
            if (!('bluetooth' in navigator)) {
                const isHttp = location.protocol === 'http:'
                    && !['localhost', '127.0.0.1'].includes(location.hostname);
                this.status = 'error';
                this.errMsg = isHttp
                    ? 'Web Bluetooth tidak aktif di HTTP. Buka via HTTPS atau localhost.'
                    : 'Browser tidak mendukung Web Bluetooth. Gunakan Chrome atau Edge.';
                return;
            }

            // ── Cek previously-paired devices dulu (tanpa popup) ────────────
            if (navigator.bluetooth.getDevices) {
                try {
                    const paired = await navigator.bluetooth.getDevices();
                    if (paired.length > 0) {
                        this.devices = paired.map(d => ({
                            id:       d.id,
                            label:    d.name || 'Printer Bluetooth',
                            sub:      d.id?.substring(0, 18) || 'Bluetooth',
                            raw:      d,
                            isPaired: true,
                        }));
                        this.status = 'idle';
                        this.modal  = true;
                        return;
                    }
                } catch (_) {}
            }

            // ── Buka native Bluetooth picker ────────────────────────────────
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
                // User pilih dari picker → langsung connect
                this.status = 'idle';
                await this.doConnect({
                    id:    dev.id,
                    label: dev.name || 'Printer',
                    sub:   'Bluetooth',
                    raw:   dev,
                });
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

        // ── CONNECT ke device yang dipilih ──────────────────────────────────
        async doConnect(device) {
            this.status       = 'connecting';
            this.connectingTo = device.label;
            this.errMsg       = '';

            try {
                // Android native
                if (device.isNative) {
                    const res = window.AndroidInterface
                        ?.connectBluetoothPrinter?.(device.raw?.address || device.id);
                    if (res === 'SUCCESS') {
                        return await this._saveConnected(device.label, 'Bluetooth');
                    }
                    throw new Error(res || 'Koneksi native gagal');
                }

                // Web Bluetooth GATT
                const rawDev = device.raw;
                if (!rawDev?.gatt) throw new Error('Device tidak valid atau tidak mendukung GATT.');

                // Disconnect stale
                if (rawDev.gatt.connected) {
                    try { rawDev.gatt.disconnect(); } catch (_) {}
                    await new Promise(r => setTimeout(r, 400));
                }

                const server = await rawDev.gatt.connect();
                this._gatt   = server;

                // Cari write characteristic
                const serviceUUIDs = [
                    '000018f0-0000-1000-8000-00805f9b34fb',
                    '0000ffe0-0000-1000-8000-00805f9b34fb',
                    '49535343-fe7d-4ae5-8fa9-9fafd205e455',
                    'e7810a71-73ae-499d-8c15-faa9aef0c3f2',
                    '0000ff00-0000-1000-8000-00805f9b34fb',
                    '00001101-0000-1000-8000-00805f9b34fb',
                ];

                let char = null;

                for (const uuid of serviceUUIDs) {
                    try {
                        const svc   = await server.getPrimaryService(uuid);
                        const chars = await svc.getCharacteristics();
                        char = chars.find(c => c.properties.write || c.properties.writeWithoutResponse);
                        if (char) break;
                    } catch (_) {}
                }

                // Fallback: scan semua service
                if (!char) {
                    try {
                        const svcs = await server.getPrimaryServices();
                        for (const svc of svcs) {
                            try {
                                const chars = await svc.getCharacteristics();
                                char = chars.find(c => c.properties.write || c.properties.writeWithoutResponse);
                                if (char) break;
                            } catch (_) {}
                        }
                    } catch (_) {}
                }

                if (!char) {
                    throw new Error(
                        'Printer tidak terdeteksi sebagai ESC/POS printer. '
                        + 'Pastikan printer mendukung Bluetooth ESC/POS.'
                    );
                }

                this._char      = char;
                this._rawDevice = rawDev;

                // Deteksi jika putus tidak disengaja
                rawDev.addEventListener('gattserverdisconnected', () => {
                    if (this.status === 'connected') {
                        this.status = 'idle';
                        this._char  = null;
                        this._gatt  = null;
                        window.showToast?.({
                            type: 'warning',
                            title: 'Printer Terputus',
                            message: 'Koneksi ke ' + this.deviceName + ' terputus.',
                        });
                    }
                });

                await this._saveConnected(rawDev.name || device.label, 'Bluetooth');

            } catch (e) {
                this.status = 'error';
                this.modal  = false;
                this.errMsg = e.message || String(e);
            }
        },

        // ── USB / Web Serial ────────────────────────────────────────────────
        async connectUsb() {
            this.errMsg = '';
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

        // ── Mode cetak browser ──────────────────────────────────────────────
        async useSystemDialog() {
            await this._saveConnected('Dialog Browser', 'SYSTEM');
        },

        // ── Disconnect ──────────────────────────────────────────────────────
        async doDisconnect() {
            try {
                if (this._gatt?.connected) this._gatt.disconnect();
            } catch (_) {}
            try {
                if (window.thermalPrinter?.connected) window.thermalPrinter.disconnect();
            } catch (_) {}
            this._gatt      = null;
            this._char      = null;
            this._rawDevice = null;
            this.status     = 'idle';
            this.deviceName = '';
            this.connType   = '';
            this.devices    = [];
            localStorage.removeItem('kl_printer');
            await this._wire?.call('updatePairedDevice', 'SYSTEM', '');
            window.showToast?.({ type: 'info', title: 'Terputus', message: 'Printer dilepas.' });
        },

        // ── Tutup modal ─────────────────────────────────────────────────────
        closeModal() {
            this.modal = false;
            if (this.status === 'scanning' || this.status === 'connecting') {
                this.status = 'idle';
            }
        },

        // ── Test Print ──────────────────────────────────────────────────────
        async doTestPrint() {
            const paper = this.paperSize || '58mm';
            const cols  = paper === '80mm' ? 48 : 32;
            const sep   = '-'.repeat(cols);
            const now   = new Date().toLocaleString('id-ID');

            // Android
            if (window.thermalPrinter?.isAndroidApp?.() && window.AndroidInterface) {
                try {
                    window.AndroidInterface.printFormattedText(
                        `[C]<b>KEE LAUNDRY</b>\n[C]Test Print\n[C]${sep}\n` +
                        `[L]Waktu  : ${now}\n[L]Printer: ${this.deviceName}\n[L]Lebar  : ${paper}\n` +
                        `[C]${sep}\n[C]<b>PRINTER OK ✓</b>\n\n\n`
                    );
                    window.showToast?.({ type: 'success', title: 'Test Print OK', message: 'Cek struk.' });
                    return;
                } catch (_) {}
            }

            // Web Bluetooth
            if (this._char) {
                try {
                    await this._escposPrint(this._char, {
                        cols, sep, now,
                        name: this.deviceName,
                        paper,
                    });
                    window.showToast?.({ type: 'success', title: 'Test Print OK', message: 'Cek struk.' });
                    return;
                } catch (e) {
                    this.errMsg = 'Print gagal: ' + (e.message || e);
                    return;
                }
            }

            // Fallback dialog browser
            this._browserPrint(paper, now);
        },

        // ── ESC/POS print ───────────────────────────────────────────────────
        async _escposPrint(char, { cols, sep, now, name, paper }) {
            const enc = new TextEncoder();
            const E   = 0x1B;
            const G   = 0x1D;
            const cmd = [];
            const add = (bytes) => bytes.forEach(b => cmd.push(b));

            add([E, 0x40]);                           // Init
            add([E, 0x61, 0x01]);                     // Center
            add([E, 0x45, 0x01, G, 0x21, 0x01]);      // Bold + 2x
            add([...enc.encode('KEE LAUNDRY\n')]);
            add([G, 0x21, 0x00, E, 0x45, 0x00]);      // Normal
            add([...enc.encode('Test Print\n')]);
            add([E, 0x61, 0x00]);                     // Left
            add([...enc.encode(sep + '\n')]);
            add([...enc.encode(`Waktu  : ${now}\n`)]);
            add([...enc.encode(`Printer: ${name}\n`)]);
            add([...enc.encode(`Lebar  : ${paper}\n`)]);
            add([...enc.encode(sep + '\n')]);
            add([E, 0x61, 0x01, E, 0x45, 0x01]);
            add([...enc.encode('PRINTER OK ✓\n')]);
            add([E, 0x45, 0x00]);
            add([0x0A, 0x0A]);
            add([G, 0x56, 0x00]);                     // Cut

            const data  = new Uint8Array(cmd);
            const chunk = 512;
            for (let i = 0; i < data.length; i += chunk) {
                await char.writeValue(data.slice(i, i + chunk));
                await new Promise(r => setTimeout(r, 60));
            }
        },

        // ── Browser print fallback ──────────────────────────────────────────
        _browserPrint(paper, now) {
            const w   = paper === '80mm' ? '300px' : '220px';
            const win = window.open('', '_blank', `width=${paper === '80mm' ? 380 : 300},height=430`);
            if (!win) { this.errMsg = 'Popup diblokir browser. Izinkan popup dulu.'; return; }
            win.document.write(
                `<!DOCTYPE html><html><head><title>Test Print</title>`
                + `<style>@page{size:${paper} auto;margin:4mm}body{font-family:Courier New,monospace;`
                + `width:${w};margin:0 auto;font-size:11px;line-height:1.5;color:#000}`
                + `.c{text-align:center}.b{font-weight:bold}hr{border:none;border-top:1px dashed #000;margin:4px 0}`
                + `</style></head><body>`
                + `<div class="c b" style="font-size:13px">KEE LAUNDRY</div>`
                + `<div class="c">Test Print</div><hr>`
                + `<div>Waktu  : ${now}</div><div>Lebar  : ${paper}</div>`
                + `<div>Status : <b>PRINTER OK ✓</b></div><hr>`
                + `<div class="c">Terima Kasih</div><br><br></body></html>`
            );
            win.document.close();
            win.focus();
            setTimeout(() => { win.print(); setTimeout(() => win.close(), 1200); }, 400);
        },

        // ── Helper: simpan state setelah connect berhasil ────────────────────
        async _saveConnected(name, type) {
            this.status     = 'connected';
            this.deviceName = name;
            this.connType   = type;
            this.modal      = false;
            this.errMsg     = '';
            localStorage.setItem('kl_printer', JSON.stringify({ name, type }));
            await this._wire?.call('updatePairedDevice', type, name);
            window.showToast?.({ type: 'success', title: 'Printer Terhubung! 🖨️', message: name + ' siap cetak.' });
        },
    };
}
