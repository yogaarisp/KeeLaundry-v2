<div class="p-0">
    {{-- Thermal receipt style --}}
    <div class="bg-white rounded-2xl overflow-hidden">
        {{-- Header brand --}}
        <div class="bg-sky-600 text-white text-center py-5 px-4">
            <p class="font-bold text-lg tracking-wide">KEE LAUNDRY</p>
            <p class="text-sky-200 text-xs mt-1">Kee Laundry – Pusat</p>
            <p class="text-sky-200 text-xs">WA: 081234567890</p>
        </div>

        {{-- Body --}}
        <div class="px-4 py-4 space-y-3 font-mono text-sm">
            {{-- Invoice info --}}
            <div class="flex justify-between text-xs text-slate-500">
                <span>{{ $order->created_at->format('d/m/Y H:i') }}</span>
                <span class="font-semibold">{{ $order->invoice_number }}</span>
            </div>
            <div class="border-t border-dashed border-slate-300 pt-3">
                <p class="font-semibold text-slate-800">{{ $order->customer?->name }}</p>
                <p class="text-xs text-slate-400">{{ $order->customer?->phone }}</p>
            </div>
            <div class="border-t border-dashed border-slate-300 pt-3 space-y-1">
                <div class="flex justify-between">
                    <span class="text-slate-600">{{ $order->servicePackage?->name }}</span>
                </div>
                <div class="flex justify-between text-xs text-slate-500">
                    <span>{{ $order->quantity }} {{ $order->unit }} × Rp {{ number_format($order->servicePackage?->price_per_unit ?? 0) }}</span>
                    <span>Rp {{ number_format($order->total_price) }}</span>
                </div>
                @if ($order->perfume_variant)
                <p class="text-xs text-slate-400">Parfum: {{ $order->perfume_variant }}</p>
                @endif
                @if ($order->notes)
                <p class="text-xs text-slate-400 italic">Catatan: {{ $order->notes }}</p>
                @endif
            </div>
            <div class="border-t-2 border-dashed border-slate-300 pt-3">
                <div class="flex justify-between font-bold">
                    <span class="text-slate-800">TOTAL</span>
                    <span class="text-sky-600 text-base">Rp {{ number_format($order->total_price) }}</span>
                </div>
                <div class="flex justify-between text-xs text-slate-500 mt-1">
                    <span>{{ $order->payment_method }}</span>
                    <span @class(['font-semibold', 'text-emerald-600' => $order->payment_status === 'LUNAS', 'text-amber-600' => $order->payment_status === 'BELUM_LUNAS'])>
                        {{ $order->payment_status }}
                    </span>
                </div>
            </div>
            {{-- Estimated pickup --}}
            @if ($order->servicePackage?->estimated_hours)
            <div class="border-t border-dashed border-slate-200 pt-3 text-center text-xs text-slate-500">
                <p>Estimasi selesai: <strong class="text-slate-700">{{ $order->created_at->addHours($order->servicePackage->estimated_hours)->format('d M Y, H:i') }}</strong></p>
            </div>
            @endif
            {{-- Status badge --}}
            <div class="text-center pt-1">
                @php $sc = match($order->status) { 'SELESAI' => 'badge-green', 'SIAP_DIAMBIL' => 'badge-sky', 'VOID' => 'badge-rose', default => 'badge-amber' }; @endphp
                <span class="badge {{ $sc }}">{{ $order->status }}</span>
            </div>
            {{-- Footer --}}
            <div class="border-t border-dashed border-slate-200 pt-3 text-center text-[10px] text-slate-400 leading-relaxed">
                <p>Terima kasih telah mempercayakan cucian Anda.</p>
                <p>Komplain maks. 24 jam setelah diambil.</p>
            </div>
        </div>

        {{-- Actions --}}
        <div class="px-4 pb-4 flex flex-col sm:flex-row gap-2">
            <button type="button"
                    onclick="printThermalReceipt()"
                    class="flex-1 flex items-center justify-center gap-2 py-2.5 rounded-xl bg-sky-600 hover:bg-sky-700 text-white text-sm font-bold shadow-sm transition-colors">
                <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0 1 10.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0 .229 2.523a1.125 1.125 0 0 1-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0 0 21 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 0 0-1.913-.247M6.34 18H5.25A2.25 2.25 0 0 1 3 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 0 1 1.913-.247m10.5 0a48.536 48.536 0 0 0-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5Zm-3 0h.008v.008H15V10.5Z"/></svg>
                <span>Cetak Struk</span>
            </button>
            <a href="https://wa.me/62{{ ltrim($order->customer?->phone, '0') }}?text={{ urlencode('Halo ' . ($order->customer?->name ?? '') . '! Cucian Anda dengan nota ' . $order->invoice_number . ' sudah siap diambil. Terima kasih 🧺') }}"
               target="_blank"
               class="flex-1 flex items-center justify-center gap-2 py-2.5 rounded-xl bg-emerald-500 hover:bg-emerald-600 text-white text-sm font-semibold transition-colors">
                <svg class="size-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
                <span>Kirim WA</span>
            </a>
            <button wire:click="closeReceipt" class="btn-secondary py-2.5">Tutup</button>
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
