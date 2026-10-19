@php
$statusColorMap = [
    'ANTREAN'      => ['border' => '#e2e8f0',  'badge' => 'badge-slate'],
    'CUCI'         => ['border' => '#bae6fd',  'badge' => 'badge-sky'],
    'SETRIKA'      => ['border' => '#a5f3fc',  'badge' => 'badge-cyan'],
    'PACKING'      => ['border' => '#fde68a',  'badge' => 'badge-amber'],
    'SIAP_DIAMBIL' => ['border' => '#a7f3d0',  'badge' => 'badge-green'],
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

<div class="bg-white rounded-2xl border-2 transition-all duration-150 hover:shadow-md p-3 space-y-2.5"
     style="border-color: {{ $cfg['border'] }}">

    {{-- Invoice + time --}}
    <div class="flex items-start justify-between gap-1">
        <div class="min-w-0">
            <p class="font-mono text-[11px] font-semibold text-slate-500 truncate">{{ $order->invoice_number }}</p>
            <p class="text-xs text-slate-400">{{ $order->created_at->diffForHumans() }}</p>
        </div>
        @php $payClass = $order->payment_status === 'LUNAS' ? 'badge-green' : 'badge-amber'; @endphp
        <span class="badge {{ $payClass }} text-[10px] shrink-0">{{ $order->payment_status }}</span>
    </div>

    {{-- Customer --}}
    <div>
        <p class="text-sm font-bold text-slate-800 leading-tight">{{ $order->customer?->name }}</p>
        @if ($order->customer?->preferences)
        <div class="flex items-start gap-1 mt-1">
            <span class="text-amber-400 shrink-0">★</span>
            <p class="text-[11px] text-amber-700 italic leading-tight">{{ $order->customer->preferences }}</p>
        </div>
        @endif
    </div>

    {{-- Service info --}}
    <div class="flex items-center gap-2 text-xs text-slate-600">
        <span class="font-semibold">{{ $order->quantity }} {{ $order->unit }}</span>
        <span class="text-slate-300">·</span>
        <span class="truncate">{{ $order->servicePackage?->name }}</span>
    </div>

    {{-- Parfum + Rack --}}
    <div class="flex flex-wrap gap-1">
        <span class="badge badge-slate text-[10px]">{{ $order->perfume_variant }}</span>
        @if ($order->rack_location)
        <span class="badge badge-sky text-[10px]">📦 {{ $order->rack_location }}</span>
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
    <div class="flex gap-1.5 pt-1 flex-wrap">
        @if ($nextStatus && $nextLabel)
        <button wire:click="openUpdateDrawer({{ $order->id }}, '{{ $nextStatus }}')"
                class="flex-1 flex items-center justify-center gap-1.5 text-xs font-semibold py-2 px-2
                       rounded-xl bg-sky-600 text-white hover:bg-sky-700 active:scale-95 transition-all
                       min-h-[36px]">
            <svg class="size-3.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/></svg>
            {{ $nextLabel }}
        </button>
        @endif

        @if ($order->status === 'SIAP_DIAMBIL')
        <button wire:click="confirmFinish({{ $order->id }})"
                class="flex-1 flex items-center justify-center gap-1.5 text-xs font-semibold py-2 px-2
                       rounded-xl bg-emerald-500 text-white hover:bg-emerald-600 active:scale-95 transition-all
                       min-h-[36px]">
            <svg class="size-3.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
            Selesai
        </button>
        @endif

        {{-- Reset + Price --}}
        <div class="flex gap-1">
            <button wire:click="resetToAntrean({{ $order->id }})"
                    class="btn-icon"
                    title="Reset ke Antrean (Cuci Ulang)">
                <svg class="size-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99"/></svg>
            </button>
        </div>
    </div>

    {{-- Total price footer --}}
    <div class="pt-1 border-t border-slate-100 flex items-center justify-between">
        <span class="text-xs text-slate-400">Total tagihan</span>
        <span class="text-sm font-bold text-slate-700 tabular-nums">Rp {{ number_format($order->total_price) }}</span>
    </div>
</div>
