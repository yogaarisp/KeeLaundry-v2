@php
$emp = $slip['employee'];
$month = now()->locale('id')->isoFormat('MMMM YYYY');
@endphp
<div class="bg-white">
    {{-- Header --}}
    <div class="bg-gradient-to-r from-sky-600 to-sky-700 text-white text-center py-6 px-5">
        <p class="text-xs font-semibold text-sky-200 uppercase tracking-widest mb-1">Kee Laundry – Pusat</p>
        <p class="text-xl font-extrabold">SLIP GAJI</p>
        <p class="text-sky-200 text-sm mt-1">Periode: {{ $month }}</p>
    </div>

    {{-- Employee info --}}
    <div class="px-5 py-4 border-b border-slate-100 flex items-center gap-3">
        <div class="size-12 rounded-xl bg-sky-50 flex items-center justify-center shrink-0">
            <span class="text-lg font-extrabold text-sky-700">{{ mb_strtoupper(mb_substr($emp->name,0,1)) }}</span>
        </div>
        <div>
            <p class="font-extrabold text-slate-800 text-base">{{ $emp->name }}</p>
            <p class="text-sm text-slate-500">{{ $emp->role }} · {{ $emp->phone }}</p>
            <p class="text-xs text-slate-400">{{ $slip['attendance_days'] }} hari hadir · {{ $slip['total_output_kg'] }} Kg output</p>
        </div>
    </div>

    {{-- Salary breakdown --}}
    <div class="px-5 py-4 space-y-2">
        {{-- Pendapatan --}}
        <p class="text-xs font-bold text-slate-400 uppercase tracking-wide mb-2">Pendapatan</p>
        @foreach ([
            ['Gaji Pokok', $slip['base_salary']],
            ['Uang Makan (' . $slip['attendance_days'] . ' hari)', $slip['total_allowance']],
            ['Komisi Produksi (' . $slip['total_output_kg'] . ' Kg)', $slip['total_commission']],
        ] as [$label, $amount])
        <div class="flex justify-between items-center">
            <span class="text-sm text-slate-600">{{ $label }}</span>
            <span class="text-sm font-semibold text-slate-800 tabular-nums">Rp {{ number_format($amount) }}</span>
        </div>
        @endforeach

        <div class="border-t border-dashed border-slate-200 my-2"></div>
        <div class="flex justify-between items-center">
            <span class="text-sm font-bold text-slate-700">Gross Pay</span>
            <span class="text-base font-extrabold text-slate-800 tabular-nums">Rp {{ number_format($slip['gross_pay']) }}</span>
        </div>

        @if ($slip['cash_advance'] > 0)
        <div class="border-t border-dashed border-slate-200 my-2"></div>
        <p class="text-xs font-bold text-slate-400 uppercase tracking-wide mb-2">Potongan</p>
        <div class="flex justify-between items-center">
            <span class="text-sm text-rose-600">Kasbon / Pinjaman</span>
            <span class="text-sm font-semibold text-rose-600 tabular-nums">–Rp {{ number_format($slip['cash_advance']) }}</span>
        </div>
        @endif

        {{-- Take home --}}
        <div class="mt-3 rounded-2xl bg-sky-50 border-2 border-sky-200 px-4 py-3 flex justify-between items-center">
            <span class="font-extrabold text-sky-800">Take-Home Pay</span>
            <span class="text-xl font-extrabold text-sky-700 tabular-nums">Rp {{ number_format($slip['take_home_pay']) }}</span>
        </div>
    </div>

    {{-- Footer --}}
    <div class="px-5 pb-5 space-y-3">
        <p class="text-center text-xs text-slate-400 border-t border-dashed border-slate-200 pt-3">Slip gaji ini diterbitkan secara digital oleh sistem Kee Laundry ERP.</p>
        <div class="flex gap-2">
            <a href="https://wa.me/62{{ ltrim($emp->phone, '0') }}?text={{ urlencode('Halo ' . $emp->name . '! Slip gaji bulan ' . $month . ' sudah tersedia. Take-home: Rp ' . number_format($slip['take_home_pay'])) }}"
               target="_blank"
               class="flex-1 flex items-center justify-center gap-2 py-2.5 rounded-xl bg-emerald-500 text-white text-sm font-semibold hover:bg-emerald-600 transition-colors">
                <svg class="size-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
                Kirim WA
            </a>
            <button wire:click="$set('showPayslip', false)" class="btn-secondary flex-1">Tutup</button>
        </div>
    </div>
</div>
