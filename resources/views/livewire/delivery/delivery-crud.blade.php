<div class="space-y-5" x-data="{ showForm: @entangle('showForm'), showDelete: @entangle('showDeleteConfirm') }">

    {{-- ══ TOP HEADER & ACTIONS ═══════════════════════════════════════════ --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h2 class="text-xl sm:text-2xl font-bold text-slate-800">Antar Jemput Laundry</h2>
            <p class="text-xs sm:text-sm text-slate-400 mt-0.5">Kelola penjemputan cucian kotor dan pengantaran cucian bersih</p>
        </div>
        <div class="flex items-center gap-2">
            <button wire:click="openCreate('JEMPUT')" class="btn-secondary text-xs sm:text-sm">
                <svg class="size-4 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                <span>Jadwal Jemput</span>
            </button>
            <button wire:click="openCreate('ANTAR')" class="btn-primary text-xs sm:text-sm">
                <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                <span>Jadwal Antar</span>
            </button>
        </div>
    </div>

    {{-- ══ METRIC STATS CARDS ═════════════════════════════════════════════ --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        {{-- Jemput Menunggu --}}
        <div class="card p-4">
            <div class="flex items-center justify-between">
                <span class="text-xs font-medium text-slate-500">Jemput Menunggu</span>
                <span class="size-8 rounded-lg bg-slate-100 text-slate-600 flex items-center justify-center text-sm">🛵</span>
            </div>
            <p class="text-2xl font-bold text-slate-800 mt-2">{{ $this->counts['jemput_pending'] }}</p>
            <p class="text-[11px] text-slate-400 mt-0.5">Siap diambil kurir</p>
        </div>

        {{-- Antar Menunggu --}}
        <div class="card p-4">
            <div class="flex items-center justify-between">
                <span class="text-xs font-medium text-slate-500">Antar Menunggu</span>
                <span class="size-8 rounded-lg bg-slate-100 text-slate-600 flex items-center justify-center text-sm">📦</span>
            </div>
            <p class="text-2xl font-bold text-slate-800 mt-2">{{ $this->counts['antar_pending'] }}</p>
            <p class="text-[11px] text-slate-400 mt-0.5">Cucian bersih siap antar</p>
        </div>

        {{-- Jadwal Hari Ini --}}
        <div class="card p-4">
            <div class="flex items-center justify-between">
                <span class="text-xs font-medium text-slate-500">Jadwal Hari Ini</span>
                <span class="size-8 rounded-lg bg-slate-100 text-slate-600 flex items-center justify-center text-sm">📅</span>
            </div>
            <p class="text-2xl font-bold text-slate-800 mt-2">{{ $this->counts['today_total'] }}</p>
            <p class="text-[11px] text-slate-400 mt-0.5">Total tugas hari ini</p>
        </div>

        {{-- Selesai --}}
        <div class="card p-4">
            <div class="flex items-center justify-between">
                <span class="text-xs font-medium text-slate-500">Telah Selesai</span>
                <span class="size-8 rounded-lg bg-slate-100 text-slate-600 flex items-center justify-center text-sm">✓</span>
            </div>
            <p class="text-2xl font-bold text-slate-800 mt-2">{{ $this->counts['completed'] }}</p>
            <p class="text-[11px] text-slate-400 mt-0.5">Riwayat antar/jemput selesai</p>
        </div>
    </div>

    {{-- ══ TABS & FILTERS ═════════════════════════════════════════════════ --}}
    <div class="space-y-3">
        {{-- Navigation Tabs: Minimal Segmented Control --}}
        <div class="flex items-center gap-1 bg-slate-100 p-1 rounded-xl w-fit max-w-full overflow-x-auto scrollbar-none">
            <button wire:click="setTab('jemput')"
                    @class([
                        'px-3.5 py-1.5 rounded-lg text-xs sm:text-sm font-medium whitespace-nowrap transition-all flex items-center gap-2',
                        'bg-white text-slate-800 shadow-sm font-semibold' => $activeTab === 'jemput',
                        'text-slate-600 hover:text-slate-900' => $activeTab !== 'jemput',
                    ])>
                <span>Jemput Kotor</span>
                @if ($this->counts['jemput_pending'] > 0)
                    <span class="px-1.5 py-0.2 rounded-full text-[11px] font-bold bg-slate-200/80 text-slate-700">
                        {{ $this->counts['jemput_pending'] }}
                    </span>
                @endif
            </button>

            <button wire:click="setTab('antar')"
                    @class([
                        'px-3.5 py-1.5 rounded-lg text-xs sm:text-sm font-medium whitespace-nowrap transition-all flex items-center gap-2',
                        'bg-white text-slate-800 shadow-sm font-semibold' => $activeTab === 'antar',
                        'text-slate-600 hover:text-slate-900' => $activeTab !== 'antar',
                    ])>
                <span>Antar Bersih</span>
                @if ($this->counts['antar_pending'] > 0)
                    <span class="px-1.5 py-0.2 rounded-full text-[11px] font-bold bg-slate-200/80 text-slate-700">
                        {{ $this->counts['antar_pending'] }}
                    </span>
                @endif
            </button>

            <button wire:click="setTab('semua')"
                    @class([
                        'px-3.5 py-1.5 rounded-lg text-xs sm:text-sm font-medium whitespace-nowrap transition-all',
                        'bg-white text-slate-800 shadow-sm font-semibold' => $activeTab === 'semua',
                        'text-slate-600 hover:text-slate-900' => $activeTab !== 'semua',
                    ])>
                Semua Tugas
            </button>

            <button wire:click="setTab('riwayat')"
                    @class([
                        'px-3.5 py-1.5 rounded-lg text-xs sm:text-sm font-medium whitespace-nowrap transition-all',
                        'bg-white text-slate-800 shadow-sm font-semibold' => $activeTab === 'riwayat',
                        'text-slate-600 hover:text-slate-900' => $activeTab !== 'riwayat',
                    ])>
                Riwayat Selesai
            </button>
        </div>

        {{-- Filters row --}}
        <div class="card p-3 sm:p-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-2.5">
                {{-- Search box --}}
                <div class="relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 size-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/></svg>
                    <input type="search"
                           wire:model.live.debounce.300ms="search"
                           placeholder="Cari pelanggan, telp, alamat..."
                           class="form-input pl-9 text-xs sm:text-sm">
                </div>

                {{-- Date Filter --}}
                <select wire:model.live="filterDate" class="form-input text-xs sm:text-sm">
                    <option value="all">Semua Tanggal</option>
                    <option value="today">Hari Ini ({{ today()->format('d M') }})</option>
                    <option value="tomorrow">Besok ({{ today()->addDay()->format('d M') }})</option>
                </select>

                {{-- Courier Filter --}}
                <select wire:model.live="filterCourier" class="form-input text-xs sm:text-sm">
                    <option value="">Semua Kurir</option>
                    @foreach ($this->couriers as $courier)
                        <option value="{{ $courier->id }}">{{ $courier->name }}</option>
                    @endforeach
                </select>

                {{-- Status Filter --}}
                <select wire:model.live="filterStatus" class="form-input text-xs sm:text-sm">
                    <option value="">Semua Status</option>
                    <option value="MENUNGGU">Menunggu</option>
                    <option value="DIPROSES">Diproses / Jalan</option>
                    <option value="SELESAI">Selesai</option>
                    <option value="BATAL">Batal</option>
                </select>
            </div>
        </div>
    </div>

    {{-- ══ TASK CARDS LIST ════════════════════════════════════════════════ --}}
    <div class="space-y-3">
        @forelse ($tasks as $task)
            <div class="card p-4 sm:p-5 hover:border-slate-300 transition-colors">
                <div class="flex flex-col md:flex-row md:items-start justify-between gap-4">
                    {{-- Left side info --}}
                    <div class="space-y-2.5 flex-1 min-w-0">
                        {{-- Badges row --}}
                        <div class="flex items-center gap-2 flex-wrap">
                            @if ($task->task_type === 'JEMPUT')
                                <span class="badge badge-slate text-xs font-semibold">
                                    🛵 Jemput Kotor
                                </span>
                            @else
                                <span class="badge badge-sky text-xs font-semibold">
                                    📦 Antar Bersih
                                </span>
                            @endif

                            {{-- Status Badge --}}
                            @if ($task->status === 'MENUNGGU')
                                <span class="badge badge-amber text-xs">
                                    Menunggu
                                </span>
                            @elseif ($task->status === 'DIPROSES')
                                <span class="badge badge-sky text-xs">
                                    <span class="size-1.5 rounded-full bg-sky-500 animate-pulse inline-block"></span>
                                    Sedang Jalan
                                </span>
                            @elseif ($task->status === 'SELESAI')
                                <span class="badge badge-green text-xs">
                                    ✓ Selesai
                                </span>
                            @else
                                <span class="badge badge-slate text-xs">
                                    Dibatalkan
                                </span>
                            @endif

                            {{-- Time Slot & Date --}}
                            <span class="text-xs text-slate-400 flex items-center gap-1">
                                <svg class="size-3.5 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                                {{ $task->scheduled_date->format('d M Y') }} • {{ $task->time_slot }}
                            </span>

                            @if ($task->order)
                                <a href="/pos" class="badge badge-slate text-xs font-mono hover:text-sky-600 transition-colors">
                                    Nota #{{ $task->order->invoice_number }}
                                </a>
                            @endif
                        </div>

                        {{-- Customer Name & Phone --}}
                        <div class="flex items-center gap-2">
                            <h4 class="text-base font-semibold text-slate-900">{{ $task->customer_name }}</h4>
                            <span class="text-xs text-slate-400">({{ $task->customer_phone }})</span>
                        </div>

                        {{-- Address --}}
                        <div class="flex items-start gap-1.5 text-xs sm:text-sm text-slate-600">
                            <svg class="size-4 text-slate-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z"/></svg>
                            <span class="leading-relaxed">{{ $task->address }}</span>
                        </div>

                        {{-- Notes or Extra --}}
                        @if ($task->notes || $task->estimated_kg || $task->delivery_fee > 0)
                            <div class="flex flex-wrap items-center gap-2 text-xs text-slate-500 pt-0.5">
                                @if ($task->estimated_kg)
                                    <span class="bg-slate-100 text-slate-600 px-2 py-0.5 rounded font-medium">Est. {{ $task->estimated_kg }} Kg</span>
                                @endif
                                @if ($task->delivery_fee > 0)
                                    <span class="bg-slate-100 text-slate-700 px-2 py-0.5 rounded font-medium">Ongkir: Rp {{ number_format($task->delivery_fee) }}</span>
                                @endif
                                @if ($task->notes)
                                    <span class="text-slate-500">"{{ $task->notes }}"</span>
                                @endif
                            </div>
                        @endif
                    </div>

                    {{-- Right side: Courier Allocation & Quick Action Buttons --}}
                    <div class="flex flex-col sm:flex-row md:flex-col items-stretch sm:items-center md:items-end gap-2 shrink-0 pt-3 md:pt-0 border-t md:border-t-0 border-slate-100">
                        {{-- Courier Assign selector --}}
                        <div class="flex items-center gap-1.5 w-full sm:w-auto">
                            <span class="text-xs text-slate-400 whitespace-nowrap">Kurir:</span>
                            <select wire:change="assignCourier({{ $task->id }}, $event.target.value ?: null)"
                                    class="form-input py-1 text-xs w-full sm:w-40 bg-slate-50/50">
                                <option value="">-- Belum Ditugaskan --</option>
                                @foreach ($this->couriers as $courier)
                                    <option value="{{ $courier->id }}" @selected($task->courier_id === $courier->id)>
                                        {{ $courier->name }}
                                    </option>
                                @endforeach
                                @if ($this->couriers->isEmpty())
                                    @foreach ($this->allStaff as $staff)
                                        <option value="{{ $staff->id }}" @selected($task->courier_id === $staff->id)>
                                            {{ $staff->name }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>

                        {{-- Navigation, WhatsApp, Status & Edit Actions --}}
                        <div class="flex items-center gap-1.5 w-full sm:w-auto justify-end">
                            {{-- Google Maps button --}}
                            <a href="{{ $this->getMapsLink($task) }}"
                               target="_blank"
                               title="Buka rute Google Maps"
                               class="btn-secondary text-xs py-1.5 px-2.5">
                                <svg class="size-3.5 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z"/></svg>
                                <span>Peta</span>
                            </a>

                            {{-- WhatsApp button --}}
                            <a href="{{ $this->getWaLink($task) }}"
                               target="_blank"
                               title="Kirim pesan WhatsApp"
                               class="btn-secondary text-xs py-1.5 px-2.5 hover:text-emerald-700">
                                <svg class="size-3.5 text-emerald-600" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
                                <span>WA</span>
                            </a>

                            {{-- Pipeline Action Button --}}
                            @if ($task->status === 'MENUNGGU')
                                <button wire:click="updateStatus({{ $task->id }}, 'DIPROSES')"
                                        class="btn-primary text-xs py-1.5 px-3">
                                    <span>Mulai Jalan</span>
                                </button>
                            @elseif ($task->status === 'DIPROSES')
                                <button wire:click="updateStatus({{ $task->id }}, 'SELESAI')"
                                        class="btn-primary text-xs py-1.5 px-3">
                                    <span>Selesai</span>
                                </button>
                            @endif

                            {{-- Edit button --}}
                            <button wire:click="openEdit({{ $task->id }})"
                                    title="Edit tugas"
                                    class="p-1.5 rounded-lg border border-slate-200 text-slate-500 hover:text-slate-800 hover:bg-slate-50 transition-colors">
                                <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125"/></svg>
                            </button>

                            {{-- Cancel button --}}
                            @if ($task->status !== 'BATAL' && $task->status !== 'SELESAI')
                                <button wire:click="updateStatus({{ $task->id }}, 'BATAL')"
                                        title="Batalkan tugas"
                                        class="p-1.5 rounded-lg border border-slate-200 text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition-colors">
                                    <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
                                </button>
                            @endif

                            {{-- Delete button --}}
                            <button wire:click="confirmDelete({{ $task->id }})"
                                    title="Hapus data"
                                    class="p-1.5 rounded-lg border border-slate-200 text-slate-300 hover:text-rose-600 hover:bg-rose-50 transition-colors">
                                <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/></svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="card p-12 text-center text-slate-400">
                <p class="text-4xl mb-2">🛵</p>
                <p class="font-bold text-slate-700">Tidak ada tugas antar-jemput</p>
                <p class="text-xs text-slate-400 mt-1">Gunakan tombol "Jadwal Jemput" atau "Jadwal Antar" untuk menambahkan jadwal baru.</p>
            </div>
        @endforelse

        {{-- Pagination --}}
        @if ($tasks->hasPages())
            <div class="pt-2">
                {{ $tasks->links() }}
            </div>
        @endif
    </div>

    {{-- ══ MODAL FORM: CREATE / EDIT ══════════════════════════════════════ --}}
    <div x-show="showForm" x-cloak style="display:none">
        <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-50" @click="showForm = false; $wire.closeForm()"></div>

        {{-- Mobile Bottom Sheet --}}
        <div x-show="showForm"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="translate-y-full"
             x-transition:enter-end="translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="translate-y-0"
             x-transition:leave-end="translate-y-full"
             class="md:hidden bottom-sheet max-h-[85vh] overflow-y-auto">
            <div class="bottom-sheet-handle"></div>
            @include('livewire.delivery._task-form')
        </div>

        {{-- Desktop Centered Modal --}}
        <div x-show="showForm"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             class="hidden md:flex fixed inset-0 z-50 items-center justify-center p-4 pointer-events-none">
            <div class="pointer-events-auto w-full max-w-lg bg-white rounded-2xl shadow-2xl overflow-hidden max-h-[90vh] overflow-y-auto">
                @include('livewire.delivery._task-form')
            </div>
        </div>
    </div>

    {{-- ══ MODAL: DELETE CONFIRMATION ═════════════════════════════════════ --}}
    <div x-show="showDelete" x-cloak style="display:none">
        <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-50" @click="showDelete = false"></div>
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 pointer-events-none">
            <div class="pointer-events-auto w-full max-w-sm bg-white rounded-2xl shadow-2xl p-6 text-center space-y-4">
                <div class="size-12 rounded-full bg-rose-50 text-rose-500 mx-auto flex items-center justify-center text-xl">
                    🗑️
                </div>
                <div>
                    <h3 class="font-bold text-slate-800 text-base">Hapus Tugas Ini?</h3>
                    <p class="text-xs text-slate-500 mt-1">Data tugas antar jemput akan dihapus dari sistem.</p>
                </div>
                <div class="flex gap-2">
                    <button wire:click="deleteConfirmed" class="btn-danger flex-1 justify-center py-2 text-xs sm:text-sm">
                        Ya, Hapus
                    </button>
                    <button @click="showDelete = false" class="btn-secondary flex-1 justify-center py-2 text-xs sm:text-sm">
                        Batal
                    </button>
                </div>
            </div>
        </div>
    </div>

</div>
