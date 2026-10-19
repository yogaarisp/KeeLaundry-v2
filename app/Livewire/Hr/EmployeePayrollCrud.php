<?php

namespace App\Livewire\Hr;

use App\Models\Employee;
use App\Services\PayrollService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Component;

class EmployeePayrollCrud extends Component
{
    // ── Tabs ───────────────────────────────────────────────────────
    public string $activeTab = 'list'; // list | payroll

    // ── Employee form ──────────────────────────────────────────────
    public bool   $showForm     = false;
    public ?int   $editingId    = null;
    public string $name         = '';
    public string $phone        = '';
    public string $role         = 'OPERATOR_CUCI';
    public int    $base_salary  = 1800000;
    public int    $daily_allowance = 25000;
    public int    $commission_rate = 500;
    public bool   $is_active    = true;

    // ── Attendance / Kasbon modal ──────────────────────────────────
    public bool   $showAttendance   = false;
    public ?int   $attendanceEmpId  = null;
    public int    $attendanceDays   = 25;
    public int    $cashAdvanceAdjust = 0;
    public string $cashAdvanceMode  = 'add'; // add | reduce

    // ── Payslip modal ─────────────────────────────────────────────
    public bool  $showPayslip    = false;
    public ?int  $payslipEmpId   = null;

    // ── Close Period confirm ───────────────────────────────────────
    public bool  $showCloseConfirm = false;

    // ── Delete confirm ─────────────────────────────────────────────
    public bool  $showDeleteConfirm = false;
    public ?int  $deletingId        = null;

    protected PayrollService $payrollService;

    public function boot(PayrollService $payrollService): void
    {
        $this->payrollService = $payrollService;
    }

    // ─── Computed ────────────────────────────────────────────────────

    #[Computed]
    public function employees(): Collection
    {
        return Employee::withTrashed()
            ->orderByDesc('is_active')
            ->orderBy('name')
            ->get();
    }

    #[Computed]
    public function allPayslips(): Collection
    {
        return $this->payrollService->calculateAllPayslips();
    }

    #[Computed]
    public function payrollSummary(): array
    {
        return $this->payrollService->getSummary();
    }

    #[Computed]
    public function selectedPayslip(): ?array
    {
        if (! $this->payslipEmpId) {
            return null;
        }
        $emp = Employee::find($this->payslipEmpId);
        return $emp ? $this->payrollService->calculatePayslip($emp) : null;
    }

    #[Computed]
    public function attendanceEmployee(): ?Employee
    {
        return $this->attendanceEmpId ? Employee::find($this->attendanceEmpId) : null;
    }

    // ─── Employee CRUD ───────────────────────────────────────────────

    public function openCreate(): void
    {
        $this->resetEmpForm();
        $this->showForm = true;
    }

    public function openEdit(int $id): void
    {
        $e = Employee::withTrashed()->findOrFail($id);
        $this->editingId        = $e->id;
        $this->name             = $e->name;
        $this->phone            = $e->phone;
        $this->role             = $e->role;
        $this->base_salary      = $e->base_salary;
        $this->daily_allowance  = $e->daily_allowance;
        $this->commission_rate  = $e->commission_rate;
        $this->is_active        = (bool) $e->is_active;
        $this->showForm         = true;
    }

    public function saveEmployee(): void
    {
        $this->validate([
            'name'             => ['required', 'string', 'min:2', 'max:100'],
            'phone'            => ['required', 'string', 'min:8', 'max:20'],
            'role'             => ['required', Rule::in(['KASIR', 'OPERATOR_CUCI', 'SETRIKA', 'KURIR'])],
            'base_salary'      => ['required', 'integer', 'min:500000'],
            'daily_allowance'  => ['required', 'integer', 'min:0'],
            'commission_rate'  => ['required', 'integer', 'min:0'],
        ], [], ['base_salary' => 'Gaji pokok', 'daily_allowance' => 'Uang makan', 'commission_rate' => 'Tarif komisi']);

        DB::transaction(function () {
            $data = [
                'name'            => trim($this->name),
                'phone'           => trim($this->phone),
                'role'            => $this->role,
                'base_salary'     => $this->base_salary,
                'daily_allowance' => $this->daily_allowance,
                'commission_rate' => $this->commission_rate,
                'is_active'       => $this->is_active,
            ];

            if ($this->editingId) {
                Employee::withTrashed()->findOrFail($this->editingId)->update($data);
                $msg = "Data «{$this->name}» berhasil diperbarui.";
            } else {
                Employee::create(array_merge($data, ['attendance_days' => 25, 'total_output_kg' => 0, 'cash_advance' => 0]));
                $msg = "Karyawan «{$this->name}» berhasil didaftarkan.";
            }

            $this->dispatch('notify', ['type' => 'success', 'title' => 'Tersimpan', 'message' => $msg]);
        });

        $this->showForm = false;
        $this->resetEmpForm();
        unset($this->employees, $this->allPayslips, $this->payrollSummary);
    }

    public function confirmDelete(int $id): void
    {
        $this->deletingId = $id;
        $this->showDeleteConfirm = true;
    }

    public function deleteEmployee(): void
    {
        $e = Employee::findOrFail($this->deletingId);
        $name = $e->name;
        $e->update(['is_active' => false]);
        $e->delete();
        $this->dispatch('notify', ['type' => 'success', 'title' => 'Dinonaktifkan', 'message' => "Karyawan «{$name}» berhasil dinonaktifkan."]);
        $this->showDeleteConfirm = false;
        unset($this->employees, $this->allPayslips);
    }

    public function restoreEmployee(int $id): void
    {
        Employee::withTrashed()->findOrFail($id)->restore();
        Employee::withTrashed()->findOrFail($id)->update(['is_active' => true]);
        $this->dispatch('notify', ['type' => 'success', 'title' => 'Dipulihkan', 'message' => 'Karyawan berhasil dipulihkan.']);
        unset($this->employees, $this->allPayslips);
    }

    // ─── Attendance & Kasbon ─────────────────────────────────────────

    public function openAttendance(int $id): void
    {
        $emp = Employee::findOrFail($id);
        $this->attendanceEmpId   = $id;
        $this->attendanceDays    = $emp->attendance_days;
        $this->cashAdvanceAdjust = 0;
        $this->cashAdvanceMode   = 'add';
        $this->showAttendance    = true;
        unset($this->attendanceEmployee);
    }

    public function saveAttendance(): void
    {
        $this->validate([
            'attendanceDays'    => ['required', 'integer', 'min:0', 'max:31'],
            'cashAdvanceAdjust' => ['required', 'integer', 'min:0'],
        ]);

        DB::transaction(function () {
            $emp = Employee::findOrFail($this->attendanceEmpId);
            $this->payrollService->updateAttendance($emp, $this->attendanceDays);

            if ($this->cashAdvanceAdjust > 0) {
                $amount = $this->cashAdvanceMode === 'add'
                    ? $this->cashAdvanceAdjust
                    : -$this->cashAdvanceAdjust;
                $this->payrollService->adjustCashAdvance($emp, $amount);
            }
        });

        $this->dispatch('notify', ['type' => 'success', 'title' => 'Absensi Disimpan', 'message' => "Absensi & kasbon berhasil diperbarui."]);
        $this->showAttendance = false;
        unset($this->employees, $this->allPayslips, $this->payrollSummary, $this->attendanceEmployee);
    }

    // ─── Payslip ─────────────────────────────────────────────────────

    public function openPayslip(int $id): void
    {
        $this->payslipEmpId = $id;
        $this->showPayslip  = true;
        unset($this->selectedPayslip);
    }

    // ─── Close Period ────────────────────────────────────────────────

    public function closePeriod(): void
    {
        $this->payrollService->closePeriod();
        $this->dispatch('notify', ['type' => 'warning', 'title' => 'Periode Ditutup', 'message' => 'Output Kg & kasbon semua karyawan telah di-reset. Payroll bulan ini selesai.']);
        $this->showCloseConfirm = false;
        unset($this->employees, $this->allPayslips, $this->payrollSummary);
    }

    // ─── Helpers ─────────────────────────────────────────────────────

    private function resetEmpForm(): void
    {
        $this->editingId       = null;
        $this->name            = '';
        $this->phone           = '';
        $this->role            = 'OPERATOR_CUCI';
        $this->base_salary     = 1800000;
        $this->daily_allowance = 25000;
        $this->commission_rate = 500;
        $this->is_active       = true;
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.hr.employee-payroll-crud')
            ->layout('layouts.app', ['pageTitle' => 'HR & Payroll']);
    }
}
