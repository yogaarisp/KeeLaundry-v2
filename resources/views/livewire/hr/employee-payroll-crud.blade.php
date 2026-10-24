<div x-data="{
    showForm:       @entangle('showForm'),
    showAttendance: @entangle('showAttendance'),
    showPayslip:    @entangle('showPayslip'),
    showClose:      @entangle('showCloseConfirm'),
    showDelete:     @entangle('showDeleteConfirm'),
}" class="space-y-4">

{{-- ══ HEADER ══════════════════════════════════════════════════════ --}}
<div class="flex items-center justify-between gap-3">
    <div>
        <h2 class="text-lg font-bold text-slate-800">HR &amp; Payroll</h2>
        <p class="text-xs text-slate-400 mt-0.5">Kelola karyawan, absensi, kasbon, &amp; slip gaji</p>
    </div>
    <div class="flex items-center gap-2 shrink-0">
        <button wire:click="$set('showCloseConfirm', true)"
                class="btn-secondary text-xs sm:text-sm py-2 px-3 gap-1.5 text-amber-800 bg-amber-50 border-amber-200 hover:bg-amber-100 font-semibold"
                title="Tutup Periode Payroll">
            <svg class="size-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z"/></svg>
            <span class="hidden sm:inline">Tutup Periode</span>
        </button>
        <button wire:click="openCreate" class="btn-primary text-xs sm:text-sm py-2 px-3.5 gap-1.5 font-bold shadow-xs">
            <svg class="size-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
            <span class="hidden sm:inline">Tambah Karyawan</span>
            <span class="sm:hidden">Karyawan</span>
        </button>
    </div>
</div>

{{-- ══ TABS (2-Column Segmented Control on Mobile) ═════════════════ --}}
<div class="grid grid-cols-2 bg-slate-100 p-1 rounded-2xl sm:w-fit sm:flex sm:gap-1">
    <button wire:click="$set('activeTab','list')"
            @class([
                'py-2 px-3 sm:px-4 rounded-xl text-xs font-bold transition-all text-center flex items-center justify-center gap-1.5',
                'bg-white text-slate-900 shadow-xs' => $activeTab === 'list',
                'text-slate-500 hover:text-slate-800' => $activeTab !== 'list',
            ])>
        <span>👤</span>
        <span>Daftar Karyawan</span>
    </button>
    <button wire:click="$set('activeTab','payroll')"
            @class([
                'py-2 px-3 sm:px-4 rounded-xl text-xs font-bold transition-all text-center flex items-center justify-center gap-1.5',
                'bg-white text-slate-900 shadow-xs' => $activeTab === 'payroll',
                'text-slate-500 hover:text-slate-800' => $activeTab !== 'payroll',
            ])>
        <span>💰</span>
        <span>Kalkulasi Payroll</span>
    </button>
</div>

{{-- ══ TAB: DAFTAR KARYAWAN ═══════════════════════════════════════ --}}
@if ($activeTab === 'list')
<div class="space-y-3">
    {{-- Desktop table (Centered & Well-Spaced) --}}
    <div class="card hidden md:block overflow-hidden shadow-xs">
        <table class="w-full text-sm">
            <thead class="bg-slate-50/80 border-b border-slate-200/80">
                <tr>
                    <th class="text-left px-5 py-3.5 text-xs font-semibold text-slate-400 uppercase tracking-wide">Karyawan</th>
                    <th class="text-center px-4 py-3.5 text-xs font-semibold text-slate-400 uppercase tracking-wide">Role</th>
                    <th class="text-right px-4 py-3.5 text-xs font-semibold text-slate-400 uppercase tracking-wide">Gaji Pokok</th>
                    <th class="text-center px-4 py-3.5 text-xs font-semibold text-slate-400 uppercase tracking-wide">Output Produksi</th>
                    <th class="text-right px-4 py-3.5 text-xs font-semibold text-slate-400 uppercase tracking-wide">Kasbon</th>
                    <th class="text-center px-4 py-3.5 text-xs font-semibold text-slate-400 uppercase tracking-wide">Kehadiran</th>
                    <th class="px-5 py-3.5 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($this->employees as $e)
                @php $roleClass = match($e->role){ 'KASIR' => 'badge-sky', 'SETRIKA' => 'badge-cyan', 'KURIR' => 'badge-amber', default => 'badge-slate' }; @endphp
                <tr @class(['hover:bg-slate-50/70 transition-colors', 'opacity-50' => !$e->is_active || $e->trashed()])>
                    <td class="px-5 py-3.5">
                        <div class="flex items-center gap-3">
                            <div class="size-10 rounded-full bg-gradient-to-br from-sky-100 to-indigo-100 border border-sky-200/60 flex items-center justify-center shrink-0 shadow-2xs">
                                <span class="text-sm font-bold text-sky-700">{{ mb_strtoupper(mb_substr($e->name, 0, 1)) }}</span>
                            </div>
                            <div>
                                <p class="font-bold text-slate-800">{{ $e->name }}</p>
                                <p class="text-xs text-slate-400 mt-0.5">{{ $e->phone }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-3.5 text-center"><span class="badge {{ $roleClass }} font-semibold">{{ $e->role }}</span></td>
                    <td class="px-4 py-3.5 text-right tabular-nums font-semibold text-slate-700">Rp {{ number_format($e->base_salary) }}</td>
                    <td class="px-4 py-3.5 text-center">
                        <span @class(['font-bold tabular-nums', 'text-sky-600' => $e->total_output_kg > 0, 'text-slate-400' => $e->total_output_kg == 0])>
                            {{ number_format($e->total_output_kg, 1) }} Kg
                        </span>
                        @if ($e->commission_rate > 0)
                            <p class="text-[10px] text-emerald-600 font-semibold">+Rp {{ number_format($e->total_commission) }}</p>
                        @endif
                    </td>
                    <td class="px-4 py-3.5 text-right">
                        @if ($e->cash_advance > 0)
                            <span class="text-rose-600 font-bold tabular-nums">Rp {{ number_format($e->cash_advance) }}</span>
                        @else
                            <span class="text-slate-300">—</span>
                        @endif
                    </td>
                    <td class="px-4 py-3.5 text-center">
                        <span class="font-bold text-slate-700">{{ $e->attendance_days }} hari</span>
                    </td>
                    <td class="px-5 py-3.5">
                        <div class="flex items-center justify-end gap-1.5">
                            @if ($e->trashed())
                                <button wire:click="restoreEmployee({{ $e->id }})" class="btn-secondary text-xs py-1 px-2.5">Pulihkan</button>
                            @else
                                <button wire:click="openPayslip({{ $e->id }})" class="btn-icon text-sky-600 hover:bg-sky-50" title="Slip Gaji">
                                    <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"/></svg>
                                </button>
                                <button wire:click="openAttendance({{ $e->id }})" class="btn-icon text-amber-600 hover:bg-amber-50" title="Absensi & Kasbon">
                                    <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5"/></svg>
                                </button>
                                <button wire:click="openEdit({{ $e->id }})" class="btn-icon" title="Edit">
                                    <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Z"/></svg>
                                </button>
                                <button wire:click="confirmDelete({{ $e->id }})" class="btn-icon text-slate-400 hover:text-rose-600 hover:bg-rose-50" title="Nonaktifkan">
                                    <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M22 10.5h-6m-2.25-4.125a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0ZM4 19.235v-.11a6.375 6.375 0 0 1 12.75 0v.109A12.318 12.318 0 0 1 10.374 21c-2.331 0-4.512-.645-6.374-1.766Z"/></svg>
                                </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="px-5 py-12 text-center text-slate-400"><p class="text-2xl mb-2">👤</p><p>Belum ada karyawan</p></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Mobile cards --}}
    <div class="md:hidden space-y-3">
        @forelse ($this->employees as $e)
        @php
            $rc = match($e->role){ 'KASIR' => 'badge-sky', 'SETRIKA' => 'badge-cyan', 'KURIR' => 'badge-amber', default => 'badge-slate' };
            $cleanPhone = preg_replace('/^0/', '62', preg_replace('/[^\d]/', '', $e->phone));
        @endphp
        <div @class(['card p-3.5 space-y-3', 'opacity-60' => !$e->is_active || $e->trashed()])>
            <div class="flex items-center justify-between gap-2">
                <div class="flex items-center gap-2.5 min-w-0 flex-1">
                    <div class="size-10 rounded-full bg-gradient-to-br from-sky-100 to-indigo-100 flex items-center justify-center font-bold text-sky-700 text-sm shrink-0">
                        {{ mb_strtoupper(mb_substr($e->name, 0, 1)) }}
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center gap-1.5 flex-wrap">
                            <p class="font-bold text-slate-800 text-sm leading-tight truncate">{{ $e->name }}</p>
                            <span class="badge {{ $rc }} text-[10px] font-semibold">{{ $e->role }}</span>
                        </div>
                        <p class="text-xs text-slate-400 mt-0.5">{{ $e->phone }}</p>
                    </div>
                </div>
                @if ($e->phone)
                <a href="https://wa.me/{{ $cleanPhone }}" target="_blank" class="size-8 rounded-lg bg-emerald-50 text-emerald-600 hover:bg-emerald-100 flex items-center justify-center shrink-0 transition-colors" title="WhatsApp">
                    <svg class="size-4" viewBox="0 0 24 24" fill="currentColor"><path d="M12.04 2c-5.46 0-9.91 4.45-9.91 9.91 0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38c1.45.79 3.08 1.21 4.74 1.21 5.46 0 9.91-4.45 9.91-9.91 0-2.65-1.03-5.14-2.9-7.01A9.816 9.816 0 0 0 12.04 2m.01 1.67c4.54 0 8.24 3.7 8.24 8.24 0 2.2-.86 4.28-2.42 5.84a8.18 8.18 0 0 1-5.82 2.41h-.01c-1.42 0-2.82-.37-4.06-1.08l-.29-.17-3.02.79.81-2.94-.19-.3a8.214 8.214 0 0 1-1.26-4.38c0-4.54 3.7-8.24 8.24-8.24M8.53 7.33c-.16 0-.35.06-.53.25-.19.19-.71.7-.71 1.71 0 1.01.73 1.99.83 2.13.1.13 1.44 2.2 3.5 3.09.49.21.87.34 1.17.43.49.16.94.14 1.29.08.4-.06 1.21-.5 1.38-.98.17-.48.17-.9.12-.98-.05-.09-.19-.14-.4-.25s-1.23-.61-1.42-.68c-.19-.07-.33-.1-.47.1-.14.21-.54.68-.66.82-.12.14-.24.16-.45.05-.21-.1-.89-.33-1.69-1.05-.62-.56-1.04-1.25-1.16-1.46-.12-.21-.01-.32.09-.42.09-.09.21-.24.31-.36.1-.12.14-.21.21-.35.07-.14.03-.26-.02-.36-.05-.1-.47-1.14-.65-1.56-.17-.42-.35-.36-.48-.37-.12-.01-.26-.01-.4-.01z"/></svg>
                </a>
                @endif
            </div>

            <div class="grid grid-cols-3 gap-2 text-center text-xs">
                <div class="bg-slate-50 border border-slate-100 rounded-xl p-2">
                    <p class="text-slate-400 text-[11px]">Gaji Pokok</p>
                    <p class="font-bold text-slate-700 mt-0.5">Rp {{ number_format($e->base_salary / 1000) }}rb</p>
                </div>
                <div class="bg-sky-50 border border-sky-100 rounded-xl p-2">
                    <p class="text-sky-600 text-[11px]">Output</p>
                    <p class="font-bold text-sky-700 mt-0.5">{{ $e->total_output_kg }} Kg</p>
                </div>
                <div @class(['rounded-xl p-2 border', 'bg-rose-50 border-rose-100' => $e->cash_advance > 0, 'bg-slate-50 border-slate-100' => $e->cash_advance == 0])>
                    <p class="text-slate-400 text-[11px]">Kasbon</p>
                    <p @class(['font-bold mt-0.5', 'text-rose-600' => $e->cash_advance > 0, 'text-slate-300' => $e->cash_advance == 0])>
                        {{ $e->cash_advance > 0 ? 'Rp ' . number_format($e->cash_advance / 1000) . 'rb' : '—' }}
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-1.5 pt-1 border-t border-slate-100">
                @if ($e->trashed())
                    <button wire:click="restoreEmployee({{ $e->id }})" class="btn-secondary text-xs py-2 flex-1 justify-center">Pulihkan</button>
                @else
                    <button wire:click="openPayslip({{ $e->id }})" class="btn-secondary text-xs py-2 px-2.5 flex-1 justify-center text-sky-700 font-semibold">
                        📄 Slip Gaji
                    </button>
                    <button wire:click="openAttendance({{ $e->id }})" class="btn-secondary text-xs py-2 px-2.5 flex-1 justify-center text-amber-700 font-semibold">
                        📅 Absensi
                    </button>
                    <button wire:click="openEdit({{ $e->id }})" class="size-9 shrink-0 rounded-xl bg-slate-50 border border-slate-200 text-slate-600 hover:text-slate-900 flex items-center justify-center transition-colors" title="Edit">
                        <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Z"/></svg>
                    </button>
                    <button wire:click="confirmDelete({{ $e->id }})" class="size-9 shrink-0 rounded-xl bg-slate-50 border border-slate-200 text-slate-400 hover:text-rose-600 hover:bg-rose-50 flex items-center justify-center transition-colors" title="Nonaktifkan">
                        <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M22 10.5h-6m-2.25-4.125a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0ZM4 19.235v-.11a6.375 6.375 0 0 1 12.75 0v.109A12.318 12.318 0 0 1 10.374 21c-2.331 0-4.512-.645-6.374-1.766Z"/></svg>
                    </button>
                @endif
            </div>
        </div>
        @empty
        <div class="card p-12 text-center text-slate-400"><p class="text-3xl mb-2">👤</p><p>Belum ada karyawan</p></div>
        @endforelse
    </div>
</div>
@endif

{{-- ══ TAB: PAYROLL KALKULASI ════════════════════════════════════════ --}}
@if ($activeTab === 'payroll')
<div class="space-y-4">
    {{-- Summary KPI (Balanced 4-card Grid) --}}
    @php $summary = $this->payrollSummary; @endphp
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
        <div class="kpi-card">
            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide">Total Staff Aktif</p>
            <p class="text-2xl font-bold text-slate-800 mt-1">{{ $summary['employee_count'] }} <span class="text-xs font-normal text-slate-400">orang</span></p>
        </div>
        <div class="kpi-card">
            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide">Total Gaji Pokok</p>
            <p class="text-xl font-bold text-slate-800 mt-1 tabular-nums">Rp {{ number_format($summary['total_base']) }}</p>
        </div>
        <div class="kpi-card">
            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide">Total Komisi</p>
            <p class="text-xl font-bold text-sky-600 mt-1 tabular-nums">Rp {{ number_format($summary['total_commission']) }}</p>
        </div>
        <div class="kpi-card border-l-4 border-l-sky-500">
            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide">Total Beban Gaji</p>
            <p class="text-xl font-bold text-sky-700 mt-1 tabular-nums">Rp {{ number_format($summary['total_gross']) }}</p>
        </div>
    </div>

    {{-- Payslip table (Desktop View) --}}
    <div class="card overflow-hidden shadow-xs">
        <table class="w-full text-sm hidden md:table">
            <thead class="bg-slate-50/80 border-b border-slate-200/80">
                <tr>
                    <th class="text-left px-5 py-3.5 text-xs font-semibold text-slate-400 uppercase tracking-wide">Karyawan</th>
                    <th class="text-right px-4 py-3.5 text-xs font-semibold text-slate-400 uppercase tracking-wide">Gaji Pokok</th>
                    <th class="text-right px-4 py-3.5 text-xs font-semibold text-slate-400 uppercase tracking-wide">Uang Makan</th>
                    <th class="text-right px-4 py-3.5 text-xs font-semibold text-slate-400 uppercase tracking-wide">Komisi Kg</th>
                    <th class="text-right px-4 py-3.5 text-xs font-semibold text-slate-400 uppercase tracking-wide">Kasbon</th>
                    <th class="text-right px-4 py-3.5 text-xs font-semibold text-slate-400 uppercase tracking-wide">Take-Home Pay</th>
                    <th class="px-5 py-3.5 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach ($this->allPayslips as $slip)
                @php $emp = $slip['employee']; @endphp
                <tr class="hover:bg-slate-50/70 transition-colors">
                    <td class="px-5 py-3.5">
                        <div class="flex items-center gap-3">
                            <div class="size-9 rounded-full bg-sky-50 border border-sky-100 flex items-center justify-center shrink-0 shadow-2xs">
                                <span class="text-xs font-bold text-sky-700">{{ mb_strtoupper(mb_substr($emp->name,0,1)) }}</span>
                            </div>
                            <div>
                                <p class="font-bold text-slate-800">{{ $emp->name }}</p>
                                <p class="text-xs text-slate-400 mt-0.5">{{ $emp->role }} · {{ number_format($slip['total_output_kg'], 1) }} Kg</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-3.5 text-right tabular-nums text-slate-700 font-medium">Rp {{ number_format($slip['base_salary']) }}</td>
                    <td class="px-4 py-3.5 text-right tabular-nums text-slate-600 font-medium">Rp {{ number_format($slip['total_allowance']) }}</td>
                    <td class="px-4 py-3.5 text-right tabular-nums text-sky-600 font-bold">Rp {{ number_format($slip['total_commission']) }}</td>
                    <td class="px-4 py-3.5 text-right tabular-nums">
                        @if ($slip['cash_advance'] > 0)
                            <span class="text-rose-600 font-bold">–Rp {{ number_format($slip['cash_advance']) }}</span>
                        @else <span class="text-slate-300">—</span> @endif
                    </td>
                    <td class="px-4 py-3.5 text-right">
                        <span class="text-base font-black text-slate-800 tabular-nums">Rp {{ number_format($slip['take_home_pay']) }}</span>
                    </td>
                    <td class="px-5 py-3.5 text-right">
                        <button wire:click="openPayslip({{ $emp->id }})" class="btn-secondary text-xs py-1.5 px-3.5 text-sky-700 font-bold hover:bg-sky-50 hover:border-sky-200">
                            Slip Gaji
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot class="bg-sky-50/70 border-t-2 border-sky-200">
                <tr>
                    <td class="px-5 py-3.5 font-bold text-slate-700" colspan="5">Total Beban Gaji Bulan Ini</td>
                    <td class="px-4 py-3.5 text-right font-black text-sky-800 text-base tabular-nums">Rp {{ number_format($summary['total_gross']) }}</td>
                    <td class="px-5 py-3.5"></td>
                </tr>
            </tfoot>
        </table>

        {{-- Mobile payroll cards --}}
        <div class="md:hidden divide-y divide-slate-100">
            @foreach ($this->allPayslips as $slip)
            @php $emp = $slip['employee']; @endphp
            <div class="p-3.5 space-y-2.5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="font-bold text-slate-800 text-sm leading-tight">{{ $emp->name }}</p>
                        <p class="text-xs text-slate-400 mt-0.5">{{ $emp->role }} · {{ number_format($slip['total_output_kg'], 1) }} Kg</p>
                    </div>
                    <div class="text-right">
                        <p class="font-black text-sky-700 tabular-nums text-base">Rp {{ number_format($slip['take_home_pay']) }}</p>
                        <span class="text-[10px] text-slate-400">Take-Home</span>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-2 text-xs">
                    <div class="bg-slate-50 border border-slate-100 rounded-xl p-2"><span class="text-slate-400 block text-[11px]">Gaji Pokok:</span><strong class="text-slate-700">Rp {{ number_format($slip['base_salary']) }}</strong></div>
                    <div class="bg-sky-50 border border-sky-100 rounded-xl p-2"><span class="text-slate-400 block text-[11px]">Komisi:</span><strong class="text-sky-700 font-bold">Rp {{ number_format($slip['total_commission']) }}</strong></div>
                    <div class="bg-slate-50 border border-slate-100 rounded-xl p-2"><span class="text-slate-400 block text-[11px]">Uang Makan:</span><strong class="text-slate-700">Rp {{ number_format($slip['total_allowance']) }}</strong></div>
                    @if ($slip['cash_advance'] > 0)
                    <div class="bg-rose-50 border border-rose-100 rounded-xl p-2"><span class="text-rose-500 block text-[11px]">Kasbon:</span><strong class="text-rose-600 font-bold">–Rp {{ number_format($slip['cash_advance']) }}</strong></div>
                    @endif
                </div>
                <button wire:click="openPayslip({{ $emp->id }})" class="btn-secondary text-xs py-2 w-full justify-center text-sky-700 font-bold">
                    Lihat Slip Gaji
                </button>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endif

{{-- ══ MODALS (True Centered Dialog on Desktop, Bottom Sheet on Mobile) ═════ --}}

{{-- 1. Employee Form Modal --}}
<div x-show="showForm" x-cloak style="display:none">
    <div class="bottom-sheet-overlay md:hidden" @click="showForm = false"></div>
    <div class="dialog-overlay hidden md:flex" @click.self="showForm = false"></div>

    {{-- Mobile Bottom Sheet --}}
    <div x-show="showForm"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="translate-y-full"
         x-transition:enter-end="translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="translate-y-0"
         x-transition:leave-end="translate-y-full"
         class="bottom-sheet md:hidden overscroll-contain">
        <div class="bottom-sheet-handle"></div>
        @include('livewire.hr._employee-form')
    </div>

    {{-- Desktop Centered Dialog --}}
    <div x-show="showForm"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="hidden md:flex fixed inset-0 z-50 items-center justify-center p-4 pointer-events-none">
        <div class="pointer-events-auto w-full max-w-lg bg-white rounded-2xl shadow-2xl overflow-hidden max-h-[90vh] overflow-y-auto">
            @include('livewire.hr._employee-form')
        </div>
    </div>
</div>

{{-- 2. Attendance & Kasbon Modal --}}
<div x-show="showAttendance" x-cloak style="display:none">
    <div class="bottom-sheet-overlay md:hidden" @click="showAttendance = false"></div>
    <div class="dialog-overlay hidden md:flex" @click.self="showAttendance = false"></div>

    {{-- Mobile Bottom Sheet --}}
    <div x-show="showAttendance"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="translate-y-full"
         x-transition:enter-end="translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="translate-y-0"
         x-transition:leave-end="translate-y-full"
         class="bottom-sheet md:hidden overscroll-contain">
        <div class="bottom-sheet-handle"></div>
        <div class="p-5 space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2">
                    <button type="button" @click="showAttendance = false" class="p-1.5 -ml-1 text-slate-400 hover:text-slate-600 rounded-lg hover:bg-slate-100 transition-colors">
                        <svg class="size-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/></svg>
                    </button>
                    <h3 class="font-bold text-slate-800 text-base">Absensi &amp; Kasbon</h3>
                </div>
            </div>

            @if ($this->attendanceEmployee)
            <div class="flex items-center gap-3 bg-sky-50 border border-sky-100 rounded-2xl p-3.5">
                <div class="size-10 rounded-xl bg-sky-100 flex items-center justify-center font-bold text-sky-700 text-sm shrink-0">
                    {{ mb_strtoupper(mb_substr($this->attendanceEmployee->name, 0, 1)) }}
                </div>
                <div class="min-w-0 flex-1">
                    <p class="font-bold text-slate-800 text-sm truncate">{{ $this->attendanceEmployee->name }}</p>
                    <p class="text-xs text-slate-500 mt-0.5">{{ $this->attendanceEmployee->role }} · Kasbon: <strong class="text-rose-600">Rp {{ number_format($this->attendanceEmployee->cash_advance) }}</strong></p>
                </div>
            </div>
            @endif

            <div class="space-y-4">
                <div>
                    <label class="form-label text-xs font-semibold text-slate-700">Jumlah Hari Hadir (Bulan Ini)</label>
                    <div class="flex items-center gap-3">
                        <button type="button" wire:click="$set('attendanceDays', max(0, attendanceDays - 1))" class="btn-icon size-11 font-bold text-lg rounded-xl">–</button>
                        <input type="number" wire:model.live="attendanceDays" min="0" max="31" class="form-input text-center text-xl font-bold h-11">
                        <button type="button" wire:click="$set('attendanceDays', min(31, attendanceDays + 1))" class="btn-icon size-11 font-bold text-lg rounded-xl">+</button>
                    </div>
                    @error('attendanceDays') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="form-label text-xs font-semibold text-slate-700">Penyesuaian Kasbon</label>
                    <div class="grid grid-cols-2 gap-2 mb-2">
                        <button type="button" wire:click="$set('cashAdvanceMode','add')" @class(['py-2 px-3 rounded-xl text-xs font-bold border transition-all text-center', 'border-rose-500 bg-rose-50 text-rose-700 ring-1 ring-rose-500' => $cashAdvanceMode==='add', 'border-slate-200 bg-white text-slate-600 hover:bg-slate-50' => $cashAdvanceMode!=='add'])>+ Tambah Kasbon</button>
                        <button type="button" wire:click="$set('cashAdvanceMode','reduce')" @class(['py-2 px-3 rounded-xl text-xs font-bold border transition-all text-center', 'border-emerald-500 bg-emerald-50 text-emerald-700 ring-1 ring-emerald-500' => $cashAdvanceMode==='reduce', 'border-slate-200 bg-white text-slate-600 hover:bg-slate-50' => $cashAdvanceMode!=='reduce'])>– Pelunasan</button>
                    </div>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs font-medium text-slate-400">Rp</span>
                        <input type="number" wire:model="cashAdvanceAdjust" min="0" step="50000" class="form-input pl-9 text-sm font-bold" placeholder="0 = tidak ada perubahan">
                    </div>
                    @error('cashAdvanceAdjust') <p class="form-error">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="pt-2">
                <button type="button" wire:click="saveAttendance" wire:loading.attr="disabled" wire:target="saveAttendance"
                        class="btn-primary w-full justify-center py-3 text-sm font-bold shadow-md">
                    <span wire:loading.remove wire:target="saveAttendance">Simpan Absensi &amp; Kasbon</span>
                    <span wire:loading wire:target="saveAttendance" class="flex items-center gap-2">
                        <svg class="size-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                        Menyimpan…
                    </span>
                </button>
            </div>
        </div>
    </div>

    {{-- Desktop Centered Dialog --}}
    <div x-show="showAttendance"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="hidden md:flex fixed inset-0 z-50 items-center justify-center p-4 pointer-events-none">
        <div class="pointer-events-auto w-full max-w-md bg-white rounded-2xl shadow-2xl overflow-hidden p-6 space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <h3 class="font-bold text-slate-800 text-base">Absensi &amp; Kasbon Karyawan</h3>
                <button type="button" @click="showAttendance = false" class="p-1.5 text-slate-400 hover:text-slate-600 rounded-lg hover:bg-slate-100 transition-colors">
                    <svg class="size-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
                </button>
            </div>

            @if ($this->attendanceEmployee)
            <div class="flex items-center gap-3 bg-sky-50 border border-sky-100 rounded-2xl p-3.5">
                <div class="size-10 rounded-xl bg-sky-100 flex items-center justify-center font-bold text-sky-700 text-sm shrink-0">
                    {{ mb_strtoupper(mb_substr($this->attendanceEmployee->name, 0, 1)) }}
                </div>
                <div class="min-w-0 flex-1">
                    <p class="font-bold text-slate-800 text-sm truncate">{{ $this->attendanceEmployee->name }}</p>
                    <p class="text-xs text-slate-500 mt-0.5">{{ $this->attendanceEmployee->role }} · Kasbon saat ini: <strong class="text-rose-600">Rp {{ number_format($this->attendanceEmployee->cash_advance) }}</strong></p>
                </div>
            </div>
            @endif

            <div class="space-y-4">
                <div>
                    <label class="form-label text-xs font-semibold text-slate-700">Jumlah Hari Hadir (Bulan Ini)</label>
                    <div class="flex items-center gap-3">
                        <button type="button" wire:click="$set('attendanceDays', max(0, attendanceDays - 1))" class="btn-icon size-11 font-bold text-lg rounded-xl">–</button>
                        <input type="number" wire:model.live="attendanceDays" min="0" max="31" class="form-input text-center text-xl font-bold h-11">
                        <button type="button" wire:click="$set('attendanceDays', min(31, attendanceDays + 1))" class="btn-icon size-11 font-bold text-lg rounded-xl">+</button>
                    </div>
                    @error('attendanceDays') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="form-label text-xs font-semibold text-slate-700">Penyesuaian Kasbon</label>
                    <div class="grid grid-cols-2 gap-2 mb-2">
                        <button type="button" wire:click="$set('cashAdvanceMode','add')" @class(['py-2 px-3 rounded-xl text-xs font-bold border transition-all text-center', 'border-rose-500 bg-rose-50 text-rose-700 ring-1 ring-rose-500' => $cashAdvanceMode==='add', 'border-slate-200 bg-white text-slate-600 hover:bg-slate-50' => $cashAdvanceMode!=='add'])>+ Tambah Kasbon</button>
                        <button type="button" wire:click="$set('cashAdvanceMode','reduce')" @class(['py-2 px-3 rounded-xl text-xs font-bold border transition-all text-center', 'border-emerald-500 bg-emerald-50 text-emerald-700 ring-1 ring-emerald-500' => $cashAdvanceMode==='reduce', 'border-slate-200 bg-white text-slate-600 hover:bg-slate-50' => $cashAdvanceMode!=='reduce'])>– Pelunasan</button>
                    </div>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs font-medium text-slate-400">Rp</span>
                        <input type="number" wire:model="cashAdvanceAdjust" min="0" step="50000" class="form-input pl-9 text-sm font-bold" placeholder="0 = tidak ada perubahan">
                    </div>
                    @error('cashAdvanceAdjust') <p class="form-error">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="pt-2">
                <button type="button" wire:click="saveAttendance" wire:loading.attr="disabled" wire:target="saveAttendance"
                        class="btn-primary w-full justify-center py-3 text-sm font-bold shadow-md">
                    <span wire:loading.remove wire:target="saveAttendance">Simpan Absensi &amp; Kasbon</span>
                    <span wire:loading wire:target="saveAttendance" class="flex items-center gap-2">
                        <svg class="size-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                        Menyimpan…
                    </span>
                </button>
            </div>
        </div>
    </div>
</div>

{{-- 3. Payslip Modal --}}
<div x-show="showPayslip" x-cloak style="display:none">
    <div class="bottom-sheet-overlay md:hidden" @click="showPayslip = false"></div>
    <div class="dialog-overlay hidden md:flex" @click.self="showPayslip = false"></div>

    {{-- Mobile Bottom Sheet --}}
    <div x-show="showPayslip"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="translate-y-full"
         x-transition:enter-end="translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="translate-y-0"
         x-transition:leave-end="translate-y-full"
         class="bottom-sheet md:hidden overscroll-contain">
        <div class="bottom-sheet-handle"></div>
        @if ($this->selectedPayslip)
            @include('livewire.hr._payslip', ['slip' => $this->selectedPayslip])
        @endif
    </div>

    {{-- Desktop Centered Dialog --}}
    <div x-show="showPayslip"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="hidden md:flex fixed inset-0 z-50 items-center justify-center p-4 pointer-events-none">
        <div class="pointer-events-auto w-full max-w-md bg-white rounded-2xl shadow-2xl overflow-hidden max-h-[90vh] overflow-y-auto">
            @if ($this->selectedPayslip)
                @include('livewire.hr._payslip', ['slip' => $this->selectedPayslip])
            @endif
        </div>
    </div>
</div>

{{-- 4. Close Period Confirm Modal (Centered) --}}
<div x-show="showClose" x-cloak style="display:none">
    <div class="dialog-overlay">
        <div x-show="showClose"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="dialog-panel p-6 relative max-w-sm w-full">
            <button @click="showClose = false" class="absolute top-4 right-4 text-slate-400 hover:text-slate-600 p-1.5 rounded-lg hover:bg-slate-100 transition-colors">
                <svg class="size-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
            </button>
            <div class="text-center my-3">
                <div class="size-14 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center mx-auto mb-3 text-2xl shadow-xs">
                    🔒
                </div>
                <h3 class="font-bold text-slate-800 text-lg">Tutup Periode Payroll?</h3>
                <p class="text-xs text-slate-500 mt-1.5 leading-relaxed">Output Kg dan kasbon semua karyawan akan direset ke 0. Pastikan slip gaji sudah diterbitkan &amp; dikirim ke karyawan.</p>
            </div>
            <div class="pt-3">
                <button wire:click="closePeriod" class="btn-danger w-full justify-center py-3 text-sm font-bold shadow-md">
                    Ya, Tutup Periode Payroll
                </button>
            </div>
        </div>
    </div>
</div>

{{-- 5. Delete / Deactivate Confirm Modal (Centered) --}}
<div x-show="showDelete" x-cloak style="display:none">
    <div class="dialog-overlay">
        <div x-show="showDelete"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="dialog-panel p-6 relative max-w-sm w-full">
            <button @click="showDelete = false" class="absolute top-4 right-4 text-slate-400 hover:text-slate-600 p-1.5 rounded-lg hover:bg-slate-100 transition-colors">
                <svg class="size-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
            </button>
            <div class="text-center my-3">
                <div class="size-14 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center mx-auto mb-3 text-2xl shadow-xs">
                    🚫
                </div>
                <h3 class="font-bold text-slate-800 text-lg">Nonaktifkan Karyawan?</h3>
                <p class="text-xs text-slate-500 mt-1.5 leading-relaxed">Karyawan akan dinonaktifkan dari sistem dan tidak muncul dalam pembagian komisi Kanban Produksi.</p>
            </div>
            <div class="pt-3">
                <button wire:click="deleteEmployee" class="btn-danger w-full justify-center py-3 text-sm font-bold shadow-md">
                    Ya, Nonaktifkan Karyawan
                </button>
            </div>
        </div>
    </div>
</div>

</div>
