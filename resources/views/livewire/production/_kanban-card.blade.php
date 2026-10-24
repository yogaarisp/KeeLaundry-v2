@php
$statusColorMap = [
    'ANTREAN'      => ['border' => 'border-slate-200',  'dot' => 'bg-slate-400',   'badge' => 'badge-slate'],
    'CUCI'         => ['border' => 'border-sky-200',    'dot' => 'bg-sky-500',     'badge' => 'badge-sky'],
    'SETRIKA'      => ['border' => 'border-cyan-200',   'dot' => 'bg-cyan-500',    'badge' => 'badge-cyan'],
    'PACKING'      => ['border' => 'border-amber-200',  'dot' => 'bg-amber-500',   'badge' => 'badge-amber'],
    'SIAP_DIAMBIL' => ['border' => 'border-emerald-200','dot' => 'bg-emerald-500', 'badge' => 'badge-green'],
];
$cfg = $statusColorMap[$order->status] ?? $statusColorMap['ANTREAN'];

$nextStatus = match($order->status) {
    'ANTREAN'  => 'CUCI',
    'CUCI'     => 'SETRIKA',
    'SETRIKA'  => 'PACKING',
    'PACKING'  => 'SIAP_DIAMBIL',
    default    => null,
};

$nextLabel = match($nextStatus) {
    'CUCI'         => 'Mulai Cuci',
    'SETRIKA'      => 'Setrika',
    'PACKING'      => 'Packing',
    'SIAP_DIAMBIL' => 'Siap Ambil',
    default        => null,
};
@endphp

<div class="bg-white rounded-2xl border {{ $cfg['border'] }} shadow-xs transition-all duration-150 hover:shadow-md p-3.5 space-y-2.5">

    {{-- Invoice + time + Payment status --}}
    <div class="flex items-start justify-between gap-1.5">
        <div class="min-w-0">
            <span class="font-mono text-[11px] font-bold text-slate-600 block truncate">{{ $order->invoice_number }}</span>
            <span class="text-[11px] text-slate-400 block mt-0.5">{{ $order->created_at->diffForHumans() }}</span>
        </div>
        @php $payClass = $order->payment_status === 'LUNAS' ? 'badge-green' : 'badge-amber'; @endphp
        <span class="badge {{ $payClass }} text-[10px] shrink-0 font-semibold">{{ $order->payment_status }}</span>
    </div>

    {{-- Customer Name & Contact --}}
    <div>
        <div class="flex items-center justify-between gap-1">
            <p class="text-sm font-bold text-slate-800 leading-tight truncate">{{ $order->customer?->name }}</p>
            @if ($order->customer?->phone)
            @php $cleanPhone = preg_replace('/^0/', '62', preg_replace('/[^\d]/', '', $order->customer->phone)); @endphp
            <a href="https://wa.me/{{ $cleanPhone }}" target="_blank"
               class="text-emerald-600 hover:text-emerald-700 p-0.5 shrink-0 rounded transition-colors"
               title="Hubungi WhatsApp">
                <svg class="size-3.5" viewBox="0 0 24 24" fill="currentColor"><path d="M12.04 2c-5.46 0-9.91 4.45-9.91 9.91 0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38c1.45.79 3.08 1.21 4.74 1.21 5.46 0 9.91-4.45 9.91-9.91 0-2.65-1.03-5.14-2.9-7.01A9.816 9.816 0 0 0 12.04 2m.01 1.67c4.54 0 8.24 3.7 8.24 8.24 0 2.2-.86 4.28-2.42 5.84a8.18 8.18 0 0 1-5.82 2.41h-.01c-1.42 0-2.82-.37-4.06-1.08l-.29-.17-3.02.79.81-2.94-.19-.3a8.214 8.214 0 0 1-1.26-4.38c0-4.54 3.7-8.24 8.24-8.24M8.53 7.33c-.16 0-.35.06-.53.25-.19.19-.71.7-.71 1.71 0 1.01.73 1.99.83 2.13.1.13 1.44 2.2 3.5 3.09.49.21.87.34 1.17.43.49.16.94.14 1.29.08.4-.06 1.21-.5 1.38-.98.17-.48.17-.9.12-.98-.05-.09-.19-.14-.4-.25s-1.23-.61-1.42-.68c-.19-.07-.33-.1-.47.1-.14.21-.54.68-.66.82-.12.14-.24.16-.45.05-.21-.1-.89-.33-1.69-1.05-.62-.56-1.04-1.25-1.16-1.46-.12-.21-.01-.32.09-.42.09-.09.21-.24.31-.36.1-.12.14-.21.21-.35.07-.14.03-.26-.02-.36-.05-.1-.47-1.14-.65-1.56-.17-.42-.35-.36-.48-.37-.12-.01-.26-.01-.4-.01z"/></svg>
            </a>
            @endif
        </div>
        @if ($order->customer?->preferences)
        <div class="flex items-start gap-1 mt-1 bg-amber-50/80 border border-amber-200/50 rounded-lg px-2 py-1">
            <span class="text-amber-500 shrink-0 text-xs mt-0.5">★</span>
            <p class="text-[11px] text-amber-800 font-medium leading-tight">{{ $order->customer->preferences }}</p>
        </div>
        @endif
    </div>

    {{-- Service info --}}
    <div class="flex items-center gap-1.5 text-xs text-slate-600 bg-slate-50 rounded-xl px-2.5 py-1.5">
        <span class="font-bold text-slate-800">{{ $order->quantity }} {{ $order->unit }}</span>
        <span class="text-slate-300">·</span>
        <span class="truncate font-medium">{{ $order->servicePackage?->name }}</span>
    </div>

    {{-- Parfum + Rack badges --}}
    <div class="flex flex-wrap gap-1">
        <span class="badge badge-slate text-[10px]">🌸 {{ $order->perfume_variant }}</span>
        @if ($order->rack_location)
        <span class="badge badge-sky text-[10px] font-semibold">📦 {{ $order->rack_location }}</span>
        @endif
    </div>

    {{-- PIC badges --}}
    @if ($order->washedBy || $order->ironedBy || $order->packedBy)
    <div class="flex flex-wrap gap-1 text-[10px]">
        @if ($order->washedBy) <span class="badge badge-sky">🧼 {{ $order->washedBy->name }}</span> @endif
        @if ($order->ironedBy) <span class="badge badge-cyan">♨️ {{ $order->ironedBy->name }}</span> @endif
        @if ($order->packedBy) <span class="badge badge-amber">📦 {{ $order->packedBy->name }}</span> @endif
    </div>
    @endif

    {{-- Action buttons --}}
    <div class="flex items-center gap-1.5 pt-1">
        @if ($nextStatus && $nextLabel)
        <button wire:click="openUpdateDrawer({{ $order->id }}, '{{ $nextStatus }}')"
                class="flex-1 min-w-0 flex items-center justify-center gap-1.5 text-xs font-semibold py-2 px-2.5
                       rounded-xl bg-sky-600 text-white hover:bg-sky-700 active:scale-95 transition-all shadow-xs
                       min-h-[38px]">
            <svg class="size-3.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/></svg>
            <span class="truncate">{{ $nextLabel }}</span>
        </button>
        @endif

        @if ($order->status === 'SIAP_DIAMBIL')
        <button wire:click="confirmFinish({{ $order->id }})"
                class="flex-1 min-w-0 flex items-center justify-center gap-1.5 text-xs font-semibold py-2 px-2.5
                       rounded-xl bg-emerald-600 text-white hover:bg-emerald-700 active:scale-95 transition-all shadow-xs
                       min-h-[38px]">
            <svg class="size-3.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
            <span class="truncate">Selesai</span>
        </button>
        @endif

        @if ($order->status !== 'ANTREAN')
        <button wire:click="resetToAntrean({{ $order->id }})"
                class="size-9 shrink-0 rounded-xl bg-slate-50 border border-slate-200 text-slate-400 hover:text-rose-600 hover:border-rose-200 hover:bg-rose-50 flex items-center justify-center transition-all"
                title="Reset ke Antrean (Cuci Ulang)">
            <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99"/></svg>
        </button>
        @endif
    </div>

    {{-- Total price footer --}}
    <div class="pt-2 border-t border-slate-100 flex items-center justify-between text-xs">
        <span class="text-slate-400">Total tagihan</span>
        <span class="font-bold text-slate-700 tabular-nums">Rp {{ number_format($order->total_price) }}</span>
    </div>
</div>
