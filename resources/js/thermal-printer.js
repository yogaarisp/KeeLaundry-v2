/**
 * ThermalPrinterService
 * Ported dari project ds (G:/laragon/www/ds/frontend/src/services/thermalPrinter.js)
 * Mendukung Web Bluetooth API (browser) dan Android Native Bridge (APK Capacitor)
 *
 * Cara pakai:
 *   import thermalPrinter from './thermal-printer'
 *   await thermalPrinter.connect()
 *   await thermalPrinter.printReceipt(receiptData)
 */

class ThermalPrinterService extends EventTarget {
    constructor() {
        super()
        this.device = null
        this.characteristic = null
        this.connected = false
        const savedWidth = localStorage.getItem('printer_width')
        this.printerWidth = savedWidth ? parseInt(savedWidth) : 58
        this.type = 'web' // 'web' | 'native'

        // Auto-reconnect saat load halaman (hanya browser, bukan APK)
        const shouldAutoConnect = localStorage.getItem('printer_auto_reconnect') !== 'false'
        if (!this.isAndroidApp() && this.isBluetoothSupported() && shouldAutoConnect) {
            setTimeout(() => this.autoConnect(), 500)
        }
    }

    /** Apakah berjalan di dalam APK Android dengan Native Bridge */
    isAndroidApp() {
        return !!window.AndroidInterface
    }

    /** Apakah browser mendukung Web Bluetooth */
    isBluetoothSupported() {
        return 'bluetooth' in navigator
    }

    /** Encode string ke Uint8Array */
    encode(str) {
        return new TextEncoder().encode(str)
    }

    // ─── SCAN ────────────────────────────────────────────────────────────────

    /**
     * Scan printer yang sudah di-pairing.
     * - Native: kembalikan list dari AndroidInterface.scanPrinters()
     * - Browser: kembalikan device yang sudah diberi izin (via getDevices())
     */
    async scan() {
        if (this.isAndroidApp()) {
            try {
                const data = window.AndroidInterface.scanPrinters()
                return JSON.parse(data || '[]')
            } catch (err) {
                console.error('[scan] Native scan error:', err)
                return []
            }
        }

        // Browser — kembalikan previously-permitted devices (tanpa popup)
        if ('bluetooth' in navigator && navigator.bluetooth.getDevices) {
            try {
                const devices = await navigator.bluetooth.getDevices()
                return devices.map(d => ({
                    name: d.name || 'Unknown Device',
                    address: d.id,
                    type: 'bluetooth',
                    isWebDevice: true,
                    raw: d,
                }))
            } catch { return [] }
        }
        return []
    }

    // ─── CONNECT ─────────────────────────────────────────────────────────────

    /**
     * Hubungkan ke printer.
     * @param {Object|null} printer - objek printer dari scan(), atau null untuk buka browser picker
     */
    async connect(printer = null) {
        // ── PATH A: Android APK ──────────────────────────────────────────────
        if (this.isAndroidApp()) {
            if (!printer) throw new Error('Printer object wajib diisi untuk koneksi Native')

            let result = ''
            if (printer.type === 'bluetooth') {
                result = window.AndroidInterface.connectBluetoothPrinter(printer.address)
            } else {
                result = window.AndroidInterface.connectUsbPrinter()
            }

            if (result === 'SUCCESS') {
                this.connected = true
                this.type = 'native'
                this._syncNativeSettings()
                this.dispatchEvent(new CustomEvent('connection-changed', { detail: true }))
                return true
            }
            throw new Error(result)
        }

        // ── PATH B: Browser Web Bluetooth ────────────────────────────────────
        if (!this.isBluetoothSupported()) {
            throw new Error('Web Bluetooth tidak didukung. Gunakan Chrome atau Edge, atau buka lewat APK Android.')
        }

        // Jika ada device spesifik dari getDevices() (previously permitted)
        if (printer && printer.isWebDevice && printer.raw) {
            this.device = printer.raw
        } else {
            // Munculkan browser picker
            this.device = await navigator.bluetooth.requestDevice({
                filters: [
                    { services: ['000018f0-0000-1000-8000-00805f9b34fb'] },
                    { namePrefix: 'RPN' },
                    { namePrefix: 'BlueTooth Printer' },
                    { namePrefix: 'MPT' },
                    { namePrefix: 'Printer' },
                    { namePrefix: 'POS' },
                ],
                optionalServices: [
                    '000018f0-0000-1000-8000-00805f9b34fb',
                    '49535343-fe7d-4ae5-8fa9-9fafd205e455',
                    '00001101-0000-1000-8000-00805f9b34fb',
                ],
            })
        }

        await this._connectToDevice(this.device)
        localStorage.setItem('printer_auto_reconnect', 'true')
        return true
    }

    /**
     * Low-level: connect ke device, discover GATT service & characteristic
     */
    async _connectToDevice(device) {
        this.device = device

        // Retry koneksi GATT sampai 3x (handle stale connection)
        let server = null
        for (let i = 1; i <= 3; i++) {
            try {
                server = await device.gatt.connect()
                break
            } catch (err) {
                if (err.name === 'NetworkError' && i < 3) {
                    console.warn(`[connect] Stale connection, retry ${i}/3...`)
                    await new Promise(r => setTimeout(r, 1000))
                } else {
                    throw err
                }
            }
        }

        // Cari service yang cocok
        const serviceUUIDs = [
            '000018f0-0000-1000-8000-00805f9b34fb',
            '49535343-fe7d-4ae5-8fa9-9fafd205e455',
        ]
        let service = null
        for (const uuid of serviceUUIDs) {
            try {
                service = await server.getPrimaryService(uuid)
                if (service) break
            } catch { continue }
        }

        if (!service) throw new Error('Service printer tidak ditemukan. Pastikan printer mendukung ESC/POS BLE.')

        // Cari write characteristic
        const characteristics = await service.getCharacteristics()
        this.characteristic = characteristics.find(
            c => c.properties.write || c.properties.writeWithoutResponse
        )

        if (!this.characteristic) throw new Error('Write characteristic tidak ditemukan.')

        this.connected = true
        this.type = 'web'
        this.dispatchEvent(new CustomEvent('connection-changed', { detail: true }))

        // Auto-reconnect jika putus tidak sengaja
        device.addEventListener('gattserverdisconnected', () => {
            this.connected = false
            this.characteristic = null
            this.dispatchEvent(new CustomEvent('connection-changed', { detail: false }))

            if (localStorage.getItem('printer_auto_reconnect') !== 'false') {
                console.warn('[printer] Disconnected unexpectedly, attempting reconnect...')
                this._reconnectWithRetry(device, 5, 1000)
            }
        })
    }

    // ─── AUTO RECONNECT ──────────────────────────────────────────────────────

    async autoConnect() {
        if (this.isAndroidApp() || !this.isBluetoothSupported()) return false
        if (localStorage.getItem('printer_auto_reconnect') === 'false') return false

        try {
            if (!navigator.bluetooth.getDevices) return false
            const devices = await navigator.bluetooth.getDevices()
            if (!devices.length) return false

            const device = devices[0]
            console.log('[autoConnect] Reconnecting to:', device.name)

            if (device.watchAdvertisements) {
                return await this._connectViaAdvertisement(device)
            }
            return await this._reconnectWithRetry(device, 5, 1500)
        } catch (err) {
            console.warn('[autoConnect] Failed:', err.message)
            return false
        }
    }

    _connectViaAdvertisement(device) {
        return new Promise((resolve) => {
            const timeout = setTimeout(() => {
                this._reconnectWithRetry(device, 3, 1500).then(resolve).catch(() => resolve(false))
            }, 2000)

            const onAd = async () => {
                clearTimeout(timeout)
                device.removeEventListener('advertisementreceived', onAd)
                try {
                    await this._connectToDevice(device)
                    resolve(true)
                } catch { resolve(false) }
            }

            device.addEventListener('advertisementreceived', onAd)
            device.watchAdvertisements().catch(() => {
                clearTimeout(timeout)
                this._reconnectWithRetry(device, 3, 1500).then(resolve).catch(() => resolve(false))
            })
        })
    }

    async _reconnectWithRetry(device, maxRetries = 5, delayMs = 1500) {
        for (let i = 1; i <= maxRetries; i++) {
            try {
                console.log(`[reconnect] Attempt ${i}/${maxRetries}`)
                await this._connectToDevice(device)
                return true
            } catch (err) {
                console.warn(`[reconnect] Attempt ${i} failed:`, err.message)
                if (i < maxRetries) await new Promise(r => setTimeout(r, delayMs))
            }
        }
        return false
    }

    // ─── DISCONNECT ──────────────────────────────────────────────────────────

    async disconnect() {
        localStorage.setItem('printer_auto_reconnect', 'false')

        if (this.isAndroidApp()) {
            if (window.AndroidInterface?.disconnectPrinter) {
                window.AndroidInterface.disconnectPrinter()
            }
            this.connected = false
            this.dispatchEvent(new CustomEvent('connection-changed', { detail: false }))
            return
        }

        if (this.device) {
            try {
                if (this.device.gatt?.connected) await this.device.gatt.disconnect()
                if (typeof this.device.forget === 'function') await this.device.forget()
            } catch (err) {
                console.warn('[disconnect] Cleanup error:', err)
            }
        }

        this.device = null
        this.characteristic = null
        this.connected = false
        this.dispatchEvent(new CustomEvent('connection-changed', { detail: false }))
    }

    // ─── SETTINGS ────────────────────────────────────────────────────────────

    setPrinterWidth(width) {
        this.printerWidth = parseInt(width)
        localStorage.setItem('printer_width', String(width))

        if (this.connected && this.type === 'native' && this.isAndroidApp()) {
            try {
                window.AndroidInterface.setPrinterSettings(
                    203,
                    this.printerWidth === 58 ? 48.0 : 72.0,
                    this.printerWidth === 58 ? 32 : 48
                )
            } catch { }
        }
    }

    _syncNativeSettings() {
        if (this.isAndroidApp() && window.AndroidInterface?.setPrinterSettings) {
            window.AndroidInterface.setPrinterSettings(
                203,
                this.printerWidth === 58 ? 48.0 : 72.0,
                this.printerWidth === 58 ? 32 : 48
            )
        }
    }

    // ─── SEND DATA ───────────────────────────────────────────────────────────

    async sendData(data) {
        if (!this.connected || !this.characteristic) {
            throw new Error('Printer belum terhubung')
        }

        const chunkSize = 512
        for (let i = 0; i < data.length; i += chunkSize) {
            const chunk = data.slice(i, i + chunkSize)
            await this.characteristic.writeValue(chunk)
            await new Promise(r => setTimeout(r, 50))
        }
    }

    // ─── PRINT ───────────────────────────────────────────────────────────────

    async printReceipt(receiptData) {
        if (this.isAndroidApp() && this.type === 'native') {
            const text = this._buildNativeText(receiptData)
            const result = window.AndroidInterface.printFormattedText(text)
            if (result !== 'SUCCESS') throw new Error(result)
            return true
        }

        const buffer = this._buildESCPOSBuffer(receiptData)
        await this.sendData(buffer)
        return true
    }

    async testPrint() {
        const testData = {
            storeName: 'KEE LAUNDRY',
            storeAddress: 'Test Print — Printer OK',
            storePhone: '',
            transactionNumber: 'TEST-PRINT',
            date: new Date().toLocaleString('id-ID'),
            cashier: 'System',
            customerName: 'Test User',
            items: [
                { name: 'Koneksi Printer', quantity: 1, price: 0, subtotal: 0 },
                { name: 'Status: BERHASIL', quantity: 1, price: 0, subtotal: 0 },
            ],
            subtotal: 0, discount: 0, tax: 0, total: 0,
            paid: 0, change: 0, paymentMethod: 'Test',
        }
        await this.printReceipt(testData)
    }

    // ─── ESC/POS BUFFER ──────────────────────────────────────────────────────

    _buildESCPOSBuffer(data) {
        const ESC = 0x1B
        const GS = 0x1D
        const enc = new TextEncoder()
        const cmd = []

        const cols = this.printerWidth === 58 ? 32 : 48
        const pad = (txt) => ' '.repeat(Math.max(0, Math.floor((cols - txt.length) / 2)))

        const storeName    = data.storeName    || 'KEE LAUNDRY'
        const storeAddress = data.storeAddress || ''
        const storePhone   = data.storePhone   || ''

        // Init
        cmd.push(ESC, 0x40)
        cmd.push(ESC, 0x74, 0x00)

        // Header
        if (this.printerWidth === 58) {
            cmd.push(ESC, 0x61, 0x00) // left
            cmd.push(ESC, 0x45, 0x01, GS, 0x21, 0x01)
            cmd.push(...enc.encode(pad(storeName) + storeName + '\n'))
            cmd.push(GS, 0x21, 0x00, ESC, 0x45, 0x00)
            if (storeAddress) cmd.push(...enc.encode(pad(storeAddress) + storeAddress + '\n'))
            if (storePhone) {
                const wa = `WA: ${storePhone}`
                cmd.push(...enc.encode(pad(wa) + wa + '\n'))
            }
        } else {
            cmd.push(ESC, 0x61, 0x01) // center
            cmd.push(ESC, 0x45, 0x01, GS, 0x21, 0x11)
            cmd.push(...enc.encode(storeName + '\n'))
            cmd.push(GS, 0x21, 0x00, ESC, 0x45, 0x00)
            if (storeAddress) cmd.push(...enc.encode(storeAddress + '\n'))
            if (storePhone) cmd.push(...enc.encode(`WA: ${storePhone}\n`))
        }

        // Separator
        cmd.push(ESC, 0x61, 0x00)
        cmd.push(...enc.encode('-'.repeat(cols) + '\n'))

        // Info transaksi
        cmd.push(...enc.encode(`No    : ${data.transactionNumber || '-'}\n`))
        cmd.push(...enc.encode(`Tgl   : ${data.date || '-'}\n`))
        cmd.push(...enc.encode(`Kasir : ${data.cashier || '-'}\n`))
        if (data.customerName) cmd.push(...enc.encode(`Pelanggan: ${data.customerName}\n`))
        if (data.tableName)    cmd.push(...enc.encode(`Meja : ${data.tableName}\n`))

        cmd.push(...enc.encode('-'.repeat(cols) + '\n'))

        // Items
        for (const item of data.items) {
            cmd.push(...enc.encode(item.name + '\n'))
            const left  = `  ${item.quantity} x ${this._rp(item.price)}`
            const right = this._rp(item.subtotal)
            const space = ' '.repeat(Math.max(1, cols - left.length - right.length))
            cmd.push(...enc.encode(left + space + right + '\n'))
        }

        cmd.push(...enc.encode('-'.repeat(cols) + '\n'))

        // Totals
        this._addLine(cmd, enc, cols, 'Subtotal:', this._rp(data.subtotal))
        if (data.discount > 0) this._addLine(cmd, enc, cols, 'Diskon:', `-${this._rp(data.discount)}`)
        if (data.tax > 0)      this._addLine(cmd, enc, cols, 'Pajak:', this._rp(data.tax))

        cmd.push(ESC, 0x45, 0x01) // bold on
        this._addLine(cmd, enc, cols, 'TOTAL:', this._rp(data.total))
        cmd.push(ESC, 0x45, 0x00) // bold off

        this._addLine(cmd, enc, cols, 'Metode:', data.paymentMethod || 'Cash')
        cmd.push(0x0A)
        this._addLine(cmd, enc, cols, 'Bayar:', this._rp(data.paid))
        this._addLine(cmd, enc, cols, 'Kembali:', this._rp(data.change))

        // Footer
        cmd.push(0x0A)
        if (this.printerWidth === 58) {
            cmd.push(ESC, 0x61, 0x00)
            cmd.push(...enc.encode(pad('Terima Kasih') + 'Terima Kasih\n'))
            cmd.push(...enc.encode(pad('Simpan struk sebagai bukti') + 'Simpan struk sebagai bukti\n'))
        } else {
            cmd.push(ESC, 0x61, 0x01)
            cmd.push(...enc.encode('Terima Kasih\n'))
            cmd.push(...enc.encode('Simpan struk sebagai bukti\n'))
        }

        cmd.push(0x0A, 0x0A)
        cmd.push(GS, 0x56, 0x00) // Cut paper

        return new Uint8Array(cmd)
    }

    _addLine(cmd, enc, cols, label, value) {
        const space = ' '.repeat(Math.max(1, cols - label.length - value.length))
        cmd.push(...enc.encode(`${label}${space}${value}\n`))
    }

    _rp(value) {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0,
        }).format(value || 0).replace(/\s/g, ' ')
    }

    // ─── NATIVE FORMAT TEXT ──────────────────────────────────────────────────

    _buildNativeText(data) {
        const cols = this.printerWidth === 58 ? 32 : 48
        const pad  = (txt) => ' '.repeat(Math.max(0, Math.floor((cols - txt.length) / 2)))
        const div  = '[C]' + '-'.repeat(cols) + '\n'
        const isSmall = this.printerWidth === 58

        const storeName    = data.storeName    || 'KEE LAUNDRY'
        const storeAddress = data.storeAddress || ''
        const storePhone   = data.storePhone   || ''

        let t = ''

        if (isSmall) {
            t += `[L]<b>${pad(storeName)}${storeName}</b>\n`
            if (storeAddress) t += `[L]<font size='small'>${pad(storeAddress)}${storeAddress}</font>\n`
            if (storePhone) {
                const wa = `WA: ${storePhone}`
                t += `[L]<font size='small'>${pad(wa)}${wa}</font>\n`
            }
        } else {
            t += `[C]<b><font size='big'>${storeName}</font></b>\n`
            if (storeAddress) t += `[C]${storeAddress}\n`
            if (storePhone) t += `[C]WA: ${storePhone}\n`
        }

        t += div
        t += `[L]No    : ${data.transactionNumber || '-'}\n`
        t += `[L]Tgl   : ${data.date || '-'}\n`
        t += `[L]Kasir : ${data.cashier || '-'}\n`
        if (data.customerName) t += `[L]Pelanggan: ${data.customerName}\n`
        if (data.tableName)    t += `[L]Meja : ${data.tableName}\n`
        t += div

        for (const item of data.items) {
            t += `[L]<b>${item.name}</b>\n`
            t += `[L]  ${item.quantity} x ${this._rp(item.price)}[R]${this._rp(item.subtotal)}\n`
        }

        t += div
        t += `[L]Subtotal:[R]${this._rp(data.subtotal)}\n`
        if (data.discount > 0) t += `[L]Diskon:[R]-${this._rp(data.discount)}\n`
        if (data.tax > 0)      t += `[L]Pajak:[R]${this._rp(data.tax)}\n`
        t += `[L]<b>TOTAL:[R]${this._rp(data.total)}</b>\n`
        t += `[L]Metode:[R]${data.paymentMethod || 'Cash'}\n\n`
        t += `[L]Bayar:[R]${this._rp(data.paid)}\n`
        t += `[L]Kembali:[R]${this._rp(data.change)}\n`

        if (isSmall) {
            t += `[L]${pad('Terima Kasih')}Terima Kasih\n`
            t += `[L]${pad('Simpan struk sebagai bukti')}Simpan struk sebagai bukti\n`
        } else {
            t += `[C]Terima Kasih\n`
            t += `[C]Simpan struk sebagai bukti\n`
        }

        return t
    }
}

// Singleton export
const thermalPrinter = new ThermalPrinterService()
export default thermalPrinter
export { thermalPrinter }
