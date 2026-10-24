@php
$emp = $slip['employee'];
$month = now()->locale('id')->isoFormat('MMMM YYYY');
@endphp
<div class="bg-white">
    {{-- Header with Mobile Back Arrow and Desktop Close Button --}}
    <div class="bg-gradient-to-r from-sky-600 to-sky-700 text-white py-5 px-5 relative">
        <div class="flex items-center justify-between mb-2">
            <button type="button" wire:click="$set('showPayslip', false)"
                    class="md:hidden p-1.5 -ml-1 text-sky-100 hover:text-white rounded-lg hover:bg-white/10 transition-colors"
                    title="Kembali">
                <svg class="size-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/></svg>
            </button>
            <div class="text-xs font-bold text-sky-200 uppercase tracking-widest flex items-center gap-1.5">
                <span>🧺 Kee Laundry ERP</span>
            </div>
            <button type="button" wire:click="$set('showPayslip', false)"
                    class="hidden md:flex p-1.5 text-sky-100 hover:text-white rounded-lg hover:bg-white/10 transition-colors"
                    title="Tutup">
                <svg class="size-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <div class="text-center pt-1 pb-1">
            <h2 class="text-xl font-extrabold tracking-tight">SLIP GAJI KARYAWAN</h2>
            <p class="text-sky-100 text-xs mt-1 font-medium">Periode: {{ $month }}</p>
        </div>
    </div>

    {{-- Employee info --}}
    <div class="px-5 py-4 border-b border-slate-100 flex items-center gap-3 bg-slate-50/50">
        <div class="size-11 rounded-xl bg-sky-100/70 border border-sky-200 text-sky-700 flex items-center justify-center font-bold text-base shrink-0 shadow-2xs">
            {{ mb_strtoupper(mb_substr($emp->name,0,1)) }}
        </div>
        <div class="min-w-0 flex-1">
            <p class="font-bold text-slate-800 text-base leading-tight truncate">{{ $emp->name }}</p>
            <p class="text-xs text-slate-500 mt-0.5">{{ $emp->role }} · {{ $emp->phone }}</p>
            <div class="flex items-center gap-2 mt-1 text-[11px] text-slate-500 font-medium">
                <span class="bg-white border border-slate-200 px-2 py-0.5 rounded-md font-semibold">{{ $slip['attendance_days'] }} Hari Hadir</span>
                <span class="bg-white border border-slate-200 px-2 py-0.5 rounded-md font-semibold">{{ number_format($slip['total_output_kg'], 1) }} Kg Output</span>
            </div>
        </div>
    </div>

    {{-- Salary breakdown --}}
    <div class="px-5 py-4 space-y-2.5">
        {{-- Pendapatan --}}
        <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-2">Rincian Pendapatan</p>
        @foreach ([
            ['Gaji Pokok', $slip['base_salary']],
            ['Uang Makan (' . $slip['attendance_days'] . ' hari)', $slip['total_allowance']],
            ['Komisi Produksi (' . number_format($slip['total_output_kg'], 1) . ' Kg)', $slip['total_commission']],
        ] as [$label, $amount])
        <div class="flex justify-between items-center text-xs sm:text-sm">
            <span class="text-slate-600 font-medium">{{ $label }}</span>
            <span class="font-bold text-slate-800 tabular-nums">Rp {{ number_format($amount) }}</span>
        </div>
        @endforeach

        <div class="border-t border-dashed border-slate-200 my-2.5"></div>
        <div class="flex justify-between items-center text-xs sm:text-sm">
            <span class="font-bold text-slate-700">Total Penghasilan (Gross Pay)</span>
            <span class="font-extrabold text-slate-800 tabular-nums">Rp {{ number_format($slip['gross_pay']) }}</span>
        </div>

        @if ($slip['cash_advance'] > 0)
        <div class="border-t border-dashed border-slate-200 my-2.5"></div>
        <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-2">Potongan</p>
        <div class="flex justify-between items-center text-xs sm:text-sm">
            <span class="text-rose-600 font-medium">Potongan Kasbon / Pinjaman</span>
            <span class="font-bold text-rose-600 tabular-nums">–Rp {{ number_format($slip['cash_advance']) }}</span>
        </div>
        @endif

        {{-- Take-Home Pay Highlight --}}
        <div class="mt-4 rounded-2xl bg-sky-50/80 border-2 border-sky-200 p-3.5 flex justify-between items-center shadow-xs">
            <div>
                <span class="font-extrabold text-sky-900 text-sm block">Take-Home Pay</span>
                <span class="text-[11px] text-sky-600">Gaji bersih yang diterima</span>
            </div>
            <span class="text-xl sm:text-2xl font-black text-sky-700 tabular-nums">Rp {{ number_format($slip['take_home_pay']) }}</span>
        </div>
    </div>

    {{-- Footer Actions --}}
    <div class="px-5 pb-5 pt-2 space-y-3">
        <p class="text-center text-[11px] text-slate-400 border-t border-slate-100 pt-3">
            Slip gaji diterbitkan secara digital &amp; sah oleh manajemen Kee Laundry.
        </p>
        @php
            $cleanPhone = preg_replace('/^0/', '62', preg_replace('/[^\d]/', '', $emp->phone));
            $waMessage = "Halo {$emp->name}! Slip gaji bulan {$month} sudah diterbitkan.\n\nTake-Home Pay: Rp " . number_format($slip['take_home_pay']) . "\n\nTerima kasih atas kerja kerasmu di Kee Laundry!";
        @endphp
        <a href="https://wa.me/{{ $cleanPhone }}?text={{ urlencode($waMessage) }}"
           target="_blank"
           class="w-full flex items-center justify-center gap-2 py-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 active:scale-95 text-white text-sm font-bold shadow-md transition-all">
            <svg class="size-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12.04 2c-5.46 0-9.91 4.45-9.91 9.91 0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38c1.45.79 3.08 1.21 4.74 1.21 5.46 0 9.91-4.45 9.91-9.91 0-2.65-1.03-5.14-2.9-7.01A9.816 9.816 0 0 0 12.04 2m.01 1.67c4.54 0 8.24 3.7 8.24 8.24 0 2.2-.86 4.28-2.42 5.84a8.18 8.18 0 0 1-5.82 2.41h-.01c-1.42 0-2.82-.37-4.06-1.08l-.29-.17-3.02.79.81-2.94-.19-.3a8.214 8.214 0 0 1-1.26-4.38c0-4.54 3.7-8.24 8.24-8.24M8.53 7.33c-.16 0-.35.06-.53.25-.19.19-.71.7-.71 1.71 0 1.01.73 1.99.83 2.13.1.13 1.44 2.2 3.5 3.09.49.21.87.34 1.17.43.49.16.94.14 1.29.08.4-.06 1.21-.5 1.38-.98.17-.48.17-.9.12-.98-.05-.09-.19-.14-.4-.25s-1.23-.61-1.42-.68c-.19-.07-.33-.1-.47.1-.14.21-.54.68-.66.82-.12.14-.24.16-.45.05-.21-.1-.89-.33-1.69-1.05-.62-.56-1.04-1.25-1.16-1.46-.12-.21-.01-.32.09-.42.09-.09.21-.24.31-.36.1-.12.14-.21.21-.35.07-.14.03-.26-.02-.36-.05-.1-.47-1.14-.65-1.56-.17-.42-.35-.36-.48-.37-.12-.01-.26-.01-.4-.01z"/></svg>
            Kirim Slip via WhatsApp
        </a>
    </div>
</div>
