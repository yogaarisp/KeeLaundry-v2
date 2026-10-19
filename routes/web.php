<?php

use App\Livewire\Crm\CustomerCrud;
use App\Livewire\Dashboard\ExecutiveDashboard;
use App\Livewire\Hr\EmployeePayrollCrud;
use App\Livewire\Inventory\InventoryUtilityCrud;
use App\Livewire\Pos\PosOrderCrud;
use App\Livewire\Production\KanbanBoard;
use App\Livewire\Public\MobileOrderTracker;
use App\Livewire\Auth\Login;
use App\Livewire\Delivery\DeliveryCrud;
use App\Livewire\Settings\BranchServiceCrud;
use App\Livewire\Settings\BranchSettings;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Kee Laundry ERP — Web Routes
|--------------------------------------------------------------------------
*/

// ────────────────────────────────────────────────────────────────────────
// PUBLIC ROUTES & AUTHENTICATION
// ────────────────────────────────────────────────────────────────────────

/** Halaman tracking cucian untuk pelanggan (no login) */
Route::get('/cek', MobileOrderTracker::class)->name('tracker');

/** Alias: /lacak & /track → /cek */
Route::redirect('/lacak', '/cek')->name('lacak');
Route::redirect('/track', '/cek')->name('track');

/** Halaman Login Sistem & Akses Demo Akun Role */
Route::get('/login', Login::class)->name('login');

/** Logout */
Route::get('/logout', function () {
    Auth::logout();
    session()->forget('active_role');
    session()->invalidate();
    session()->regenerateToken();
    return redirect('/login');
})->name('logout');

// ────────────────────────────────────────────────────────────────────────
// ROLE SWITCHER (Demo / Persona Toggle)
// ────────────────────────────────────────────────────────────────────────
Route::get('/switch-role/{role}', function (string $role) {
    $role = strtoupper($role);
    $allowed = ['ADMIN', 'KASIR', 'PRODUKSI', 'KURIR'];
    if (in_array($role, $allowed)) {
        session(['active_role' => $role]);
        return match ($role) {
            'KASIR'    => redirect('/pos'),
            'PRODUKSI' => redirect('/produksi'),
            'KURIR'    => redirect('/antar-jemput'),
            default    => redirect('/'),
        };
    }
    return redirect()->back();
})->name('switch-role');

// ────────────────────────────────────────────────────────────────────────
// INTERNAL / STAFF ROUTES
// ────────────────────────────────────────────────────────────────────────

/** Dashboard Eksekutif */
Route::get('/', ExecutiveDashboard::class)->name('dashboard');

/** Kasir POS + Riwayat Order */
Route::get('/pos', PosOrderCrud::class)->name('pos');

/** Antar Jemput Laundry (Kurir & Kasir) */
Route::get('/antar-jemput', DeliveryCrud::class)->name('delivery');

/** Kanban Produksi */
Route::get('/produksi', KanbanBoard::class)->name('produksi');

/** CRM Pelanggan + Tiket Komplain */
Route::get('/pelanggan', CustomerCrud::class)->name('pelanggan');

/** HR & Payroll */
Route::get('/karyawan', EmployeePayrollCrud::class)->name('karyawan');

/** Inventaris Bahan Baku + Utilitas */
Route::get('/inventaris', InventoryUtilityCrud::class)->name('inventaris');

/** Alias: /utilitas → inventaris (dengan tab utility terbuka) */
Route::get('/utilitas', fn () => redirect('/inventaris'))->name('utilitas');

/** Master Layanan & BOM Sabun */
Route::get('/layanan', BranchServiceCrud::class)->name('layanan');

/** Pengaturan Toko: Profil, Struk, Template WA */
Route::get('/pengaturan', BranchSettings::class)->name('pengaturan');

/** Alias: /komplain → CRM Pelanggan */
Route::get('/komplain', fn () => redirect('/pelanggan'))->name('komplain');

