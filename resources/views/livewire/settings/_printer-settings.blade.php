<div class="space-y-5" x-data="thermalPrinterManager()" data-paper="{{ $printer_paper_size }}">

    {{-- ══ BAGIAN 1: STATUS PRINTER (Hero Card) ══════════════════════ --}}
    <div class="card overflow-hidden">
        {{-- Header strip --}}
        <div class="px-5 pt-5 pb-4 border-b border-slate-100 flex items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <div class="size-9 rounded-xl bg-sky-50 border border-sky-200/70 text-sky-600 flex items-center justify-center shadow-2xs shrink-0">
                    <svg class="size-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0 1 10.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0 .229 2.523a1.125 1.125 0 0 1-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0 0 21 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 0 0-1.913-.247M6.34 18H5.25A2.25 2.25 0 0 1 3 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 0 1 1.913-.247m10.5 0a48.536 48.536 0 0 0-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5Zm-3 0h.008v.008H15V10.5Z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-800 leading-tight">Printer Thermal POS</h3>
                    <p class="text-[11px] text-slate-400 mt-0.5">Koneksi &amp; setelan cetak struk kasir</p>
                </div>
            </div>

            {{-- Status Pill --}}
            <div class="shrink-0 flex items-center gap-1.5 rounded-full px-3 py-1 border text-[11px] font-bold transition-all"
                 :class="connectionStatus === 'connected'
                     ? 'bg-emerald-50 border-emerald-200 text-emerald-700'
                     : 'bg-slate-50 border-slate-200 text-slate-500'">
                <span class="size-2 rounded-full shrink-0"
                      :class="connectionStatus === 'connected' ? 'bg-emerald-500 animate-pulse' : 'bg-slate-300'"></span>
                <span x-text="connectionStatus === 'connected' ? 'Terhubung' : 'Tidak Terhubung'"></span>
            </div>
        </div>

        {{-- Body: Info & Actions --}}
        <div class="p-5 space-y-4">
            {{-- Printer device info row --}}
            <div class="flex items-center gap-4 p-4 rounded-2xl border transition-all"
                 :class="connectionStatus === 'connected'
                     ? 'bg-emerald-50/50 border-emerald-200/80'
                     : 'bg-slate-50/80 border-slate-200 border-dashed'">
                {{-- Printer Icon Box --}}
                <div class="size-12 rounded-2xl flex items-center justify-center shrink-0 border transition-all"
                     :class="connectionStatus === 'connected'
                         ? 'bg-emerald-100 border-emerald-200 text-emerald-600'
                         : 'bg-white border-slate-200 text-slate-300'">
                    <svg class="size-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0 1 10.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0 .229 2.523a1.125 1.125 0 0 1-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0 0 21 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 0 0-1.913-.247M6.34 18H5.25A2.25 2.25 0 0 1 3 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 0 1 1.913-.247m10.5 0a48.536 48.536 0 0 0-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5Zm-3 0h.008v.008H15V10.5Z"/>
                    </svg>
                </div>

                {{-- Info --}}
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-bold leading-tight transition-colors"
                       :class="connectionStatus === 'connected' ? 'text-emerald-800' : 'text-slate-400'"
                       x-text="connectionStatus === 'connected' ? (deviceName || 'Printer Bluetooth') : 'Belum ada printer terhubung'">
                    </p>
                    <p class="text-[11px] mt-0.5 transition-colors"
                       :class="connectionStatus === 'connected' ? 'text-emerald-600' : 'text-slate-400'"
                       x-text="connectionStatus === 'connected' ? 'Siap mencetak struk transaksi' : 'Tap Scan untuk mencari printer Bluetooth di sekitar'">
                    </p>
                </div>

                {{-- Tombol Putuskan --}}
                <button x-show="connectionStatus === 'connected'"
                        x-cloak
                        @click="disconnect()"
                        type="button"
                        class="shrink-0 text-xs font-semibold text-rose-600 hover:text-rose-700 bg-white hover:bg-rose-50 border border-rose-200 px-3 py-1.5 rounded-xl transition-colors shadow-2xs">
                    Putuskan
                </button>
            </div>

            {{-- Action Buttons --}}
            <div class="flex items-center gap-2.5">
                {{-- Scan & Connect --}}
                <button type="button"
                        @click="scanAndConnect()"
                        :disabled="isConnecting"
                        class="btn-primary flex-1 justify-center py-2.5 gap-2 text-sm disabled:opacity-60">
                    <template x-if="!isConnecting">
                        <svg class="size-4.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.348 14.652a3.75 3.75 0 0 1 0-5.304m5.304 0a3.75 3.75 0 0 1 0 5.304m-7.425 2.121a6.75 6.75 0 0 1 0-9.546m9.546 0a6.75 6.75 0 0 1 0 9.546M5.106 18.894c-3.808-3.808-3.808-9.98 0-13.789m13.788 0c3.808 3.808 3.808 9.98 0 13.788M12 12h.008v.008H12V12Z"/>
                        </svg>
                    </template>
                    <template x-if="isConnecting">
                        <svg class="size-4.5 animate-spin shrink-0" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                        </svg>
                    </template>
                    <span x-text="isConnecting ? 'Mencari Printer…' : 'Scan & Hubungkan'"></span>
                </button>

                {{-- Test Print --}}
                <button type="button"
                        @click="testPrint()"
                        class="btn-secondary py-2.5 px-4 gap-2 shrink-0">
                    <svg class="size-4 text-slate-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0 1 10.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0 .229 2.523a1.125 1.125 0 0 1-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0 0 21 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 0 0-1.913-.247M6.34 18H5.25A2.25 2.25 0 0 1 3 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 0 1 1.913-.247m10.5 0a48.536 48.536 0 0 0-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5Zm-3 0h.008v.008H15V10.5Z"/>
                    </svg>
                    <span class="hidden sm:inline">Test Print</span>
                </button>

                {{-- USB --}}
                <button type="button"
                        @click="pairUsb()"
                        class="btn-icon shrink-0" title="Hubungkan via USB">
                    <svg class="size-4.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 0 0 6 3.75v16.5a2.25 2.25 0 0 0 2.25 2.25h7.5A2.25 2.25 0 0 0 18 20.25V3.75a2.25 2.25 0 0 0-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18h3"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    {{-- ══ BAGIAN 2: SETELAN CETAK ════════════════════════════════════ --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

        {{-- Kiri: Ukuran Kertas --}}
        <div class="card p-5 space-y-4">
            <div class="border-b border-slate-100 pb-3 flex items-center gap-2">
                <div class="size-7 rounded-lg bg-slate-50 border border-slate-200 text-slate-500 flex items-center justify-center shrink-0">
                    <svg class="size-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"/>
                    </svg>
                </div>
                <div>
                    <h4 class="text-xs font-bold text-slate-800">Ukuran Kertas</h4>
                    <p class="text-[11px] text-slate-400">Lebar roll thermal printer</p>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                {{-- 58mm --}}
                <button type="button"
                        wire:click="$set('printer_paper_size', '58mm')"
                        @click="$nextTick(() => @this.savePrinter())"
                        @class([
                            'relative group flex flex-col items-center justify-center gap-1 p-4 rounded-2xl min-h-[90px] border-2 transition-all cursor-pointer',
                            'border-sky-500 bg-sky-50/50 shadow-xs' => $printer_paper_size === '58mm',
                            'border-slate-200 bg-white hover:border-slate-300 hover:bg-slate-50' => $printer_paper_size !== '58mm',
                        ])>
                    <span @class(['text-xl font-extrabold tracking-tight leading-none', 'text-sky-600' => $printer_paper_size === '58mm', 'text-slate-400' => $printer_paper_size !== '58mm'])>58mm</span>
                    <span @class(['text-[10px] font-bold tracking-widest uppercase', 'text-sky-400' => $printer_paper_size === '58mm', 'text-slate-300' => $printer_paper_size !== '58mm'])>Standard</span>
                    <span @class(['text-[10px] font-semibold', 'text-sky-500' => $printer_paper_size === '58mm', 'text-slate-300' => $printer_paper_size !== '58mm'])>Kasir Umum</span>
                    @if ($printer_paper_size === '58mm')
                        <span class="absolute top-2 right-2 size-5 rounded-full bg-sky-500 text-white flex items-center justify-center text-[10px]">✓</span>
                    @endif
                </button>

                {{-- 80mm --}}
                <button type="button"
                        wire:click="$set('printer_paper_size', '80mm')"
                        @click="$nextTick(() => @this.savePrinter())"
                        @class([
                            'relative group flex flex-col items-center justify-center gap-1 p-4 rounded-2xl min-h-[90px] border-2 transition-all cursor-pointer',
                            'border-sky-500 bg-sky-50/50 shadow-xs' => $printer_paper_size === '80mm',
                            'border-slate-200 bg-white hover:border-slate-300 hover:bg-slate-50' => $printer_paper_size !== '80mm',
                        ])>
                    <span @class(['text-xl font-extrabold tracking-tight leading-none', 'text-sky-600' => $printer_paper_size === '80mm', 'text-slate-400' => $printer_paper_size !== '80mm'])>80mm</span>
                    <span @class(['text-[10px] font-bold tracking-widest uppercase', 'text-sky-400' => $printer_paper_size === '80mm', 'text-slate-300' => $printer_paper_size !== '80mm'])>Large</span>
                    <span @class(['text-[10px] font-semibold', 'text-sky-500' => $printer_paper_size === '80mm', 'text-slate-300' => $printer_paper_size !== '80mm'])>Kasir Besar</span>
                    @if ($printer_paper_size === '80mm')
                        <span class="absolute top-2 right-2 size-5 rounded-full bg-sky-500 text-white flex items-center justify-center text-[10px]">✓</span>
                    @endif
                </button>
            </div>
        </div>

        {{-- Kanan: Auto Print & Metode --}}
        <div class="card p-5 space-y-4">
            <div class="border-b border-slate-100 pb-3 flex items-center gap-2">
                <div class="size-7 rounded-lg bg-slate-50 border border-slate-200 text-slate-500 flex items-center justify-center shrink-0">
                    <svg class="size-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 0 1 0 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 0 1-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 0 1-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 0 1-1.369-.49l-1.297-2.247a1.125 1.125 0 0 1 .26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 0 1 0-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 0 1-.26-1.43l1.297-2.247a1.125 1.125 0 0 1 1.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28Z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                    </svg>
                </div>
                <div>
                    <h4 class="text-xs font-bold text-slate-800">Opsi Cetak</h4>
                    <p class="text-[11px] text-slate-400">Setelan perilaku cetak otomatis</p>
                </div>
            </div>

            {{-- Auto Print Toggle --}}
            <div class="flex items-center justify-between p-3.5 rounded-xl border transition-all"
                 :class="$wire.printer_auto_print ? 'bg-sky-50/60 border-sky-200/80' : 'bg-slate-50 border-slate-200'">
                <div class="space-y-0.5 pr-3">
                    <p class="text-xs font-bold text-slate-800">Auto Print Struk</p>
                    <p class="text-[11px] text-slate-400 leading-relaxed">Cetak otomatis setelah transaksi selesai</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer shrink-0">
                    <input type="checkbox"
                           wire:model.live="printer_auto_print"
                           @change="$nextTick(() => @this.savePrinter())"
                           class="sr-only peer">
                    <div class="w-11 h-6 bg-slate-200 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-sky-500"></div>
                </label>
            </div>

            {{-- Metode Aktif & Alternatif --}}
            <div class="space-y-2">
                <div class="flex items-center justify-between px-1">
                    <span class="text-[11px] text-slate-400 font-semibold">Metode cetak aktif</span>
                    <span class="badge badge-slate text-[10px] font-mono font-bold">{{ $printer_type }}</span>
                </div>
                <div class="flex items-center gap-2 flex-wrap">
                    <button type="button" @click="pairUsb()"
                            class="flex items-center gap-1.5 text-[11px] font-semibold text-slate-600 hover:text-sky-700 bg-white hover:bg-sky-50 border border-slate-200 hover:border-sky-200 px-2.5 py-1 rounded-lg transition-colors shadow-2xs">
                        <svg class="size-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 0 0 6 3.75v16.5a2.25 2.25 0 0 0 2.25 2.25h7.5A2.25 2.25 0 0 0 18 20.25V3.75a2.25 2.25 0 0 0-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18h3"/></svg>
                        USB Kabel
                    </button>
                    <button type="button" wire:click="$set('printer_type', 'SYSTEM')" @click="$nextTick(() => @this.savePrinter())"
                            class="flex items-center gap-1.5 text-[11px] font-semibold text-slate-600 hover:text-sky-700 bg-white hover:bg-sky-50 border border-slate-200 hover:border-sky-200 px-2.5 py-1 rounded-lg transition-colors shadow-2xs">
                        <svg class="size-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17.25v1.007a3 3 0 0 1-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0 1 15 18.257V17.25m6-12V15a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 15V5.25m18 0A2.25 2.25 0 0 0 18.75 3H5.25A2.25 2.25 0 0 0 3 5.25m18 0H3"/></svg>
                        System Dialog
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- ══ BAGIAN 3: PETUNJUK PENGGUNAAN ══════════════════════════════ --}}
    <div class="card p-5">
        <div class="flex items-start gap-3.5">
            <div class="size-8 rounded-xl bg-amber-50 border border-amber-200/70 text-amber-600 flex items-center justify-center shrink-0">
                <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z"/>
                </svg>
            </div>
            <div class="flex-1 space-y-3">
                <div>
                    <p class="text-xs font-bold text-slate-800">Petunjuk Penggunaan</p>
                    <p class="text-[11px] text-slate-400 mt-0.5">Pastikan kondisi berikut sebelum menghubungkan printer</p>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-2">
                    @foreach ([
                        ['icon' => '📶', 'text' => 'Aktifkan Bluetooth di perangkat Anda'],
                        ['icon' => '🖨️', 'text' => 'Printer mendukung perintah ESC/POS'],
                        ['icon' => '🌐', 'text' => 'Gunakan Google Chrome atau Microsoft Edge'],
                        ['icon' => '🔗', 'text' => 'Printer sudah di-pairing lewat pengaturan HP/laptop'],
                    ] as $hint)
                    <div class="flex items-start gap-2.5">
                        <span class="text-sm shrink-0 leading-tight">{{ $hint['icon'] }}</span>
                        <span class="text-[11px] text-slate-600 leading-relaxed">{{ $hint['text'] }}</span>
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
        deviceName: localStorage.getItem('thermal_printer_name') || '',
        isConnecting: false,

        async scanAndConnect() {
            this.isConnecting = true;
            try {
                const result = await window.ThermalPrinter.scanAndConnect();
                this.deviceName = result.name;
                this.connectionStatus = 'connected';
                localStorage.setItem('thermal_printer_connected', 'true');
                localStorage.setItem('thermal_printer_name', this.deviceName);
                localStorage.setItem('thermal_printer_type', 'BLUETOOTH');
                await $wire.updatePairedDevice('BLUETOOTH', this.deviceName);
                window.showToast({ type: 'success', title: 'Printer Terhubung', message: 'Berhasil: ' + this.deviceName });
            } catch (err) {
                if (err && err.message === 'NO_WEB_BLUETOOTH') {
                    window.showToast({ type: 'error', title: 'Bluetooth Tidak Didukung', message: 'Gunakan APK Android atau Chrome/Edge desktop' });
                } else if (err && err.message && !err.message.toLowerCase().includes('cancel')) {
                    window.showToast({ type: 'error', title: 'Gagal Terhubung', message: err.message });
                }
                console.warn('BT error:', err);
            } finally {
                this.isConnecting = false;
            }
        },

        async disconnect() {
            try { await window.ThermalPrinter.disconnect(); } catch(e) {}
            this.connectionStatus = 'disconnected';
            this.deviceName = '';
            localStorage.removeItem('thermal_printer_connected');
            localStorage.removeItem('thermal_printer_name');
            $wire.updatePairedDevice('SYSTEM', '');
            window.showToast({ type: 'info', title: 'Printer Terputus', message: 'Koneksi printer telah direset.' });
        },

        async pairUsb() {
            if ('serial' in navigator) {
                this.isConnecting = true;
                try {
                    const port = await navigator.serial.requestPort();
                    await port.open({ baudRate: 9600 });
                    this.deviceName = 'USB Thermal Printer';
                    this.connectionStatus = 'connected';
                    localStorage.setItem('thermal_printer_connected', 'true');
                    localStorage.setItem('thermal_printer_name', this.deviceName);
                    await $wire.updatePairedDevice('USB', this.deviceName);
                    window.showToast({ type: 'success', title: 'USB Terhubung', message: 'Port serial printer siap.' });
                } catch (err) {
                    console.warn('USB error:', err);
                } finally {
                    this.isConnecting = false;
                }
            } else {
                window.showToast({ type: 'error', title: 'USB Tidak Didukung', message: 'Web Serial tidak tersedia di browser ini.' });
            }
        },

        async testPrint() {
            const paper = this.$el.dataset.paper || '58mm';
            try {
                await window.ThermalPrinter.printTestPage(this.deviceName || 'Thermal Printer', paper);
                window.showToast({ type: 'success', title: 'Test Print Dikirim', message: 'Cek printer apakah mencetak.' });
            } catch (err) {
                if (err && err.message === 'BROWSER_FALLBACK') {
                    this._openBrowserPrint(paper);
                } else {
                    console.warn('Test print error:', err);
                    this._openBrowserPrint(paper);
                }
            }
        },

        _openBrowserPrint(paper) {
            const w = paper === '80mm' ? '300px' : '220px';
            const name = this.deviceName || 'Thermal Printer';
            const html = '<html><head><title>Test Print</title><style>@page{size:' + paper + ' auto;margin:0}body{font-family:Courier New,monospace;width:' + w + ';margin:0 auto;padding:10px;font-size:12px}.c{text-align:center}.b{font-weight:bold}.d{border-top:1px dashed #000;margin:6px 0}.r{display:flex;justify-content:space-between}</style></head><body><div class="c b" style="font-size:14px">KEE LAUNDRY</div><div class="c">Kee Laundry Pusat</div><div class="d"></div><div class="r"><span>Tanggal:</span><span>' + new Date().toLocaleDateString('id-ID') + '</span></div><div class="r"><span>Printer:</span><span>' + name + '</span></div><div class="r"><span>Lebar:</span><span>' + paper + '</span></div><div class="d"></div><div class="c b">PRINTER OK!</div><br><br></body></html>';
            const win = window.open('', '_blank', 'width=350,height=450');
            if (win) { win.document.write(html); win.document.close(); win.focus(); setTimeout(function(){ win.print(); win.close(); }, 400); }
            else { window.print(); }
        }
    };
}
</script>