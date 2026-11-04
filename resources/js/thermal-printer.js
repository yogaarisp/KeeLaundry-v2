/**
 * thermal-printer.js
 * Abstraksi koneksi printer thermal:
 * - Di APK Android (Capacitor): pakai @capacitor-community/bluetooth-le
 * - Di browser Chrome/Edge: pakai Web Bluetooth API (navigator.bluetooth)
 */

import { BleClient } from '@capacitor-community/bluetooth-le';
import { Capacitor } from '@capacitor/core';

// UUIDs layanan printer thermal ESC/POS
const PRINTER_SERVICES = [
    '000018f0-0000-1000-8000-00805f9b34fb',
    'e7810a71-73ae-499d-8c15-faa9aef0c3f2',
    '49535343-fe7d-4ae5-8fa9-9fafd205e455',
];

// UUID karakteristik write yang umum dipakai printer BLE
const WRITE_CHARACTERISTICS = [
    '00002af1-0000-1000-8000-00805f9b34fb',
    'bef8d6c9-9c21-4c9e-b632-bd58c1009f9f',
    '49535343-8841-43f4-a8d4-ecbe34729bb3',
];

const isNative = () => Capacitor.isNativePlatform();

/**
 * Koneksi state global (disimpan di module scope agar bisa dipakai saat cetak)
 */
let _bleDeviceId = null;
let _bleServiceUuid = null;
let _bleCharUuid = null;
let _bleDevice = null; // Web Bluetooth device object

/**
 * scanAndConnect - Scan & pasangkan printer
 * @returns {Promise<{name: string, id: string}>}
 */
export async function scanAndConnect() {
    if (isNative()) {
        return await scanAndConnectNative();
    } else {
        return await scanAndConnectWeb();
    }
}

/**
 * Native path: pakai Capacitor BleClient
 */
async function scanAndConnectNative() {
    await BleClient.initialize({ androidNeverForLocation: true });

    // Minta permission Bluetooth di Android 12+
    await BleClient.requestLEScan(
        { services: [], allowDuplicates: false },
        () => {} // stop setelah requestDevice
    );
    await BleClient.stopLEScan();

    const device = await BleClient.requestDevice({
        services: [],
        optionalServices: PRINTER_SERVICES,
        namePrefix: '', // semua device
    });

    if (!device?.deviceId) throw new Error('Tidak ada perangkat dipilih');

    await BleClient.connect(device.deviceId, () => {
        // disconnect callback
        _bleDeviceId = null;
    });

    // Temukan service & karakteristik yang cocok
    const services = await BleClient.getServices(device.deviceId);
    for (const svc of services) {
        for (const char of svc.characteristics) {
            const props = char.properties;
            const canWrite = props.write || props.writeWithoutResponse;
            if (canWrite) {
                _bleDeviceId = device.deviceId;
                _bleServiceUuid = svc.uuid;
                _bleCharUuid = char.uuid;
                return { name: device.name || 'Thermal Printer', id: device.deviceId };
            }
        }
    }

    // Fallback: coba UUID yang umum
    _bleDeviceId = device.deviceId;
    _bleServiceUuid = PRINTER_SERVICES[0];
    _bleCharUuid = WRITE_CHARACTERISTICS[0];
    return { name: device.name || 'Thermal Printer', id: device.deviceId };
}

/**
 * Web path: pakai Web Bluetooth API (Chrome/Edge desktop)
 */
async function scanAndConnectWeb() {
    if (!navigator.bluetooth) {
        throw new Error('NO_WEB_BLUETOOTH');
    }

    const device = await navigator.bluetooth.requestDevice({
        acceptAllDevices: true,
        optionalServices: PRINTER_SERVICES,
    });

    if (!device) throw new Error('Tidak ada perangkat dipilih');

    _bleDevice = device;
    return { name: device.name || 'Printer Bluetooth', id: device.id };
}

/**
 * disconnect - Putuskan koneksi
 */
export async function disconnect() {
    try {
        if (isNative() && _bleDeviceId) {
            await BleClient.disconnect(_bleDeviceId);
        }
    } catch (e) {
        console.warn('Disconnect error:', e);
    } finally {
        _bleDeviceId = null;
        _bleServiceUuid = null;
        _bleCharUuid = null;
        _bleDevice = null;
    }
}

/**
 * printESC - Kirim data ESC/POS ke printer
 * @param {Uint8Array} data
 */
export async function printESC(data) {
    if (isNative()) {
        if (!_bleDeviceId) throw new Error('Printer tidak terhubung');
        const CHUNK = 100; // BLE MTU chunk
        for (let i = 0; i < data.length; i += CHUNK) {
            const chunk = data.slice(i, i + CHUNK);
            await BleClient.writeWithoutResponse(
                _bleDeviceId,
                _bleServiceUuid,
                _bleCharUuid,
                new DataView(chunk.buffer)
            );
            await new Promise(r => setTimeout(r, 20));
        }
    } else {
        // Di browser: tidak bisa kirim data langsung, fallback ke print dialog
        throw new Error('BROWSER_FALLBACK');
    }
}

/**
 * printTestPage - Cetak halaman test
 */
export async function printTestPage(printerName, paperSize) {
    const width = paperSize === '80mm' ? '300px' : '220px';
    const html = `
        <html><head><title>Test Print</title>
        <style>
            @page { size: ${paperSize} auto; margin: 0; }
            body { font-family: 'Courier New', monospace; width: ${width}; margin: 0 auto; padding: 10px; font-size: 12px; }
            .c { text-align: center; } .b { font-weight: bold; }
            .d { border-top: 1px dashed #000; margin: 6px 0; }
            .r { display: flex; justify-content: space-between; }
        </style></head>
        <body>
            <div class="c b" style="font-size:14px">KEE LAUNDRY</div>
            <div class="c">Kee Laundry Pusat</div>
            <div class="d"></div>
            <div class="r"><span>Tanggal:</span><span>${new Date().toLocaleDateString('id-ID')}</span></div>
            <div class="r"><span>Printer:</span><span>${printerName}</span></div>
            <div class="r"><span>Lebar:</span><span>${paperSize}</span></div>
            <div class="d"></div>
            <div class="c b">✓ KONEKSI PRINTER OK!</div>
            <div class="c" style="font-size:10px;margin-top:4px">Kee Laundry ERP v2.0</div>
            <br><br>
        </body></html>
    `;

    if (isNative()) {
        // Di native: kirim ESC/POS bytes langsung
        try {
            const esc = buildTestESC(printerName, paperSize);
            await printESC(esc);
        } catch {
            // fallback: buka window print (mungkin tidak bisa di native)
            openPrintWindow(html);
        }
    } else {
        openPrintWindow(html);
    }
}

function openPrintWindow(html) {
    const win = window.open('', '_blank', 'width=350,height=450');
    if (win) {
        win.document.write(html);
        win.document.close();
        win.focus();
        setTimeout(() => { win.print(); win.close(); }, 400);
    } else {
        window.print();
    }
}

/**
 * buildTestESC - Buat byte array ESC/POS untuk test print
 */
function buildTestESC(printerName, paperSize) {
    const encoder = new TextEncoder();
    const lines = [
        '\x1B\x40',              // Init printer
        '\x1B\x61\x01',         // Center align
        '\x1B\x21\x10',         // Double width
        'KEE LAUNDRY\n',
        '\x1B\x21\x00',         // Normal
        'Kee Laundry Pusat\n',
        '\x1B\x61\x00',         // Left align
        '--------------------------------\n',
        `Tanggal : ${new Date().toLocaleDateString('id-ID')}\n`,
        `Printer : ${printerName}\n`,
        `Lebar   : ${paperSize}\n`,
        '--------------------------------\n',
        '\x1B\x61\x01',         // Center
        '\x1B\x21\x10',         // Bold
        'PRINTER OK!\n',
        '\x1B\x21\x00',         // Normal
        '\n\n\n',
        '\x1D\x56\x41\x03',     // Cut paper
    ];

    const parts = lines.map(l => encoder.encode(l));
    const total = parts.reduce((s, p) => s + p.length, 0);
    const result = new Uint8Array(total);
    let offset = 0;
    for (const p of parts) {
        result.set(p, offset);
        offset += p.length;
    }
    return result;
}

export { isNative };
