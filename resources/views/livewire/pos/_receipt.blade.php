<div class="flex flex-col h-full overflow-hidden bg-white">
    {{-- Header Bar --}}
    <div class="flex items-center justify-between px-4 py-3 bg-slate-50 border-b border-slate-100 shrink-0">
        <div class="flex items-center gap-2">
            <button type="button"
                    wire:click="closeReceipt"
                    class="p-1.5 -ml-1 text-slate-600 hover:text-slate-900 hover:bg-slate-200/60 rounded-xl transition-all md:hidden"
                    title="Kembali">
                <svg class="size-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/></svg>
            </button>
            <h3 class="font-bold text-slate-800 text-sm sm:text-base">Struk Transaksi</h3>
        </div>
        <button type="button" wire:click="closeReceipt" class="btn-icon hidden md:flex" title="Tutup">
            <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
        </button>
    </div>

    {{-- Thermal Receipt Body (Scrollable) --}}
    <div class="flex-1 overflow-y-auto p-3 sm:p-4 bg-slate-100/60">
        <div class="bg-white rounded-2xl shadow-xs border border-slate-200/90 overflow-hidden">
            {{-- Brand header --}}
            <div class="bg-sky-600 text-white text-center py-4 px-4">
                <p class="font-extrabold text-base sm:text-lg tracking-wider">KEE LAUNDRY</p>
                <p class="text-sky-100 text-xs mt-0.5">{{ $order->branch?->name ?? 'Kee Laundry – Pusat' }}</p>
                <p class="text-sky-200 text-xs">{{ $order->branch?->phone ?? 'WA: 081234567890' }}</p>
            </div>

            {{-- Receipt content --}}
            <div class="p-4 space-y-3 font-mono text-xs sm:text-sm">
                {{-- Date & Invoice --}}
                <div class="flex justify-between items-center text-xs text-slate-500">
                    <span>{{ $order->created_at->format('d/m/Y H:i') }}</span>
                    <span class="font-bold text-slate-700 bg-slate-100 px-1.5 py-0.5 rounded">{{ $order->invoice_number }}</span>
                </div>

                {{-- Customer --}}
                <div class="border-t border-dashed border-slate-300 pt-2.5">
                    <p class="font-bold text-slate-800 text-sm">{{ $order->customer?->name }}</p>
                    <p class="text-xs text-slate-400">{{ $order->customer?->phone }}</p>
                </div>

                {{-- Item details --}}
                <div class="border-t border-dashed border-slate-300 pt-2.5 space-y-1">
                    <div class="flex justify-between">
                        <span class="font-semibold text-slate-700">{{ $order->servicePackage?->name }}</span>
                    </div>
                    <div class="flex justify-between text-xs text-slate-500">
                        <span>{{ $order->quantity }} {{ $order->unit }} × Rp {{ number_format($order->servicePackage?->price_per_unit ?? 0) }}</span>
                        <span class="font-semibold text-slate-800">Rp {{ number_format($order->total_price) }}</span>
                    </div>
                    @if ($order->perfume_variant)
                        <p class="text-xs text-slate-400">Parfum: {{ $order->perfume_variant }}</p>
                    @endif
                    @if ($order->notes)
                        <p class="text-xs text-amber-600 italic">Catatan: {{ $order->notes }}</p>
                    @endif
                </div>

                {{-- Total & Payment --}}
                <div class="border-t-2 border-dashed border-slate-300 pt-2.5">
                    <div class="flex justify-between items-center font-bold">
                        <span class="text-slate-800 text-sm">TOTAL</span>
                        <span class="text-sky-600 text-base sm:text-lg">Rp {{ number_format($order->total_price) }}</span>
                    </div>
                    <div class="flex justify-between items-center text-xs text-slate-500 mt-1">
                        <span>Bayar: {{ $order->payment_method }}</span>
                        <span @class([
                            'badge text-[10px] font-bold',
                            'badge-green' => $order->payment_status === 'LUNAS',
                            'badge-amber' => $order->payment_status === 'BELUM_LUNAS',
                            'badge-rose'  => $order->payment_status === 'REFUND',
                        ])>
                            {{ $order->payment_status }}
                        </span>
                    </div>
                </div>

                {{-- Estimated Pickup --}}
                @if ($order->servicePackage?->estimated_hours)
                <div class="border-t border-dashed border-slate-200 pt-2.5 text-center text-xs text-slate-500">
                    <p>Estimasi selesai:</p>
                    <p class="font-bold text-slate-700 mt-0.5">{{ $order->created_at->addHours($order->servicePackage->estimated_hours)->format('d M Y, H:i') }}</p>
                </div>
                @endif

                {{-- Status badge --}}
                <div class="text-center pt-1">
                    @php $sc = match($order->status) { 'SELESAI' => 'badge-green', 'SIAP_DIAMBIL' => 'badge-sky', 'VOID' => 'badge-rose', default => 'badge-amber' }; @endphp
                    <span class="badge {{ $sc }} text-xs font-semibold uppercase">{{ $order->status }}</span>
                </div>

                {{-- Footer message --}}
                <div class="border-t border-dashed border-slate-200 pt-2.5 text-center text-[10px] text-slate-400 leading-relaxed">
                    <p>{{ $order->branch?->receipt_footer ?? 'Terima kasih telah mempercayakan cucian Anda.' }}</p>
                    <p>Komplain maks. 24 jam setelah diambil.</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Sticky Action Footer (Always 100% visible, never cut off!) --}}
    <div class="p-3 sm:p-4 bg-white border-t border-slate-100 shrink-0">
        <div class="grid grid-cols-2 gap-2.5">
            <button type="button"
                    onclick="printThermalReceipt()"
                    class="btn-primary py-2.5 sm:py-3 text-xs sm:text-sm font-semibold flex items-center justify-center gap-1.5 shadow-sm">
                <svg class="size-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0 1 10.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0 .229 2.523a1.125 1.125 0 0 1-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0 0 21 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 0 0-1.913-.247M6.34 18H5.25A2.25 2.25 0 0 1 3 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 0 1 1.913-.247m10.5 0a48.536 48.536 0 0 0-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5Zm-3 0h.008v.008H15V10.5Z"/></svg>
                <span>Cetak Struk</span>
            </button>

            @php
                $custPhone = ltrim($order->customer?->phone ?? '', '0');
                $waText = "Halo " . ($order->customer?->name ?? 'Kak') . "! Berikut nota transaksi cucian Anda di Kee Laundry:\n\n"
                        . "🧾 *Nota:* {$order->invoice_number}\n"
                        . "🧺 *Layanan:* " . ($order->servicePackage?->name ?? '-') . " ({$order->quantity} {$order->unit})\n"
                        . "💰 *Total:* Rp " . number_format($order->total_price) . " [{$order->payment_status}]\n"
                        . "🔍 *Cek Progres:* " . url('/cek?q=' . $order->invoice_number) . "\n\n"
                        . "Terima kasih! 🙏";
                $waUrl = $custPhone ? "https://wa.me/62{$custPhone}?text=" . urlencode($waText) : '#';
            @endphp
            <a href="{{ $waUrl }}"
               target="_blank"
               class="flex items-center justify-center gap-1.5 py-2.5 sm:py-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs sm:text-sm font-semibold shadow-sm transition-colors">
                <svg class="size-4 shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
                <span>Kirim WA</span>
            </a>
        </div>
    </div>
</div>

<script>
function printThermalReceipt() {
    const paper = '{{ $order->branch?->printer_paper_size ?? "58mm" }}';
    const width = paper === '80mm' ? '300px' : '220px';

    const printHtml = `
        <html>
        <head>
            <title>Struk #{{ $order->invoice_number }}</title>
            <style>
                @page { size: ${paper} auto; margin: 0; }
                body {
                    font-family: 'Courier New', Courier, monospace;
                    width: ${width};
                    margin: 0 auto;
                    padding: 8px;
                    font-size: 11px;
                    line-height: 1.4;
                    color: #000;
                }
                .center { text-align: center; }
                .right { text-align: right; }
                .dashed { border-top: 1px dashed #000; margin: 6px 0; }
                .bold { font-weight: bold; }
                .flex { display: flex; justify-content: space-between; }
            </style>
        </head>
        <body>
            <div class="center bold" style="font-size: 14px;">KEE LAUNDRY</div>
            <div class="center">{{ $order->branch?->name ?? 'Kee Laundry Pusat' }}</div>
            <div class="center">{{ $order->branch?->phone ?? 'WA: 081234567890' }}</div>
            <div class="dashed"></div>
            <div class="flex">
                <span>{{ $order->created_at->format('d/m/Y H:i') }}</span>
                <span class="bold">{{ $order->invoice_number }}</span>
            </div>
            <div class="dashed"></div>
            <div>Pelanggan: <span class="bold">{{ $order->customer?->name }}</span></div>
            <div>No. Telp: {{ $order->customer?->phone }}</div>
            <div class="dashed"></div>
            <div class="bold">{{ $order->servicePackage?->name }}</div>
            <div class="flex">
                <span>{{ $order->quantity }} {{ $order->unit }} × Rp {{ number_format($order->servicePackage?->price_per_unit ?? 0) }}</span>
                <span>Rp {{ number_format($order->total_price) }}</span>
            </div>
            @if ($order->perfume_variant)
                <div>Parfum: {{ $order->perfume_variant }}</div>
            @endif
            @if ($order->notes)
                <div>Catatan: {{ $order->notes }}</div>
            @endif
            <div class="dashed"></div>
            <div class="flex bold" style="font-size: 13px;">
                <span>TOTAL</span>
                <span>Rp {{ number_format($order->total_price) }}</span>
            </div>
            <div class="flex">
                <span>Bayar ({{ $order->payment_method }})</span>
                <span class="bold">{{ $order->payment_status }}</span>
            </div>
            @if ($order->servicePackage?->estimated_hours)
                <div class="dashed"></div>
                <div class="center">
                    Est. Selesai: {{ $order->created_at->addHours($order->servicePackage->estimated_hours)->format('d M Y, H:i') }}
                </div>
            @endif
            <div class="dashed"></div>
            <div class="center" style="font-size: 9px;">
                <p>{{ $order->branch?->receipt_footer ?? 'Terima kasih telah mempercayakan cucian Anda.' }}</p>
                <p>Komplain maks. 24 jam setelah diambil.</p>
            </div>
            <br><br>
        </body>
        </html>
    `;

    const win = window.open('', '_blank', 'width=350,height=500');
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
</script>
