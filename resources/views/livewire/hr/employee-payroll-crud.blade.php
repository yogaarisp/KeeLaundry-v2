<div x-data="{
    showForm:       @entangle('showForm'),
    showAttendance: @entangle('showAttendance'),
    showPayslip:    @entangle('showPayslip'),
    showClose:      @entangle('showCloseConfirm'),
    showDelete:     @entangle('showDeleteConfirm'),
}" class="space-y-5">

{{-- ══ HEADER ══════════════════════════════════════════════════════ --}}
<div class="flex flex-col sm:flex-row sm:items-center gap-3">
    <div>
        <h2 class="text-lg font-bold text-slate-800">HR & Payroll</h2>
        <p class="text-xs text-slate-400 mt-0.5">Kelola karyawan, absensi, kasbon, dan slip gaji</p>
    </div>
    <div class="flex items-center gap-2 sm:ml-auto">
        <button wire:click="$set('showCloseConfirm', true)" class="btn-secondary text-sm py-2 gap-1.5 text-amber-600">
            <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z"/></svg>
            Tutup Periode
        </button>
        <button wire:click="openCreate" class="btn-primary">
            <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
            Tambah Karyawan
        </button>
    </div>
</div>

{{-- ══ TABS ════════════════════════════════════════════════════════ --}}
<div class="flex gap-1 bg-slate-100 rounded-xl p-1 w-fit">
    <button wire:click="$set('activeTab','list')" @class(['px-4 py-2 rounded-lg text-xs font-semibold transition-all', 'bg-white shadow-sm text-sky-700' => $activeTab==='list', 'text-slate-500' => $activeTab!=='list'])>👤 Daftar Karyawan</button>
    <button wire:click="$set('activeTab','payroll')" @class(['px-4 py-2 rounded-lg text-xs font-semibold transition-all', 'bg-white shadow-sm text-sky-700' => $activeTab==='payroll', 'text-slate-500' => $activeTab!=='payroll'])>💰 Kalkulasi Payroll</button>
</div>

{{-- ══ TAB: DAFTAR KARYAWAN ═══════════════════════════════════════ --}}
@if ($activeTab === 'list')
<div class="space-y-3">
    {{-- Desktop table --}}
    <div class="card hidden md:block overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-50/80 border-b border-slate-200/80">
                <tr>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wide">Karyawan</th>
                    <th class="text-center px-4 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wide">Role</th>
                    <th class="text-right px-4 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wide">Gaji Pokok</th>
                    <th class="text-center px-4 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wide">Output Kg</th>
                    <th class="text-right px-4 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wide">Kasbon</th>
                    <th class="text-center px-4 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wide">Hadir</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($this->employees as $e)
                @php $roleClass = match($e->role){ 'KASIR' => 'badge-sky', 'SETRIKA' => 'badge-cyan', 'KURIR' => 'badge-amber', default => 'badge-slate' }; @endphp
                <tr @class(['hover:bg-slate-50/60 transition-colors', 'opacity-50' => !$e->is_active || $e->trashed()])>
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-3">
                            <div class="size-9 rounded-full bg-gradient-to-br from-sky-100 to-indigo-100 flex items-center justify-center shrink-0">
                                <span class="text-sm font-bold text-sky-700">{{ mb_strtoupper(mb_substr($e->name, 0, 1)) }}</span>
                            </div>
                            <div>
                                <p class="font-semibold text-slate-800">{{ $e->name }}</p>
                                <p class="text-xs text-slate-400">{{ $e->phone }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-3 text-center"><span class="badge {{ $roleClass }}">{{ $e->role }}</span></td>
                    <td class="px-4 py-3 text-right tabular-nums font-semibold text-slate-700">Rp {{ number_format($e->base_salary) }}</td>
                    <td class="px-4 py-3 text-center">
                        <span @class(['font-bold tabular-nums', 'text-sky-600' => $e->total_output_kg > 0, 'text-slate-300' => $e->total_output_kg == 0])>
                            {{ number_format($e->total_output_kg, 1) }} Kg
                        </span>
                        @if ($e->commission_rate > 0)
                            <p class="text-[10px] text-emerald-600">+Rp {{ number_format($e->total_commission) }}</p>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-right">
                        @if ($e->cash_advance > 0)
                            <span class="text-rose-600 font-semibold tabular-nums">Rp {{ number_format($e->cash_advance) }}</span>
                        @else
                            <span class="text-slate-300">—</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-center">
                        <span class="font-semibold text-slate-700">{{ $e->attendance_days }} hari</span>
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex items-center justify-end gap-1">
                            @if ($e->trashed())
                                <button wire:click="restoreEmployee({{ $e->id }})" class="btn-secondary text-xs py-1 px-2.5">Pulihkan</button>
                            @else
                                <button wire:click="openPayslip({{ $e->id }})" class="btn-icon" title="Slip Gaji">
                                    <svg class="size-4 text-sky-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"/></svg>
                                </button>
                                <button wire:click="openAttendance({{ $e->id }})" class="btn-icon" title="Absensi & Kasbon">
                                    <svg class="size-4 text-amber-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5"/></svg>
                                </button>
                                <button wire:click="openEdit({{ $e->id }})" class="btn-icon" title="Edit">
                                    <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Z"/></svg>
                                </button>
                                <button wire:click="confirmDelete({{ $e->id }})" class="btn-icon" title="Nonaktifkan">
                                    <svg class="size-4 text-rose-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M22 10.5h-6m-2.25-4.125a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0ZM4 19.235v-.11a6.375 6.375 0 0 1 12.75 0v.109A12.318 12.318 0 0 1 10.374 21c-2.331 0-4.512-.645-6.374-1.766Z"/></svg>
                                </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="px-4 py-12 text-center text-slate-400"><p class="text-2xl mb-2">👤</p><p>Belum ada karyawan</p></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Mobile cards --}}
    <div class="md:hidden space-y-3">
        @forelse ($this->employees as $e)
        @php $rc = match($e->role){ 'KASIR' => 'badge-sky', 'SETRIKA' => 'badge-cyan', 'KURIR' => 'badge-amber', default => 'badge-slate' }; @endphp
        <div @class(['card p-4 space-y-3', 'opacity-60' => !$e->is_active || $e->trashed()])>
            <div class="flex items-center gap-3">
                <div class="size-10 rounded-full bg-gradient-to-br from-sky-100 to-indigo-100 flex items-center justify-center shrink-0">
                    <span class="font-bold text-sky-700">{{ mb_strtoupper(mb_substr($e->name, 0, 1)) }}</span>
                </div>
                <div class="flex-1">
                    <div class="flex items-center gap-2"><p class="font-semibold text-slate-800">{{ $e->name }}</p><span class="badge {{ $rc }} text-[10px]">{{ $e->role }}</span></div>
                    <p class="text-xs text-slate-400">{{ $e->phone }}</p>
                </div>
            </div>
            <div class="grid grid-cols-3 gap-2 text-center text-xs">
                <div class="bg-slate-50 rounded-xl p-2"><p class="text-slate-400">Gaji</p><p class="font-bold text-slate-700">Rp {{ number_format($e->base_salary / 1000) }}rb</p></div>
                <div class="bg-sky-50 rounded-xl p-2"><p class="text-slate-400">Output</p><p class="font-bold text-sky-600">{{ $e->total_output_kg }} Kg</p></div>
                <div @class(['rounded-xl p-2', 'bg-rose-50' => $e->cash_advance > 0, 'bg-slate-50' => $e->cash_advance == 0])><p class="text-slate-400">Kasbon</p><p @class(['font-bold', 'text-rose-600' => $e->cash_advance > 0, 'text-slate-300' => $e->cash_advance == 0])>{{ $e->cash_advance > 0 ? 'Rp ' . number_format($e->cash_advance / 1000) . 'rb' : '—' }}</p></div>
            </div>
            <div class="flex gap-2">
                @if ($e->trashed())
                    <button wire:click="restoreEmployee({{ $e->id }})" class="btn-secondary text-xs py-1.5 flex-1">Pulihkan</button>
                @else
                    <button wire:click="openPayslip({{ $e->id }})" class="btn-secondary text-xs py-1.5 flex-1">Slip Gaji</button>
                    <button wire:click="openAttendance({{ $e->id }})" class="btn-secondary text-xs py-1.5 flex-1">Absensi</button>
                    <button wire:click="openEdit({{ $e->id }})" class="btn-secondary text-xs py-1.5 flex-1">Edit</button>
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
    {{-- Summary KPI --}}
    @php $summary = $this->payrollSummary; @endphp
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
        <div class="kpi-card">
            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide">Total Staff Aktif</p>
            <p class="text-2xl font-bold text-slate-800 mt-1">{{ $summary['employee_count'] }}</p>
        </div>
        <div class="kpi-card">
            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide">Total Gaji Pokok</p>
            <p class="text-xl font-bold text-slate-800 mt-1 tabular-nums">Rp {{ number_format($summary['total_base']) }}</p>
        </div>
        <div class="kpi-card">
            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide">Total Komisi</p>
            <p class="text-xl font-bold text-sky-600 mt-1 tabular-nums">Rp {{ number_format($summary['total_commission']) }}</p>
        </div>
        <div class="kpi-card border-l-4 border-l-sky-400">
            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide">Total Gross Pay</p>
            <p class="text-xl font-bold text-sky-700 mt-1 tabular-nums">Rp {{ number_format($summary['total_gross']) }}</p>
        </div>
    </div>

    {{-- Payslip table --}}
    <div class="card overflow-hidden">
        <table class="w-full text-sm hidden md:table">
            <thead class="bg-slate-50/80 border-b border-slate-200/80">
                <tr>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wide">Karyawan</th>
                    <th class="text-right px-4 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wide">Gaji Pokok</th>
                    <th class="text-right px-4 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wide">Uang Makan</th>
                    <th class="text-right px-4 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wide">Komisi Kg</th>
                    <th class="text-right px-4 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wide">Kasbon</th>
                    <th class="text-right px-4 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wide">Take-Home</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach ($this->allPayslips as $slip)
                @php $emp = $slip['employee']; @endphp
                <tr class="hover:bg-slate-50/60">
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-2">
                            <div class="size-8 rounded-full bg-sky-50 flex items-center justify-center shrink-0"><span class="text-xs font-bold text-sky-700">{{ mb_strtoupper(mb_substr($emp->name,0,1)) }}</span></div>
                            <div><p class="font-semibold text-slate-800">{{ $emp->name }}</p><p class="text-xs text-slate-400">{{ $emp->role }} · {{ $slip['total_output_kg'] }} Kg</p></div>
                        </div>
                    </td>
                    <td class="px-4 py-3 text-right tabular-nums text-slate-700">Rp {{ number_format($slip['base_salary']) }}</td>
                    <td class="px-4 py-3 text-right tabular-nums text-slate-600">Rp {{ number_format($slip['total_allowance']) }}</td>
                    <td class="px-4 py-3 text-right tabular-nums text-sky-600 font-semibold">Rp {{ number_format($slip['total_commission']) }}</td>
                    <td class="px-4 py-3 text-right tabular-nums">
                        @if ($slip['cash_advance'] > 0)
                            <span class="text-rose-600">–Rp {{ number_format($slip['cash_advance']) }}</span>
                        @else <span class="text-slate-300">—</span> @endif
                    </td>
                    <td class="px-4 py-3 text-right">
                        <span class="text-base font-extrabold text-slate-800 tabular-nums">Rp {{ number_format($slip['take_home_pay']) }}</span>
                    </td>
                    <td class="px-4 py-3">
                        <button wire:click="openPayslip({{ $emp->id }})" class="btn-secondary text-xs py-1 px-3">Slip</button>
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot class="bg-sky-50/60 border-t-2 border-sky-200">
                <tr>
                    <td class="px-4 py-3 font-bold text-slate-700" colspan="5">Total Beban Gaji Bulan Ini</td>
                    <td class="px-4 py-3 text-right font-extrabold text-sky-700 text-base tabular-nums">Rp {{ number_format($summary['total_gross']) }}</td>
                    <td class="px-4 py-3"></td>
                </tr>
            </tfoot>
        </table>

        {{-- Mobile payroll cards --}}
        <div class="md:hidden divide-y divide-slate-100">
            @foreach ($this->allPayslips as $slip)
            @php $emp = $slip['employee']; @endphp
            <div class="p-4 space-y-2.5">
                <div class="flex items-center justify-between">
                    <p class="font-semibold text-slate-800">{{ $emp->name }}</p>
                    <p class="font-extrabold text-sky-700 tabular-nums">Rp {{ number_format($slip['take_home_pay']) }}</p>
                </div>
                <div class="grid grid-cols-2 gap-2 text-xs">
                    <div class="bg-slate-50 rounded-lg p-2"><span class="text-slate-400">Gaji: </span><strong>Rp {{ number_format($slip['base_salary']) }}</strong></div>
                    <div class="bg-sky-50 rounded-lg p-2"><span class="text-slate-400">Komisi: </span><strong class="text-sky-600">Rp {{ number_format($slip['total_commission']) }}</strong></div>
                    <div class="bg-slate-50 rounded-lg p-2"><span class="text-slate-400">Makan: </span><strong>Rp {{ number_format($slip['total_allowance']) }}</strong></div>
                    @if ($slip['cash_advance'] > 0)
                    <div class="bg-rose-50 rounded-lg p-2"><span class="text-slate-400">Kasbon: </span><strong class="text-rose-600">–Rp {{ number_format($slip['cash_advance']) }}</strong></div>
                    @endif
                </div>
                <button wire:click="openPayslip({{ $emp->id }})" class="btn-secondary text-xs py-1.5 w-full">Lihat Slip Gaji</button>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endif

{{-- ══ MODALS ══════════════════════════════════════════════════════ --}}

{{-- Employee form --}}
<div x-show="showForm" x-cloak style="display:none">
    <div class="bottom-sheet-overlay md:hidden" @click="showForm = false"></div>
    <div class="dialog-overlay hidden md:flex" @click.self="showForm = false"></div>
    <div x-show="showForm" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="translate-y-full" x-transition:enter-end="translate-y-0" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="translate-y-0" x-transition:leave-end="translate-y-full" class="bottom-sheet md:hidden">
        <div class="bottom-sheet-handle"></div>
        @include('livewire.hr._employee-form')
    </div>
    <div x-show="showForm" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" class="hidden md:flex fixed inset-0 z-50 items-center justify-center p-4 pointer-events-none">
        <div class="pointer-events-auto w-full max-w-lg bg-white rounded-2xl shadow-2xl overflow-hidden max-h-[90vh] overflow-y-auto">
            @include('livewire.hr._employee-form')
        </div>
    </div>
</div>

{{-- Attendance & Kasbon modal --}}
<div x-show="showAttendance" x-cloak style="display:none">
    <div class="dialog-overlay" @click.self="showAttendance = false">
        <div x-show="showAttendance" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" class="dialog-panel p-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-bold text-slate-800">Absensi & Kasbon</h3>
                <button @click="showAttendance = false" class="btn-icon"><svg class="size-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg></button>
            </div>
            @if ($this->attendanceEmployee)
            <div class="flex items-center gap-3 bg-sky-50 rounded-xl p-3 mb-4">
                <div class="size-9 rounded-full bg-sky-100 flex items-center justify-center shrink-0">
                    <span class="font-bold text-sky-700">{{ mb_strtoupper(mb_substr($this->attendanceEmployee->name, 0, 1)) }}</span>
                </div>
                <div>
                    <p class="font-semibold text-slate-800">{{ $this->attendanceEmployee->name }}</p>
                    <p class="text-xs text-slate-500">{{ $this->attendanceEmployee->role }} · Kasbon saat ini: <strong class="text-rose-600">Rp {{ number_format($this->attendanceEmployee->cash_advance) }}</strong></p>
                </div>
            </div>
            @endif
            <div class="space-y-4">
                <div>
                    <label class="form-label">Jumlah Hari Hadir (bulan ini)</label>
                    <div class="flex items-center gap-3">
                        <button wire:click="$set('attendanceDays', max(0, attendanceDays - 1))" class="btn-icon size-10">–</button>
                        <input type="number" wire:model.live="attendanceDays" min="0" max="31" class="form-input text-center text-xl font-bold">
                        <button wire:click="$set('attendanceDays', min(31, attendanceDays + 1))" class="btn-icon size-10">+</button>
                    </div>
                    @error('attendanceDays') <p class="form-error">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="form-label">Kasbon / Pelunasan</label>
                    <div class="flex gap-2 mb-2">
                        <button wire:click="$set('cashAdvanceMode','add')" @class(['flex-1 py-2 rounded-xl text-sm font-semibold border transition-all', 'border-rose-400 bg-rose-50 text-rose-700' => $cashAdvanceMode==='add', 'border-slate-200 text-slate-500 hover:border-slate-300' => $cashAdvanceMode!=='add'])>+ Tambah Kasbon</button>
                        <button wire:click="$set('cashAdvanceMode','reduce')" @class(['flex-1 py-2 rounded-xl text-sm font-semibold border transition-all', 'border-emerald-400 bg-emerald-50 text-emerald-700' => $cashAdvanceMode==='reduce', 'border-slate-200 text-slate-500 hover:border-slate-300' => $cashAdvanceMode!=='reduce'])>– Pelunasan</button>
                    </div>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm text-slate-400">Rp</span>
                        <input type="number" wire:model="cashAdvanceAdjust" min="0" step="50000" class="form-input pl-9" placeholder="0 = tidak ada perubahan">
                    </div>
                    @error('cashAdvanceAdjust') <p class="form-error">{{ $message }}</p> @enderror
                </div>
            </div>
            <div class="flex gap-3 mt-5">
                <button wire:click="saveAttendance" class="btn-primary flex-1">Simpan</button>
                <button @click="showAttendance = false" class="btn-secondary">Batal</button>
            </div>
        </div>
    </div>
</div>

{{-- Payslip modal --}}
<div x-show="showPayslip" x-cloak style="display:none">
    <div class="bottom-sheet-overlay md:hidden" @click="showPayslip = false"></div>
    <div class="dialog-overlay hidden md:flex" @click.self="showPayslip = false"></div>

    <div x-show="showPayslip" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="translate-y-full" x-transition:enter-end="translate-y-0" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="translate-y-0" x-transition:leave-end="translate-y-full" class="bottom-sheet md:hidden">
        <div class="bottom-sheet-handle"></div>
        @if ($this->selectedPayslip) @include('livewire.hr._payslip', ['slip' => $this->selectedPayslip]) @endif
    </div>
    <div x-show="showPayslip" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" class="hidden md:flex fixed inset-0 z-50 items-center justify-center p-4 pointer-events-none">
        <div class="pointer-events-auto w-full max-w-md bg-white rounded-2xl shadow-2xl overflow-hidden max-h-[90vh] overflow-y-auto">
            @if ($this->selectedPayslip) @include('livewire.hr._payslip', ['slip' => $this->selectedPayslip]) @endif
        </div>
    </div>
</div>

{{-- Close period confirm --}}
<div x-show="showClose" x-cloak style="display:none">
    <div class="dialog-overlay">
        <div x-show="showClose" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" class="dialog-panel p-6">
            <div class="text-center mb-4">
                <div class="text-4xl mb-3">🔒</div>
                <p class="font-bold text-slate-800">Tutup Periode Payroll?</p>
                <p class="text-sm text-slate-500 mt-2">Output Kg dan kasbon semua karyawan akan direset ke 0. Pastikan slip gaji sudah dikirim/cetak terlebih dahulu.</p>
            </div>
            <div class="flex gap-3">
                <button wire:click="closePeriod" class="btn-danger flex-1">Ya, Tutup Periode</button>
                <button @click="showClose = false" class="btn-secondary flex-1">Batal</button>
            </div>
        </div>
    </div>
</div>

{{-- Delete confirm --}}
<div x-show="showDelete" x-cloak style="display:none">
    <div class="dialog-overlay">
        <div x-show="showDelete" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" class="dialog-panel p-6">
            <p class="font-bold text-slate-800 mb-1">Nonaktifkan Karyawan?</p>
            <p class="text-sm text-slate-500 mb-5">Karyawan akan di-soft-delete dan tidak bisa login atau klaim komisi baru.</p>
            <div class="flex gap-3">
                <button wire:click="deleteEmployee" class="btn-danger flex-1">Nonaktifkan</button>
                <button @click="showDelete = false" class="btn-secondary flex-1">Batal</button>
            </div>
        </div>
    </div>
</div>

</div>
