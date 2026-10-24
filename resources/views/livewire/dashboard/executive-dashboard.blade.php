<div
    x-data="executiveDashboard(@js($this->dailyOmzetChart))"
    x-init="initChart()"
    class="space-y-5"
>

{{-- ══════════════════════════════════════════════════════════════
     HEADER: Period Selector + Refresh (Responsive Layout)
══════════════════════════════════════════════════════════════════ --}}
<div class="space-y-3">
    <div class="flex items-center justify-between gap-3">
        <div>
            <h2 class="text-lg sm:text-xl font-bold text-slate-800 leading-tight">Executive Dashboard</h2>
            <p class="text-xs text-slate-400 mt-0.5">Cabang Utama · Real-time</p>
        </div>

        {{-- Refresh Button --}}
        <button wire:click="refresh" class="btn-icon shrink-0" title="Refresh data"
                wire:loading.class="animate-spin" wire:target="refresh">
            <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99"/>
            </svg>
        </button>
    </div>

    {{-- Period Selector (4-column Segmented Control on mobile, fit on desktop) --}}
    <div class="grid grid-cols-4 sm:flex sm:w-fit bg-slate-100 rounded-xl p-1 gap-1">
        @foreach (['today' => 'Hari Ini', '7d' => '7 Hari', 'mtd' => 'Bulan Ini', '30d' => '30 Hari'] as $key => $label)
            <button
                wire:click="setPeriod('{{ $key }}')"
                @class([
                    'px-2.5 sm:px-3 py-1.5 rounded-lg text-xs font-semibold text-center transition-all duration-150',
                    'bg-white shadow-xs text-sky-700' => $period === $key,
                    'text-slate-500 hover:text-slate-700' => $period !== $key,
                ])
            >{{ $label }}</button>
        @endforeach
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════
     KPI METRICS: Hero Omzet Card + 3 Balanced Sub-Metrics Grid
══════════════════════════════════════════════════════════════════ --}}
<div class="space-y-3 lg:space-y-0 lg:grid lg:grid-cols-4 lg:gap-3">

    {{-- Omzet (Hero Card on Mobile, Col 1 on Desktop) --}}
    <div class="kpi-card lg:col-span-1 p-4 sm:p-5">
        <div class="flex items-center justify-between">
            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wide">Omzet</span>
            <div class="flex items-center justify-center size-8 rounded-xl bg-sky-50 text-sky-600">
                <x-icon name="currency-dollar" class="size-4"/>
            </div>
        </div>
        <p class="text-2xl sm:text-3xl font-extrabold text-slate-800 mt-1.5 tabular-nums" wire:loading.class="opacity-40">
            Rp {{ number_format($this->omzetPeriod) }}
        </p>
        @php
            $omzetChange = $this->omzetLastPeriod > 0
                ? round((($this->omzetPeriod - $this->omzetLastPeriod) / $this->omzetLastPeriod) * 100, 1)
                : 0;
        @endphp
        <div class="flex items-center gap-1 mt-1.5">
            @if ($omzetChange >= 0)
                <x-icon name="arrow-trending-up" class="size-3.5 text-emerald-500"/>
                <span class="text-xs font-semibold text-emerald-600">+{{ $omzetChange }}%</span>
            @else
                <x-icon name="arrow-trending-down" class="size-3.5 text-rose-500"/>
                <span class="text-xs font-semibold text-rose-600">{{ $omzetChange }}%</span>
            @endif
            <span class="text-xs text-slate-400">vs periode lalu</span>
        </div>
    </div>

    {{-- 3 Sub-Metrics (3-Column Equal Grid on Mobile, 3 Cols on Desktop) --}}
    <div class="grid grid-cols-3 gap-2 sm:gap-3 lg:col-span-3 lg:grid-cols-3">
        {{-- Jumlah Order --}}
        <div class="kpi-card p-3 sm:p-5 flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-[10px] sm:text-xs font-semibold text-slate-400 uppercase tracking-wide truncate">Order</span>
                <div class="flex items-center justify-center size-6 sm:size-8 rounded-lg sm:rounded-xl bg-emerald-50 text-emerald-600 shrink-0">
                    <x-icon name="check-circle" class="size-3.5 sm:size-4"/>
                </div>
            </div>
            <div class="mt-2">
                <p class="text-lg sm:text-2xl font-bold text-slate-800 tabular-nums leading-none">{{ $this->orderCountPeriod }}</p>
                <p class="text-[10px] sm:text-xs text-slate-400 mt-1 truncate">nota masuk</p>
            </div>
        </div>

        {{-- Total Kg --}}
        <div class="kpi-card p-3 sm:p-5 flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-[10px] sm:text-xs font-semibold text-slate-400 uppercase tracking-wide truncate">Total Kg</span>
                <div class="flex items-center justify-center size-6 sm:size-8 rounded-lg sm:rounded-xl bg-cyan-50 text-cyan-600 shrink-0">
                    <x-icon name="scale" class="size-3.5 sm:size-4"/>
                </div>
            </div>
            <div class="mt-2">
                <p class="text-lg sm:text-2xl font-bold text-slate-800 tabular-nums leading-none">{{ number_format($this->totalKgPeriod, 1) }}</p>
                <p class="text-[10px] sm:text-xs text-slate-400 mt-1 truncate">Kg tercuci</p>
            </div>
        </div>

        {{-- Avg Order Value --}}
        <div class="kpi-card p-3 sm:p-5 flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-[10px] sm:text-xs font-semibold text-slate-400 uppercase tracking-wide truncate">Avg Order</span>
                <div class="flex items-center justify-center size-6 sm:size-8 rounded-lg sm:rounded-xl bg-amber-50 text-amber-600 shrink-0">
                    <x-icon name="chart-bar" class="size-3.5 sm:size-4"/>
                </div>
            </div>
            <div class="mt-2">
                <p class="text-base sm:text-2xl font-bold text-slate-800 tabular-nums leading-none">
                    <span class="sm:hidden">Rp {{ number_format($this->avgOrderValue / 1000, 0) }}k</span>
                    <span class="hidden sm:inline">Rp {{ number_format($this->avgOrderValue) }}</span>
                </p>
                <p class="text-[10px] sm:text-xs text-slate-400 mt-1 truncate">rata-rata</p>
            </div>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════
     KPI ROW 2: HPP per Kg + Alert Cards
══════════════════════════════════════════════════════════════════ --}}
<div class="grid grid-cols-1 sm:grid-cols-3 gap-3">

    {{-- HPP per Kg Card (highlight) --}}
    <div class="card p-4 sm:col-span-1 flex flex-col gap-3">
        <div class="flex items-start justify-between gap-2">
            <div>
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide">HPP / Kg Real-time</p>
                <p class="text-3xl font-extrabold tabular-nums mt-1
                    {{ $this->hppVariance > 0 ? 'text-rose-600' : 'text-emerald-600' }}">
                    Rp {{ number_format($this->hppPerKg) }}
                </p>
            </div>
            <div class="flex flex-col items-end gap-1 shrink-0">
                <span class="badge {{ $this->hppVariance > 0 ? 'badge-rose' : 'badge-green' }}">
                    {{ $this->hppVariance > 0 ? '+' : '' }}{{ number_format($this->hppVariance) }}/Kg
                </span>
                <span class="text-xs text-slate-400">Target: Rp {{ number_format($this->targetHppPerKg) }}</span>
            </div>
        </div>

        {{-- HPP Breakdown Mini --}}
        <div class="space-y-1.5 pt-2 border-t border-slate-100">
            @php
                $totalCostMtd = $this->bomCostMtd + $this->utilityCostMtd + $this->payrollCostMtd;
            @endphp
            @foreach ([
                ['BOM (Sabun+Parfum)', $this->bomCostMtd,       'bg-sky-400'],
                ['Utilitas (Listrik/Air/Gas)', $this->utilityCostMtd, 'bg-cyan-400'],
                ['Gaji & Komisi', $this->payrollCostMtd,  'bg-amber-400'],
            ] as [$label, $cost, $color])
                <div class="flex items-center gap-2">
                    <div class="size-2 rounded-full {{ $color }} shrink-0"></div>
                    <span class="text-xs text-slate-500 flex-1 truncate">{{ $label }}</span>
                    <span class="text-xs font-semibold text-slate-700 tabular-nums">
                        Rp {{ number_format((int) $cost) }}
                    </span>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Kanban Summary --}}
    <div class="card p-4 flex flex-col gap-3">
        <div class="flex items-center justify-between">
            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide">Kanban Aktif</p>
            <span class="badge badge-sky">{{ $this->activeOrdersTotal }} order</span>
        </div>
        <div class="space-y-2">
            @php
                $kanbanColors = [
                    'ANTREAN'      => ['bg' => 'bg-slate-200',   'text' => 'Antrean'],
                    'CUCI'         => ['bg' => 'bg-sky-400',     'text' => 'Cuci & Kering'],
                    'SETRIKA'      => ['bg' => 'bg-cyan-400',    'text' => 'Setrika'],
                    'PACKING'      => ['bg' => 'bg-amber-400',   'text' => 'Packing'],
                    'SIAP_DIAMBIL' => ['bg' => 'bg-emerald-400', 'text' => 'Siap Diambil'],
                ];
                $maxCount = max(1, max(array_column($this->kanbanCounts, 'count')));
            @endphp
            @foreach ($this->kanbanCounts as $item)
                <div class="flex items-center gap-2">
                    <span class="text-xs text-slate-500 w-24 shrink-0 truncate">{{ $kanbanColors[$item['status']]['text'] ?? $item['status'] }}</span>
                    <div class="flex-1 stock-bar-track">
                        <div class="h-full rounded-full {{ $kanbanColors[$item['status']]['bg'] ?? 'bg-sky-400' }} transition-all duration-500"
                             style="width: {{ $item['count'] > 0 ? round(($item['count'] / $maxCount) * 100) : 0 }}%"></div>
                    </div>
                    <span class="text-xs font-bold text-slate-700 tabular-nums w-5 text-right">{{ $item['count'] }}</span>
                </div>
            @endforeach
        </div>
        {{-- Unpaid --}}
        @if ($this->unpaidOrdersTotal > 0)
        <div class="flex items-center gap-2 rounded-xl bg-amber-50 border border-amber-200 px-3 py-2 mt-1">
            <x-icon name="exclamation-triangle" class="size-4 text-amber-500 shrink-0"/>
            <div class="flex-1 min-w-0">
                <p class="text-xs font-semibold text-amber-700">{{ $this->unpaidOrdersTotal }} belum lunas</p>
                <p class="text-xs text-amber-500 truncate">Rp {{ number_format($this->unpaidRevenue) }} tertunda</p>
            </div>
        </div>
        @endif
    </div>

    {{-- Complaints + Churn Quick Stats --}}
    <div class="card p-4 flex flex-col gap-3">
        <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide">Alert Toko</p>

        <a href="/pelanggan?tab=churn" wire:navigate
           class="flex items-center gap-3 rounded-xl p-3 bg-slate-50 hover:bg-sky-50 border border-slate-200/60 hover:border-sky-200 transition-colors group">
            <div class="flex items-center justify-center size-9 rounded-xl bg-rose-50 text-rose-500 group-hover:bg-rose-100 transition-colors">
                <x-icon name="user-group" class="size-5"/>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold text-slate-800">{{ $this->churnCount }} Pelanggan Churn</p>
                <p class="text-xs text-slate-400">Tidak order &gt;14 hari</p>
            </div>
            @if ($this->churnCount > 0)
                <span class="badge badge-rose">{{ $this->churnCount }}</span>
            @endif
        </a>

        <a href="/komplain" wire:navigate
           class="flex items-center gap-3 rounded-xl p-3 bg-slate-50 hover:bg-sky-50 border border-slate-200/60 hover:border-sky-200 transition-colors group">
            <div class="flex items-center justify-center size-9 rounded-xl bg-amber-50 text-amber-500 group-hover:bg-amber-100 transition-colors">
                <x-icon name="chat-bubble-left" class="size-5"/>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold text-slate-800">{{ $this->openComplaintsCount }} Komplain Aktif</p>
                <p class="text-xs text-slate-400">Open + In Progress</p>
            </div>
            @if ($this->openComplaintsCount > 0)
                <span class="badge badge-amber">{{ $this->openComplaintsCount }}</span>
            @endif
        </a>

        <div class="flex items-center gap-3 rounded-xl p-3 bg-slate-50 border border-slate-200/60">
            <div class="flex items-center justify-center size-9 rounded-xl bg-sky-50 text-sky-500">
                <x-icon name="identification" class="size-5"/>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold text-slate-800">5 Karyawan Aktif</p>
                <p class="text-xs text-slate-400">Gaji: Rp {{ number_format($this->payrollCostMtd) }}</p>
            </div>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════
     CHART: Omzet Harian
══════════════════════════════════════════════════════════════════ --}}
<div class="card p-4" wire:ignore>
    <div class="flex items-center justify-between gap-3 mb-4 flex-wrap">
        <div>
            <p class="text-sm font-bold text-slate-800">Tren Omzet Harian</p>
            <p class="text-xs text-slate-400 mt-0.5">
                {{ match($period) {
                    'today' => 'Hari ini',
                    '7d'    => '7 hari terakhir',
                    '30d'   => '30 hari terakhir',
                    'mtd'   => 'Bulan berjalan',
                    default => ''
                } }}
            </p>
        </div>
        <div class="grid grid-cols-3 sm:flex sm:w-fit w-full bg-slate-100 rounded-xl p-1 gap-1">
            <button @click="chartMode = 'omzet'; updateChart()"
                    :class="chartMode === 'omzet' ? 'bg-white shadow-xs text-sky-700' : 'text-slate-500 hover:text-slate-700'"
                    class="px-2 sm:px-3 py-1.5 rounded-lg text-xs font-semibold transition-all text-center">
                <span class="sm:hidden">Omzet</span>
                <span class="hidden sm:inline">Omzet (Rp)</span>
            </button>
            <button @click="chartMode = 'kg'; updateChart()"
                    :class="chartMode === 'kg' ? 'bg-white shadow-xs text-sky-700' : 'text-slate-500 hover:text-slate-700'"
                    class="px-2 sm:px-3 py-1.5 rounded-lg text-xs font-semibold transition-all text-center">
                <span class="sm:hidden">Kg</span>
                <span class="hidden sm:inline">Volume (Kg)</span>
            </button>
            <button @click="chartMode = 'count'; updateChart()"
                    :class="chartMode === 'count' ? 'bg-white shadow-xs text-sky-700' : 'text-slate-500 hover:text-slate-700'"
                    class="px-2 sm:px-3 py-1.5 rounded-lg text-xs font-semibold transition-all text-center">
                <span class="sm:hidden">Order</span>
                <span class="hidden sm:inline">Jumlah Order</span>
            </button>
        </div>
    </div>
    <div class="relative h-52 sm:h-64">
        <canvas id="omzetChart"></canvas>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════
     INVENTORY STOCK BARS
══════════════════════════════════════════════════════════════════ --}}
<div class="card p-4">
    <div class="flex items-center justify-between mb-4">
        <div>
            <p class="text-sm font-bold text-slate-800">Stok Bahan Baku</p>
            <p class="text-xs text-slate-400 mt-0.5">Sabun, softener & parfum · Auto-deduct aktif</p>
        </div>
        <a href="/inventaris" wire:navigate class="btn-secondary text-xs py-1.5 px-3">
            Kelola Stok
        </a>
    </div>

    <div class="space-y-3.5">
        @foreach ($this->inventoryBars as $item)
        <div>
            <div class="flex items-center justify-between mb-1.5">
                <div class="flex items-center gap-2 min-w-0 flex-1">
                    {{-- Category dot --}}
                    <span @class([
                        'size-2 rounded-full shrink-0',
                        'bg-sky-400'   => $item['category'] === 'CHEMICAL',
                        'bg-violet-400' => $item['category'] === 'PARFUM',
                    ])></span>
                    <span class="text-sm font-medium text-slate-700 truncate">{{ $item['name'] }}</span>
                    @if ($item['is_low'])
                        <span class="badge badge-rose text-[10px] shrink-0">
                            <x-icon name="exclamation-triangle" class="size-3"/>
                            Menipis
                        </span>
                    @endif
                </div>
                <div class="text-right shrink-0 ml-3">
                    <span class="text-sm font-bold tabular-nums
                        {{ $item['color'] === 'rose' ? 'text-rose-600' : ($item['color'] === 'amber' ? 'text-amber-600' : 'text-emerald-600') }}">
                        {{ number_format($item['system_stock'], 0) }}
                    </span>
                    <span class="text-xs text-slate-400"> {{ $item['unit'] }}</span>
                </div>
            </div>
            {{-- Bar --}}
            <div class="stock-bar-track">
                <div
                    class="h-full rounded-full transition-all duration-700 ease-out
                        {{ $item['color'] === 'rose' ? 'bg-rose-400' : ($item['color'] === 'amber' ? 'bg-amber-400' : 'bg-emerald-400') }}"
                    style="width: {{ max(3, $item['percent']) }}%"
                ></div>
            </div>
            <div class="flex items-center justify-between mt-1">
                <span class="text-[10px] text-slate-400">Min: {{ number_format($item['min_threshold'], 0) }} {{ $item['unit'] }}</span>
                <span class="text-[10px] font-semibold
                    {{ $item['color'] === 'rose' ? 'text-rose-500' : ($item['color'] === 'amber' ? 'text-amber-500' : 'text-slate-400') }}">
                    {{ $item['percent'] }}%
                </span>
            </div>
        </div>
        @endforeach
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════
     CRM CHURN ALERT
══════════════════════════════════════════════════════════════════ --}}
@if ($this->churnCustomers->isNotEmpty())
<div class="card p-4">
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-2">
            <div class="flex items-center justify-center size-7 rounded-lg bg-rose-50 text-rose-500">
                <x-icon name="exclamation-triangle" class="size-4"/>
            </div>
            <div>
                <p class="text-sm font-bold text-slate-800">Churn Alert — Pelanggan Pasif</p>
                <p class="text-xs text-slate-400 mt-0.5">{{ $this->churnCount }} pelanggan tidak order &gt;14 hari</p>
            </div>
        </div>
        <a href="/pelanggan?tab=churn" wire:navigate class="btn-secondary text-xs py-1.5 px-3">Lihat Semua</a>
    </div>

    {{-- Mobile: card list; Desktop: table --}}

    {{-- Desktop Table --}}
    <div class="hidden md:block overflow-x-auto -mx-1">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-slate-100">
                    <th class="text-left px-3 py-2 text-xs font-semibold text-slate-400 uppercase tracking-wide">Pelanggan</th>
                    <th class="text-left px-3 py-2 text-xs font-semibold text-slate-400 uppercase tracking-wide">Tier</th>
                    <th class="text-left px-3 py-2 text-xs font-semibold text-slate-400 uppercase tracking-wide">Terakhir Order</th>
                    <th class="text-left px-3 py-2 text-xs font-semibold text-slate-400 uppercase tracking-wide">Sisa Deposit</th>
                    <th class="px-3 py-2"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @foreach ($this->churnCustomers as $c)
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-3 py-3">
                        <div class="flex items-center gap-2">
                            <div class="size-8 rounded-full bg-gradient-to-br from-sky-100 to-sky-200 flex items-center justify-center shrink-0">
                                <span class="text-xs font-bold text-sky-700">{{ mb_strtoupper(mb_substr($c['name'], 0, 1)) }}</span>
                            </div>
                            <div>
                                <p class="font-semibold text-slate-800">{{ $c['name'] }}</p>
                                <p class="text-xs text-slate-400">{{ $c['phone'] }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-3 py-3">
                        <span @class([
                            'badge',
                            'badge-sky'   => $c['tier'] === 'VIP',
                            'badge-slate' => $c['tier'] === 'SILVER',
                            'badge-slate' => $c['tier'] === 'REGULAR',
                        ])>{{ $c['tier'] }}</span>
                    </td>
                    <td class="px-3 py-3">
                        @if ($c['days_ago'] !== null)
                            <span class="badge {{ $c['days_ago'] > 30 ? 'badge-rose' : 'badge-amber' }}">
                                {{ $c['days_ago'] }} hari lalu
                            </span>
                        @else
                            <span class="text-xs text-slate-400 italic">Belum pernah order</span>
                        @endif
                    </td>
                    <td class="px-3 py-3">
                        @if ($c['deposit_kg'] > 0)
                            <span class="text-sm font-semibold text-emerald-600 tabular-nums">{{ $c['deposit_kg'] }} Kg</span>
                        @else
                            <span class="text-xs text-slate-300">—</span>
                        @endif
                    </td>
                    <td class="px-3 py-3 text-right">
                        <a href="https://wa.me/62{{ ltrim($c['phone'], '0') }}?text={{ urlencode('Halo ' . $c['name'] . '! Ada promo spesial untuk Anda hari ini di Kee Laundry. Yuk cuci lagi! 🧺') }}"
                           target="_blank"
                           class="btn-secondary text-xs py-1 px-2.5 gap-1">
                            <svg class="size-3.5 text-emerald-500" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
                            WA
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Mobile Card List --}}
    <div class="md:hidden space-y-2.5">
        @foreach ($this->churnCustomers as $c)
        <div class="flex items-center gap-3 rounded-2xl border border-slate-200/80 bg-white p-3.5 shadow-sm">
            <div class="size-10 rounded-full bg-gradient-to-br from-sky-100 to-sky-200 flex items-center justify-center shrink-0">
                <span class="text-sm font-bold text-sky-700">{{ mb_strtoupper(mb_substr($c['name'], 0, 1)) }}</span>
            </div>
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-1.5">
                    <p class="font-semibold text-slate-800 text-sm truncate">{{ $c['name'] }}</p>
                    <span class="badge badge-slate text-[10px]">{{ $c['tier'] }}</span>
                </div>
                <p class="text-xs text-slate-400 mt-0.5">{{ $c['phone'] }}</p>
                <div class="flex items-center gap-2 mt-1.5">
                    @if ($c['days_ago'] !== null)
                        <span class="badge {{ $c['days_ago'] > 30 ? 'badge-rose' : 'badge-amber' }} text-[10px]">
                            {{ $c['days_ago'] }} hari lalu
                        </span>
                    @else
                        <span class="badge badge-rose text-[10px]">Baru daftar</span>
                    @endif
                    @if ($c['deposit_kg'] > 0)
                        <span class="text-[11px] font-semibold text-emerald-600">💰 {{ $c['deposit_kg'] }} Kg deposit</span>
                    @endif
                </div>
            </div>
            <a href="https://wa.me/62{{ ltrim($c['phone'], '0') }}?text={{ urlencode('Halo ' . $c['name'] . '! Ada promo spesial hari ini di Kee Laundry 🧺') }}"
               target="_blank"
               class="flex items-center justify-center size-10 rounded-xl bg-emerald-50 text-emerald-600 hover:bg-emerald-100 transition-colors shrink-0">
                <svg class="size-5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
            </a>
        </div>
        @endforeach
    </div>
</div>
@endif

</div>

{{-- ══════════════════════════════════════════════════════════════
     Alpine.js + Chart.js inline
══════════════════════════════════════════════════════════════════ --}}
<script>
function executiveDashboard(chartData) {
    return {
        chartMode: 'omzet',
        chart: null,
        data: chartData,

        initChart() {
            if (typeof Chart === 'undefined') {
                setTimeout(() => this.initChart(), 50);
                return;
            }

            const ctx = document.getElementById('omzetChart');
            if (!ctx) return;

            if (this.chart) {
                this.chart.destroy();
            }

            Chart.defaults.font.family = "'Plus Jakarta Sans', sans-serif";

            this.chart = new Chart(ctx, {
                type: 'bar',
                data: this.buildDataset(),
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#1e293b',
                            titleColor: '#94a3b8',
                            bodyColor: '#f1f5f9',
                            borderColor: '#334155',
                            borderWidth: 1,
                            cornerRadius: 10,
                            padding: 10,
                            callbacks: {
                                label: (ctx) => {
                                    if (this.chartMode === 'omzet') {
                                        return ' Rp ' + ctx.parsed.y.toLocaleString('id-ID');
                                    } else if (this.chartMode === 'kg') {
                                        return ' ' + ctx.parsed.y.toFixed(1) + ' Kg';
                                    } else {
                                        return ' ' + ctx.parsed.y + ' order';
                                    }
                                }
                            }
                        },
                    },
                    scales: {
                        x: {
                            grid: { display: false },
                            ticks: {
                                color: '#94a3b8',
                                font: { size: 11 },
                                maxRotation: 0,
                            },
                            border: { display: false },
                        },
                        y: {
                            grid: {
                                color: '#f1f5f9',
                                lineWidth: 1,
                            },
                            ticks: {
                                color: '#94a3b8',
                                font: { size: 11 },
                                maxTicksLimit: 5,
                                callback: (val) => {
                                    if (this.chartMode === 'omzet') {
                                        if (val >= 1000000) return 'Rp ' + (val / 1000000).toFixed(1) + 'jt';
                                        if (val >= 1000)    return 'Rp ' + (val / 1000).toFixed(0) + 'rb';
                                        return 'Rp ' + val;
                                    } else if (this.chartMode === 'kg') {
                                        return val + ' Kg';
                                    } else {
                                        return val + ' order';
                                    }
                                }
                            },
                            border: { display: false },
                        },
                    },
                    animation: { duration: 400, easing: 'easeInOutQuart' },
                    interaction: { mode: 'index', intersect: false },
                }
            });
        },

        buildDataset() {
            const colors = {
                omzet: { bg: 'rgba(2,132,199,0.15)', border: '#0284C7', hover: 'rgba(2,132,199,0.3)' },
                kg:    { bg: 'rgba(6,182,212,0.15)', border: '#06B6D4', hover: 'rgba(6,182,212,0.3)' },
                count: { bg: 'rgba(245,158,11,0.15)', border: '#F59E0B', hover: 'rgba(245,158,11,0.3)' },
            };
            const c = colors[this.chartMode];
            return {
                labels: this.data.labels,
                datasets: [{
                    data: this.data[this.chartMode],
                    backgroundColor: c.bg,
                    borderColor: c.border,
                    borderWidth: 2,
                    borderRadius: 8,
                    borderSkipped: false,
                    hoverBackgroundColor: c.hover,
                }]
            };
        },

        updateChart() {
            if (!this.chart) return;
            const ds = this.buildDataset();
            this.chart.data.labels   = ds.labels;
            this.chart.data.datasets = ds.datasets;
            // Update y-axis ticker
            this.chart.update();
        },
    };
}
</script>
