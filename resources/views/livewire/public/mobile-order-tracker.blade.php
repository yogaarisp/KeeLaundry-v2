<div class="min-h-[100dvh] flex flex-col">

{{-- ══ APP-LIKE HEADER ═════════════════════════════════════════════ --}}
<header class="sticky top-0 z-30 bg-white/90 backdrop-blur-md border-b border-slate-200/60">
    <div class="max-w-lg mx-auto px-4 h-14 flex items-center gap-3">
        <div class="flex items-center justify-center size-8 rounded-xl bg-sky-600 text-white shrink-0">
            <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                <path stroke-linecap="round" stroke-linejoin="round" d="M9.5 9.5a3.5 3.5 0 1 0 4.95 4.95"/>
            </svg>
        </div>
        <div class="flex-1 min-w-0">
            <p class="text-sm font-bold text-slate-800 truncate">
                {{ $this->branch?->name ?? 'Kee Laundry' }}
            </p>
            <p class="text-[11px] text-slate-400">Lacak status cucian Anda</p>
        </div>
        @if ($this->branch?->phone)
        <a href="https://wa.me/62{{ ltrim($this->branch->phone, '0') }}"
           target="_blank"
           class="flex items-center justify-center size-9 rounded-xl bg-emerald-50 text-emerald-600 hover:bg-emerald-100 transition-colors shrink-0">
            <svg class="size-5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
        </a>
        @endif
    </div>
</header>

{{-- ══ MAIN ════════════════════════════════════════════════════════ --}}
<main class="flex-1 max-w-lg mx-auto w-full px-4 py-6 space-y-5">

    {{-- Search Card --}}
    <div class="card p-5 space-y-4">
        <div class="text-center">
            <div class="inline-flex items-center justify-center size-16 rounded-2xl bg-sky-50 border-2 border-sky-100 mb-3">
                <svg class="size-8 text-sky-500" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m5.231 13.481L15 17.25m-4.5-15H5.625c-.621 0-1.125.504-1.125 1.125v16.5c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Zm3.75 11.625a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z"/>
                </svg>
            </div>
            <h1 class="text-lg font-extrabold text-slate-800">Lacak Status Cucian</h1>
            <p class="text-sm text-slate-400 mt-1">Masukkan nomor nota yang Anda terima dari kasir</p>
        </div>

        <div class="relative" x-data="{ focused: false }">
            <input type="text"
                   wire:model="invoiceInput"
                   wire:keydown.enter="track"
                   @focus="focused = true" @blur="focused = false"
                   placeholder="INV-20261019-0001"
                   class="form-input text-center font-mono text-base tracking-wider uppercase"
                   :class="focused ? 'border-sky-400 ring-2 ring-sky-100' : ''"
                   autocomplete="off"
                   autocorrect="off"
                   spellcheck="false">
        </div>

        <button wire:click="track"
                wire:loading.attr="disabled"
                class="btn-primary w-full justify-center py-3 text-base rounded-2xl"
                @class(['opacity-50 cursor-not-allowed' => !trim($invoiceInput)])>
            <span wire:loading.remove wire:target="track" class="flex items-center justify-center gap-2">
                <svg class="size-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/></svg>
                <span>Lacak Sekarang</span>
            </span>
            <span wire:loading wire:target="track" class="flex items-center justify-center gap-2">
                <svg class="size-5 animate-spin shrink-0" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                <span>Mencari…</span>
            </span>
        </button>
    </div>

    {{-- ══ RESULT: NOT FOUND ═════════════════════════════════════════ --}}
    @if ($searched && !$this->order)
    <div class="card p-8 text-center space-y-3">
        <div class="text-5xl">🔍</div>
        <p class="font-bold text-slate-800">Nota Tidak Ditemukan</p>
        <p class="text-sm text-slate-400 leading-relaxed">
            Nomor nota <code class="font-mono bg-slate-100 px-2 py-0.5 rounded-lg text-sky-600">{{ $trackedInvoice }}</code>
            tidak ditemukan atau sudah dibatalkan.
        </p>
        <p class="text-xs text-slate-400">Pastikan nomor nota sudah benar atau hubungi kasir kami.</p>
        <button wire:click="resetSearch" class="btn-secondary mx-auto mt-2">Coba Lagi</button>
    </div>
    @endif

    {{-- ══ RESULT: ORDER FOUND ══════════════════════════════════════ --}}
    @if ($this->order)
    @php $order = $this->order; @endphp

    {{-- Order Summary Card --}}
    <div class="card overflow-hidden">
        {{-- Status banner --}}
        @php
            $bannerConfig = match($order->status) {
                'SIAP_DIAMBIL' => ['bg' => 'bg-emerald-500', 'text' => 'Cucian Anda Siap Diambil! 🎉', 'sub' => 'Silakan datang ke toko sekarang'],
                'SELESAI'      => ['bg' => 'bg-sky-600',     'text' => 'Sudah Diambil ✓',              'sub' => 'Terima kasih sudah menggunakan layanan kami'],
                'ANTREAN'      => ['bg' => 'bg-slate-500',   'text' => 'Order Diterima 📥',             'sub' => 'Cucian Anda sedang dalam antrian'],
                'CUCI'         => ['bg' => 'bg-sky-500',     'text' => 'Sedang Dicuci 🫧',              'sub' => 'Proses pencucian sedang berlangsung'],
                'SETRIKA'      => ['bg' => 'bg-cyan-500',    'text' => 'Sedang Disetrika ♨️',           'sub' => 'Cucian sedang disetrika dengan rapi'],
                'PACKING'      => ['bg' => 'bg-amber-500',   'text' => 'QC & Packing 📦',               'sub' => 'Pengecekan kualitas dan pengemasan'],
                default        => ['bg' => 'bg-slate-500',   'text' => $order->status,                 'sub' => ''],
            };
        @endphp
        <div class="{{ $bannerConfig['bg'] }} text-white text-center py-5 px-4">
            <p class="text-xl font-extrabold">{{ $bannerConfig['text'] }}</p>
            <p class="text-sm text-white/80 mt-1">{{ $bannerConfig['sub'] }}</p>
        </div>

        {{-- Order details --}}
        <div class="p-5 space-y-4">
            {{-- Invoice + Customer --}}
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="font-mono text-xs font-bold text-slate-500">{{ $order->invoice_number }}</p>
                    <p class="text-lg font-extrabold text-slate-800 mt-0.5">{{ $order->customer?->name }}</p>
                    <p class="text-sm text-slate-400">Masuk: {{ $order->created_at->locale('id')->isoFormat('D MMM YYYY, HH:mm') }}</p>
                    <p class="text-xs text-slate-400">{{ $this->timeElapsed }}</p>
                </div>
                @php $tierClass = match($order->customer?->tier) { 'VIP' => 'badge-sky', 'SILVER' => 'badge-slate', default => 'badge-slate' }; @endphp
                <span class="badge {{ $tierClass }} shrink-0">{{ $order->customer?->tier }}</span>
            </div>

            {{-- Service + payment --}}
            <div class="grid grid-cols-2 gap-3">
                <div class="bg-slate-50 rounded-2xl p-3">
                    <p class="text-[11px] text-slate-400 font-semibold uppercase tracking-wide">Layanan</p>
                    <p class="font-bold text-slate-800 mt-1 text-sm">{{ $order->servicePackage?->name }}</p>
                    <p class="text-xs text-slate-500">{{ $order->quantity }} {{ $order->unit }} · {{ $order->perfume_variant }}</p>
                </div>
                <div class="bg-slate-50 rounded-2xl p-3">
                    <p class="text-[11px] text-slate-400 font-semibold uppercase tracking-wide">Tagihan</p>
                    <p class="font-extrabold text-sky-600 mt-1 tabular-nums text-lg">Rp {{ number_format($order->total_price) }}</p>
                    @php $payClass = $order->payment_status === 'LUNAS' ? 'badge-green' : 'badge-amber'; @endphp
                    <span class="badge {{ $payClass }} text-[10px] mt-1">{{ $order->payment_status }}</span>
                </div>
            </div>

            {{-- Rack location (if set) --}}
            @if ($order->rack_location)
            <div class="flex items-center gap-3 rounded-2xl bg-sky-50 border-2 border-sky-200 px-4 py-3">
                <div class="size-10 rounded-xl bg-sky-600 text-white flex items-center justify-center shrink-0">
                    <svg class="size-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z"/></svg>
                </div>
                <div>
                    <p class="text-xs font-bold text-sky-700">Lokasi Rak Cucian</p>
                    <p class="text-xl font-extrabold text-sky-800">{{ $order->rack_location }}</p>
                </div>
            </div>
            @endif

            {{-- ETA --}}
            @if ($this->estimatedReady)
            <div class="flex items-center gap-2 text-xs text-slate-500">
                <svg class="size-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                <span>Estimasi selesai: <strong class="text-slate-700">{{ $this->estimatedReady }}</strong></span>
            </div>
            @endif

            {{-- Notes --}}
            @if ($order->notes)
            <div class="rounded-xl bg-amber-50 border border-amber-100 px-3 py-2.5">
                <p class="text-xs font-bold text-amber-700 mb-1">📝 Catatan Order</p>
                <p class="text-xs text-amber-800">{{ $order->notes }}</p>
            </div>
            @endif
        </div>
    </div>

    {{-- ══ PROGRESS TRACKER ════════════════════════════════════════ --}}
    <div class="card p-5">
        <p class="text-sm font-bold text-slate-800 mb-5">Progress Pencucian</p>
        <div class="relative">
            {{-- Vertical progress line --}}
            <div class="absolute left-5 top-5 bottom-5 w-0.5 bg-slate-200"></div>

            <div class="space-y-4">
                @foreach ($this->progressSteps as $step)
                <div class="flex items-start gap-4 relative">
                    {{-- Step circle --}}
                    <div class="relative z-10 shrink-0">
                        @if ($step['done'])
                            <div class="size-10 rounded-full bg-emerald-500 flex items-center justify-center shadow-sm shadow-emerald-200">
                                <svg class="size-5 text-white" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
                            </div>
                        @elseif ($step['active'])
                            <div class="size-10 rounded-full bg-sky-600 flex items-center justify-center shadow-md shadow-sky-200 ring-4 ring-sky-100">
                                <span class="text-lg leading-none">{{ $step['icon'] }}</span>
                            </div>
                        @else
                            <div class="size-10 rounded-full bg-slate-100 border-2 border-slate-200 flex items-center justify-center">
                                <span class="text-base opacity-40">{{ $step['icon'] }}</span>
                            </div>
                        @endif
                    </div>

                    {{-- Step content --}}
                    <div @class(['pt-2 flex-1', 'opacity-40' => $step['pending']])>
                        <p @class(['text-sm font-bold',
                            'text-emerald-700' => $step['done'],
                            'text-sky-700'     => $step['active'],
                            'text-slate-500'   => $step['pending']])>
                            {{ $step['label'] }}
                            @if ($step['active'])
                                <span class="ml-2 badge badge-sky text-[10px] animate-pulse">Sekarang</span>
                            @endif
                        </p>
                        @if ($step['active'])
                            <p class="text-xs text-slate-400 mt-0.5">{{ $step['desc'] }}</p>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Actions --}}
    <div class="space-y-3">
        {{-- Share link --}}
        <div class="card p-4 flex items-center gap-3">
            <div class="flex items-center justify-center size-10 rounded-xl bg-slate-100 text-slate-500 shrink-0">
                <svg class="size-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7.217 10.907a2.25 2.25 0 1 0 0 2.186m0-2.186c.18.324.283.696.283 1.093s-.103.77-.283 1.093m0-2.186 9.566-5.314m-9.566 7.5 9.566 5.314m0 0a2.25 2.25 0 1 0 3.935 2.186 2.25 2.25 0 0 0-3.935-2.186Zm0-12.814a2.25 2.25 0 1 0 3.933-2.185 2.25 2.25 0 0 0-3.933 2.185Z"/></svg>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold text-slate-700">Bagikan Link Tracking</p>
                <p class="text-xs text-slate-400 truncate">{{ url('/cek?invoice=' . $order->invoice_number) }}</p>
            </div>
            <button x-data x-on:click="navigator.clipboard.writeText('{{ url('/cek?invoice=' . $order->invoice_number) }}').then(() => $dispatch('toast', { type: 'success', title: 'Disalin!', message: 'Link tracking berhasil disalin.' }))"
                    class="btn-secondary text-xs py-2 px-3 shrink-0">Salin</button>
        </div>

        {{-- WA button --}}
        @if ($this->branch?->phone)
        <a href="https://wa.me/62{{ ltrim($this->branch->phone, '0') }}?text={{ urlencode('Halo Kee Laundry! Saya ingin menanyakan status cucian dengan nota ' . $order->invoice_number . '. Terima kasih.') }}"
           target="_blank"
           class="flex items-center justify-center gap-2 w-full py-3 rounded-2xl bg-emerald-500 text-white font-semibold hover:bg-emerald-600 transition-colors">
            <svg class="size-5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
            Hubungi Kee Laundry
        </a>
        @endif

        <button wire:click="resetSearch" class="btn-secondary w-full justify-center py-2.5">
            Cek Nota Lain
        </button>
    </div>
    @endif

    {{-- ══ EMPTY STATE (before search) ════════════════════════════ --}}
    @if (!$searched)
    <div class="card p-6 text-center space-y-4">
        <div class="grid grid-cols-3 gap-3">
            @foreach (['📥 Antrean', '🫧 Cuci', '♨️ Setrika', '📦 Packing', '✅ Siap Ambil', '🎉 Selesai'] as $step)
            <div class="bg-slate-50 rounded-xl py-2.5 px-2 text-xs text-slate-500">{{ $step }}</div>
            @endforeach
        </div>
        <p class="text-xs text-slate-400">
            Masukkan nomor nota di atas untuk melihat progres cucian Anda secara real-time
        </p>
    </div>
    @endif

</main>

{{-- ══ FOOTER ══════════════════════════════════════════════════════ --}}
<footer class="text-center py-5 px-4">
    <p class="text-xs text-slate-400">
        © {{ date('Y') }} {{ $this->branch?->name ?? 'Kee Laundry' }} · Powered by Kee Laundry ERP
    </p>
    @if ($this->branch?->operational_hours)
    <p class="text-xs text-slate-400 mt-1">🕐 {{ $this->branch->operational_hours }}</p>
    @endif
</footer>

</div>
