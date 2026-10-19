<?php

namespace App\Livewire\Dashboard;

use App\Models\Customer;
use App\Models\Employee;
use App\Models\InventoryItem;
use App\Models\Order;
use App\Models\StockMutation;
use App\Models\UtilityLog;
use App\Services\PayrollService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Attributes\Polling;
use Livewire\Component;

class ExecutiveDashboard extends Component
{
    /** Filter periode: 'today' | '7d' | '30d' | 'mtd' */
    public string $period = 'mtd';

    /** Tab aktif pada bagian chart: 'omzet' | 'hpp' */
    public string $chartTab = 'omzet';

    // ─── Computed: KPI Cards ────────────────────────────────────

    #[Computed]
    public function periodDates(): array
    {
        return match ($this->period) {
            'today' => [Carbon::today(),            Carbon::now()],
            '7d'    => [Carbon::now()->subDays(6)->startOfDay(), Carbon::now()],
            '30d'   => [Carbon::now()->subDays(29)->startOfDay(), Carbon::now()],
            'mtd'   => [Carbon::now()->startOfMonth(), Carbon::now()],
            default => [Carbon::now()->startOfMonth(), Carbon::now()],
        };
    }

    #[Computed]
    public function omzetPeriod(): int
    {
        [$from, $to] = $this->periodDates;
        return (int) Order::withoutGlobalScope('branch')
            ->where('branch_id', session('active_branch_id', 1))
            ->whereNotIn('status', ['VOID'])
            ->whereBetween('created_at', [$from, $to])
            ->sum('total_price');
    }

    #[Computed]
    public function omzetLastPeriod(): int
    {
        [$from, $to] = $this->periodDates;
        $diff = $from->diffInSeconds($to);
        $prevFrom = $from->copy()->subSeconds($diff);
        $prevTo   = $from->copy()->subSecond();

        return (int) Order::withoutGlobalScope('branch')
            ->where('branch_id', session('active_branch_id', 1))
            ->whereNotIn('status', ['VOID'])
            ->whereBetween('created_at', [$prevFrom, $prevTo])
            ->sum('total_price');
    }

    #[Computed]
    public function orderCountPeriod(): int
    {
        [$from, $to] = $this->periodDates;
        return Order::withoutGlobalScope('branch')
            ->where('branch_id', session('active_branch_id', 1))
            ->whereNotIn('status', ['VOID'])
            ->whereBetween('created_at', [$from, $to])
            ->count();
    }

    #[Computed]
    public function totalKgPeriod(): float
    {
        [$from, $to] = $this->periodDates;
        return (float) Order::withoutGlobalScope('branch')
            ->where('branch_id', session('active_branch_id', 1))
            ->whereNotIn('status', ['VOID'])
            ->whereBetween('created_at', [$from, $to])
            ->sum('quantity');
    }

    #[Computed]
    public function avgOrderValue(): int
    {
        return $this->orderCountPeriod > 0
            ? (int) round($this->omzetPeriod / $this->orderCountPeriod)
            : 0;
    }

    // ─── Computed: HPP / Cost per Kg ────────────────────────────

    /**
     * Biaya bahan baku bulan berjalan (dari StockMutation AUTO_DEDUCT).
     */
    #[Computed]
    public function bomCostMtd(): float
    {
        $mutations = StockMutation::withoutGlobalScope('branch')
            ->where('branch_id', session('active_branch_id', 1))
            ->where('mutation_type', 'AUTO_DEDUCT_ORDER')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->with('inventoryItem')
            ->get();

        return $mutations->sum(function ($m) {
            $costPerUnit = (float) ($m->inventoryItem?->cost_per_unit ?? 0);
            return abs((float) $m->amount) * $costPerUnit;
        });
    }

    /**
     * Biaya utilitas bulan berjalan (listrik + air + gas).
     */
    #[Computed]
    public function utilityCostMtd(): int
    {
        return (int) UtilityLog::withoutGlobalScope('branch')
            ->where('branch_id', session('active_branch_id', 1))
            ->whereYear('period_date', now()->year)
            ->whereMonth('period_date', now()->month)
            ->sum('total_cost');
    }

    /**
     * Estimasi beban gaji bulan berjalan.
     */
    #[Computed]
    public function payrollCostMtd(): int
    {
        return (int) app(PayrollService::class)->getSummary()['total_gross'];
    }

    /**
     * HPP per Kg = (BOM + Utilitas + Gaji) / Total Kg bulan berjalan.
     */
    #[Computed]
    public function hppPerKg(): float
    {
        $totalKgMtd = (float) Order::withoutGlobalScope('branch')
            ->where('branch_id', session('active_branch_id', 1))
            ->whereNotIn('status', ['VOID'])
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('quantity');

        if ($totalKgMtd <= 0) {
            return 0;
        }

        $totalCost = $this->bomCostMtd + $this->utilityCostMtd + $this->payrollCostMtd;
        return round($totalCost / $totalKgMtd, 0);
    }

    #[Computed]
    public function targetHppPerKg(): int
    {
        return \App\Models\Branch::find(session('active_branch_id', 1))?->target_hpp_per_kg ?? 1850;
    }

    #[Computed]
    public function hppVariance(): int
    {
        return (int) $this->hppPerKg - $this->targetHppPerKg;
    }

    // ─── Computed: Kanban Summary ────────────────────────────────

    #[Computed]
    public function kanbanCounts(): array
    {
        $branchId = session('active_branch_id', 1);
        $counts = Order::withoutGlobalScope('branch')
            ->where('branch_id', $branchId)
            ->whereNotIn('status', ['SELESAI', 'VOID'])
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        $statuses = ['ANTREAN', 'CUCI', 'SETRIKA', 'PACKING', 'SIAP_DIAMBIL'];
        return array_map(fn($s) => ['status' => $s, 'count' => $counts[$s] ?? 0], $statuses);
    }

    #[Computed]
    public function activeOrdersTotal(): int
    {
        return array_sum(array_column($this->kanbanCounts, 'count'));
    }

    #[Computed]
    public function unpaidOrdersTotal(): int
    {
        return Order::withoutGlobalScope('branch')
            ->where('branch_id', session('active_branch_id', 1))
            ->where('payment_status', 'BELUM_LUNAS')
            ->whereNotIn('status', ['VOID'])
            ->count();
    }

    #[Computed]
    public function unpaidRevenue(): int
    {
        return (int) Order::withoutGlobalScope('branch')
            ->where('branch_id', session('active_branch_id', 1))
            ->where('payment_status', 'BELUM_LUNAS')
            ->whereNotIn('status', ['VOID'])
            ->sum('total_price');
    }

    // ─── Computed: Chart Data (Daily Omzet, last 30 days) ───────

    #[Computed]
    public function dailyOmzetChart(): array
    {
        $days = $this->period === 'today' ? 1 : ($this->period === '7d' ? 7 : 30);
        $branchId = session('active_branch_id', 1);

        $data = Order::withoutGlobalScope('branch')
            ->where('branch_id', $branchId)
            ->whereNotIn('status', ['VOID'])
            ->where('created_at', '>=', now()->subDays($days - 1)->startOfDay())
            ->selectRaw('DATE(created_at) as date, SUM(total_price) as total, COUNT(*) as count, SUM(quantity) as kg')
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        $result = ['labels' => [], 'omzet' => [], 'count' => [], 'kg' => []];
        for ($i = $days - 1; $i >= 0; $i--) {
            $date  = now()->subDays($i)->format('Y-m-d');
            $label = now()->subDays($i)->locale('id')->isoFormat($days <= 7 ? 'ddd D' : 'D MMM');
            $row   = $data[$date] ?? null;
            $result['labels'][] = $label;
            $result['omzet'][]  = $row ? (int) $row->total : 0;
            $result['count'][]  = $row ? (int) $row->count : 0;
            $result['kg'][]     = $row ? (float) $row->kg  : 0;
        }

        return $result;
    }

    // ─── Computed: Inventory Stock Bars ─────────────────────────

    #[Computed]
    public function inventoryBars(): Collection
    {
        return InventoryItem::withoutGlobalScope('branch')
            ->where('branch_id', session('active_branch_id', 1))
            ->whereIn('category', ['CHEMICAL', 'PARFUM'])
            ->orderByRaw('(system_stock / NULLIF(min_threshold, 0)) ASC')
            ->get()
            ->map(function (InventoryItem $item) {
                $ratio   = (float) $item->min_threshold > 0
                    ? min(1.0, (float) $item->system_stock / ((float) $item->min_threshold * 3))
                    : 1.0;
                $percent = round($ratio * 100, 1);

                return [
                    'id'          => $item->id,
                    'name'        => $item->name,
                    'sku'         => $item->sku,
                    'system_stock'=> (float) $item->system_stock,
                    'min_threshold'=> (float) $item->min_threshold,
                    'unit'        => $item->unit,
                    'category'    => $item->category,
                    'percent'     => $percent,
                    'is_low'      => (float) $item->system_stock <= (float) $item->min_threshold,
                    'color'       => $percent >= 50 ? 'emerald' : ($percent >= 20 ? 'amber' : 'rose'),
                ];
            });
    }

    // ─── Computed: CRM Churn Alert ───────────────────────────────

    #[Computed]
    public function churnCustomers(): Collection
    {
        return Customer::withoutGlobalScope('branch')
            ->where('branch_id', session('active_branch_id', 1))
            ->where(function ($q) {
                $q->where('last_order_date', '<', now()->subDays(14))
                  ->orWhereNull('last_order_date');
            })
            ->orderBy('last_order_date')
            ->limit(8)
            ->get()
            ->map(function (Customer $c) {
                return [
                    'id'         => $c->id,
                    'name'       => $c->name,
                    'phone'      => $c->phone,
                    'tier'       => $c->tier,
                    'days_ago'   => $c->last_order_date
                        ? (int) $c->last_order_date->diffInDays(now())
                        : null,
                    'deposit_kg' => (float) $c->deposit_kg,
                ];
            });
    }

    #[Computed]
    public function churnCount(): int
    {
        return Customer::withoutGlobalScope('branch')
            ->where('branch_id', session('active_branch_id', 1))
            ->where(function ($q) {
                $q->where('last_order_date', '<', now()->subDays(14))
                  ->orWhereNull('last_order_date');
            })
            ->count();
    }

    // ─── Computed: Open Complaints ───────────────────────────────

    #[Computed]
    public function openComplaintsCount(): int
    {
        return \App\Models\Complaint::withoutGlobalScope('branch')
            ->where('branch_id', session('active_branch_id', 1))
            ->whereIn('status', ['OPEN', 'IN_PROGRESS'])
            ->count();
    }

    // ─── Actions ────────────────────────────────────────────────

    public function setPeriod(string $period): void
    {
        $this->period = $period;
        unset($this->omzetPeriod, $this->omzetLastPeriod, $this->orderCountPeriod,
              $this->totalKgPeriod, $this->avgOrderValue, $this->dailyOmzetChart);
    }

    public function setChartTab(string $tab): void
    {
        $this->chartTab = $tab;
    }

    public function refresh(): void
    {
        unset(
            $this->omzetPeriod, $this->omzetLastPeriod, $this->orderCountPeriod,
            $this->totalKgPeriod, $this->avgOrderValue, $this->hppPerKg,
            $this->kanbanCounts, $this->inventoryBars, $this->churnCustomers,
            $this->dailyOmzetChart, $this->utilityCostMtd, $this->payrollCostMtd,
            $this->bomCostMtd
        );
        $this->dispatch('notify', [
            'type'    => 'success',
            'title'   => 'Data diperbarui',
            'message' => 'Dashboard berhasil direfresh.',
        ]);
    }

    public function render()
    {
        return view('livewire.dashboard.executive-dashboard')
            ->layout('layouts.app', ['pageTitle' => 'Dashboard']);
    }
}
