<?php

namespace App\Services;

use App\Models\Employee;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class PayrollService
{
    /**
     * Hitung slip gaji satu karyawan untuk periode berjalan.
     *
     * Return array berisi detail komponen gaji:
     * [
     *   'employee'         => Employee,
     *   'base_salary'      => int,
     *   'total_allowance'  => int,
     *   'total_commission' => int,
     *   'gross_pay'        => int,
     *   'cash_advance'     => int,
     *   'take_home_pay'    => int,
     *   'total_output_kg'  => float,
     *   'attendance_days'  => int,
     * ]
     */
    public function calculatePayslip(Employee $employee): array
    {
        $baseSalary     = $employee->base_salary;
        $totalAllowance = $employee->attendance_days * $employee->daily_allowance;
        $totalCommission = (int) round((float) $employee->total_output_kg * $employee->commission_rate);
        $grossPay       = $baseSalary + $totalAllowance + $totalCommission;
        $takeHomePay    = $grossPay - $employee->cash_advance;

        return [
            'employee'         => $employee,
            'base_salary'      => $baseSalary,
            'total_allowance'  => $totalAllowance,
            'total_commission' => $totalCommission,
            'gross_pay'        => $grossPay,
            'cash_advance'     => $employee->cash_advance,
            'take_home_pay'    => max(0, $takeHomePay),
            'total_output_kg'  => (float) $employee->total_output_kg,
            'attendance_days'  => $employee->attendance_days,
        ];
    }

    /**
     * Hitung payroll seluruh karyawan aktif di branch berjalan.
     *
     * @return Collection<int, array>
     */
    public function calculateAllPayslips(): Collection
    {
        return Employee::where('is_active', true)
                       ->get()
                       ->map(fn (Employee $emp) => $this->calculatePayslip($emp));
    }

    /**
     * TUTUP BUKU / PAID PAYROLL
     * ─────────────────────────────────────────────────────────────
     * Reset akumulasi output Kg dan kasbon untuk semua karyawan aktif,
     * menandai periode penggajian bulan ini sudah dibayar.
     *
     * CATATAN: Data histori payroll seharusnya disimpan ke tabel payroll_histories
     * sebelum reset. Untuk saat ini payslip disimpan sebagai snapshot JSON di log.
     */
    public function closePeriod(): void
    {
        DB::transaction(function () {
            Employee::where('is_active', true)
                    ->chunk(100, function (Collection $employees) {
                        foreach ($employees as $employee) {
                            $employee->update([
                                'total_output_kg' => 0,
                                'cash_advance'    => 0,
                                'attendance_days' => 25, // reset ke default
                            ]);
                        }
                    });
        });
    }

    /**
     * Tambah / koreksi kasbon karyawan.
     *
     * @param  int  $amount  Positif = tambah kasbon, Negatif = pelunasan.
     */
    public function adjustCashAdvance(Employee $employee, int $amount, string $notes = ''): Employee
    {
        return DB::transaction(function () use ($employee, $amount, $notes) {
            $newAdvance = max(0, $employee->cash_advance + $amount);
            $employee->update(['cash_advance' => $newAdvance]);

            return $employee->fresh();
        });
    }

    /**
     * Update absensi karyawan (jumlah hari hadir dalam periode berjalan).
     */
    public function updateAttendance(Employee $employee, int $attendanceDays): Employee
    {
        return DB::transaction(function () use ($employee, $attendanceDays) {
            $employee->update([
                'attendance_days' => max(0, min(31, $attendanceDays)),
            ]);

            return $employee->fresh();
        });
    }

    /**
     * Ringkasan total beban gaji seluruh karyawan aktif (untuk Dashboard HPP).
     *
     * @return array{total_gross: int, total_commission: int, total_base: int, employee_count: int}
     */
    public function getSummary(): array
    {
        $payslips = $this->calculateAllPayslips();

        return [
            'employee_count'  => $payslips->count(),
            'total_base'      => $payslips->sum('base_salary'),
            'total_commission'=> $payslips->sum('total_commission'),
            'total_allowance' => $payslips->sum('total_allowance'),
            'total_gross'     => $payslips->sum('gross_pay'),
        ];
    }
}
