<?php

namespace App\Livewire\Inventory;

use App\Models\InventoryItem;
use App\Models\Order;
use App\Models\StockMutation;
use App\Models\UtilityLog;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Component;

class InventoryUtilityCrud extends Component
{
    // ── Tabs ───────────────────────────────────────────────────────
    public string $activeTab = 'inventory'; // inventory | opname | utility

    // ── Inventory form ─────────────────────────────────────────────
    public bool   $showItemForm  = false;
    public ?int   $editingItemId = null;
    public string $sku           = '';
    public string $itemName      = '';
    public string $category      = 'CHEMICAL';
    public float  $system_stock  = 0;
    public string $unit          = 'ml';
    public float  $min_threshold = 1000;
    public float  $usage_per_kg  = 15;
    public float  $cost_per_unit = 0.08;

    // ── Restock form ───────────────────────────────────────────────
    public bool   $showRestock      = false;
    public ?int   $restockItemId    = null;
    public float  $restockAmount    = 0;
    public string $restockNotes     = '';

    // ── Opname form ────────────────────────────────────────────────
    public array  $opnameValues     = []; // [item_id => actual_stock_input]
    public bool   $opnameSaved      = false;

    // ── Utility form ───────────────────────────────────────────────
    public bool   $showUtilityForm  = false;
    public ?int   $editingUtilityId = null;
    public string $utilityType      = 'LISTRIK_PLN';
    public string $periodDate       = '';
    public float  $meterStart       = 0;
    public float  $meterEnd         = 0;
    public float  $usageVolume      = 0;
    public string $utilityUnit      = 'kWh';
    public int    $totalCost        = 0;
    public string $recordedBy       = 'Admin';
    public string $utilityNotes     = '';

    // ── Utility filter ─────────────────────────────────────────────
    public string $utilityFilter    = '';

    public function mount(): void
    {
        $this->periodDate = now()->format('Y-m-d');
        // Pre-fill opname values
        $this->initOpnameValues();
    }

    private function initOpnameValues(): void
    {
        $items = InventoryItem::get(['id', 'actual_stock']);
        foreach ($items as $item) {
            $this->opnameValues[$item->id] = (float) $item->actual_stock;
        }
    }

    // ─── Computed ─────────────────────────────────────────────────────

    #[Computed]
    public function inventoryItems(): Collection
    {
        return InventoryItem::withTrashed()
            ->orderBy('category')
            ->orderBy('name')
            ->get();
    }

    #[Computed]
    public function lowStockItems(): Collection
    {
        return InventoryItem::whereRaw('system_stock <= min_threshold')
            ->orderByRaw('(system_stock / NULLIF(min_threshold, 0)) ASC')
            ->get();
    }

    #[Computed]
    public function stockBarData(): Collection
    {
        return InventoryItem::whereIn('category', ['CHEMICAL', 'PARFUM'])
            ->orderByRaw('(system_stock / NULLIF(min_threshold * 3, 0)) ASC')
            ->get()
            ->map(fn (InventoryItem $i) => [
                'id'           => $i->id,
                'name'         => $i->name,
                'system_stock' => (float) $i->system_stock,
                'actual_stock' => (float) $i->actual_stock,
                'min_threshold'=> (float) $i->min_threshold,
                'unit'         => $i->unit,
                'category'     => $i->category,
                'is_low'       => $i->is_low_stock,
                'percent'      => min(100, round(((float)$i->system_stock / max((float)$i->min_threshold * 3, 0.001)) * 100, 1)),
                'color'        => $i->stock_percentage >= 50 ? 'emerald' : ($i->stock_percentage >= 20 ? 'amber' : 'rose'),
                'variance'     => $i->variance,
            ]);
    }

    #[Computed]
    public function mutationLog(): Collection
    {
        return StockMutation::with('inventoryItem:id,name,unit')
            ->orderByDesc('created_at')
            ->limit(30)
            ->get();
    }

    #[Computed]
    public function utilityLogs(): Collection
    {
        return UtilityLog::when($this->utilityFilter, fn ($q) => $q->where('utility_type', $this->utilityFilter))
            ->orderByDesc('period_date')
            ->get();
    }

    #[Computed]
    public function utilityStats(): array
    {
        $mtd = UtilityLog::whereYear('period_date', now()->year)
            ->whereMonth('period_date', now()->month)
            ->get();

        return [
            'total_cost_mtd' => $mtd->sum('total_cost'),
            'listrik'        => $mtd->where('utility_type', 'LISTRIK_PLN')->sum('total_cost'),
            'air'            => $mtd->where('utility_type', 'AIR_PDAM')->sum('total_cost'),
            'gas'            => $mtd->where('utility_type', 'GAS_LPG')->sum('total_cost'),
        ];
    }

    #[Computed]
    public function hppUtilityPerKg(): float
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

        return round($this->utilityStats['total_cost_mtd'] / $totalKgMtd, 0);
    }

    #[Computed]
    public function restockItem(): ?InventoryItem
    {
        return $this->restockItemId ? InventoryItem::find($this->restockItemId) : null;
    }

    // ─── Inventory CRUD ───────────────────────────────────────────────

    public function openCreateItem(): void
    {
        $this->resetItemForm();
        $this->showItemForm = true;
    }

    public function openEditItem(int $id): void
    {
        $item = InventoryItem::withTrashed()->findOrFail($id);
        $this->editingItemId  = $item->id;
        $this->sku            = $item->sku;
        $this->itemName       = $item->name;
        $this->category       = $item->category;
        $this->system_stock   = (float) $item->system_stock;
        $this->unit           = $item->unit;
        $this->min_threshold  = (float) $item->min_threshold;
        $this->usage_per_kg   = (float) $item->usage_per_kg;
        $this->cost_per_unit  = (float) $item->cost_per_unit;
        $this->showItemForm   = true;
    }

    public function saveItem(): void
    {
        $this->validate([
            'sku'           => ['required', 'string', 'max:30',
                Rule::unique('inventory_items', 'sku')->ignore($this->editingItemId)->whereNull('deleted_at')],
            'itemName'      => ['required', 'string', 'min:3', 'max:100'],
            'category'      => ['required', Rule::in(['CHEMICAL', 'PARFUM', 'PACKAGING', 'GAS'])],
            'unit'          => ['required', 'string'],
            'min_threshold' => ['required', 'numeric', 'min:0'],
            'cost_per_unit' => ['required', 'numeric', 'min:0'],
        ], [], ['itemName' => 'Nama item', 'min_threshold' => 'Stok minimum', 'cost_per_unit' => 'Harga per unit']);

        DB::transaction(function () {
            $data = [
                'sku'           => strtoupper(trim($this->sku)),
                'name'          => trim($this->itemName),
                'category'      => $this->category,
                'unit'          => $this->unit,
                'min_threshold' => $this->min_threshold,
                'usage_per_kg'  => $this->usage_per_kg,
                'cost_per_unit' => $this->cost_per_unit,
            ];

            if ($this->editingItemId) {
                InventoryItem::withTrashed()->findOrFail($this->editingItemId)->update($data);
                $msg = "Item «{$this->itemName}» berhasil diperbarui.";
            } else {
                InventoryItem::create(array_merge($data, [
                    'system_stock' => $this->system_stock,
                    'actual_stock' => $this->system_stock,
                ]));
                // Catat sebagai RESTOCK_IN awal
                if ($this->system_stock > 0) {
                    $newItem = InventoryItem::where('sku', strtoupper(trim($this->sku)))->firstOrFail();
                    StockMutation::create([
                        'inventory_item_id' => $newItem->id,
                        'mutation_type'     => 'RESTOCK_IN',
                        'amount'            => $this->system_stock,
                        'notes'             => 'Stok awal item baru',
                    ]);
                }
                $msg = "Item «{$this->itemName}» berhasil ditambahkan.";
            }

            $this->dispatch('notify', ['type' => 'success', 'title' => 'Tersimpan', 'message' => $msg]);
        });

        $this->showItemForm = false;
        $this->resetItemForm();
        unset($this->inventoryItems, $this->stockBarData, $this->lowStockItems);
    }

    public function deleteItem(int $id): void
    {
        $item = InventoryItem::findOrFail($id);
        $item->delete();
        $this->dispatch('notify', ['type' => 'info', 'title' => 'Dihapus', 'message' => "«{$item->name}» berhasil dihapus."]);
        unset($this->inventoryItems, $this->stockBarData);
    }

    public function restoreItem(int $id): void
    {
        InventoryItem::withTrashed()->findOrFail($id)->restore();
        $this->dispatch('notify', ['type' => 'success', 'title' => 'Dipulihkan', 'message' => 'Item berhasil dipulihkan.']);
        unset($this->inventoryItems, $this->stockBarData);
    }

    // ─── Restock / Stok Masuk ─────────────────────────────────────────

    public function openRestock(int $itemId): void
    {
        $this->restockItemId = $itemId;
        $this->restockAmount = 0;
        $this->restockNotes  = '';
        $this->showRestock   = true;
        unset($this->restockItem);
    }

    public function saveRestock(): void
    {
        $this->validate([
            'restockAmount' => ['required', 'numeric', 'min:0.1'],
        ], [], ['restockAmount' => 'Jumlah restock']);

        DB::transaction(function () {
            $item = InventoryItem::findOrFail($this->restockItemId);
            $item->increment('system_stock', $this->restockAmount);
            $item->increment('actual_stock', $this->restockAmount);

            StockMutation::create([
                'inventory_item_id' => $item->id,
                'mutation_type'     => 'RESTOCK_IN',
                'amount'            => $this->restockAmount,
                'notes'             => $this->restockNotes ?: "Restock: +{$this->restockAmount} {$item->unit}",
            ]);

            $this->dispatch('notify', [
                'type'    => 'success',
                'title'   => 'Restock Berhasil',
                'message' => "+{$this->restockAmount} {$item->unit} untuk {$item->name}. Stok: " . ($item->system_stock + 0) . " {$item->unit}",
            ]);
        });

        $this->showRestock = false;
        unset($this->inventoryItems, $this->stockBarData, $this->lowStockItems, $this->mutationLog);
    }

    // ─── Stok Opname ──────────────────────────────────────────────────

    public function saveOpname(): void
    {
        DB::transaction(function () {
            $adjustedCount = 0;
            foreach ($this->opnameValues as $itemId => $actualValue) {
                $item     = InventoryItem::find($itemId);
                if (! $item) continue;

                $variance = (float) $actualValue - (float) $item->system_stock;
                $item->update(['actual_stock' => (float) $actualValue]);

                if (abs($variance) > 0.01) {
                    StockMutation::create([
                        'inventory_item_id' => $item->id,
                        'mutation_type'     => 'OPNAME_ADJUSTMENT',
                        'amount'            => $variance,
                        'notes'             => "Opname fisik: sistem={$item->system_stock}, fisik={$actualValue}, selisih=" . round($variance, 2),
                    ]);
                    $adjustedCount++;
                }
            }

            $this->dispatch('notify', [
                'type'    => 'success',
                'title'   => 'Opname Disimpan',
                'message' => "Opname selesai. {$adjustedCount} item memiliki selisih.",
            ]);
        });

        $this->opnameSaved = true;
        unset($this->inventoryItems, $this->stockBarData, $this->mutationLog);
    }

    public function updatedUtilityType(): void
    {
        $this->utilityUnit = match ($this->utilityType) {
            'AIR_PDAM' => 'm3',
            'GAS_LPG'  => 'Tabung',
            default    => 'kWh',
        };
        $this->meterStart  = 0;
        $this->meterEnd    = 0;
        $this->usageVolume = 0;
    }

    public function updatedMeterEnd(): void
    {
        if ($this->utilityType !== 'GAS_LPG') {
            $this->usageVolume = max(0, round($this->meterEnd - $this->meterStart, 2));
        }
    }

    // ─── Utility CRUD ─────────────────────────────────────────────────

    public function openCreateUtility(): void
    {
        $this->resetUtilityForm();
        $this->showUtilityForm = true;
    }

    public function openEditUtility(int $id): void
    {
        $log = UtilityLog::findOrFail($id);
        $this->editingUtilityId = $log->id;
        $this->utilityType      = $log->utility_type;
        $this->periodDate       = $log->period_date->format('Y-m-d');
        $this->meterStart       = (float) $log->meter_start;
        $this->meterEnd         = (float) $log->meter_end;
        $this->usageVolume      = (float) $log->usage_volume;
        $this->utilityUnit      = $log->unit;
        $this->totalCost        = $log->total_cost;
        $this->recordedBy       = $log->recorded_by;
        $this->utilityNotes     = $log->notes ?? '';
        $this->showUtilityForm  = true;
    }

    public function saveUtility(): void
    {
        $this->validate([
            'utilityType'  => ['required', Rule::in(['LISTRIK_PLN', 'AIR_PDAM', 'GAS_LPG'])],
            'periodDate'   => ['required', 'date'],
            'usageVolume'  => ['required', 'numeric', 'min:0.01'],
            'totalCost'    => ['required', 'integer', 'min:1'],
            'recordedBy'   => ['required', 'string'],
        ], [], ['usageVolume' => 'Volume pemakaian', 'totalCost' => 'Total biaya', 'recordedBy' => 'Dicatat oleh']);

        DB::transaction(function () {
            $data = [
                'utility_type'  => $this->utilityType,
                'period_date'   => $this->periodDate,
                'meter_start'   => $this->meterStart,
                'meter_end'     => $this->meterEnd,
                'usage_volume'  => $this->usageVolume,
                'unit'          => $this->utilityUnit,
                'total_cost'    => $this->totalCost,
                'recorded_by'   => trim($this->recordedBy),
                'notes'         => trim($this->utilityNotes) ?: null,
            ];

            if ($this->editingUtilityId) {
                UtilityLog::findOrFail($this->editingUtilityId)->update($data);
                $msg = 'Log utilitas berhasil diperbarui.';
            } else {
                UtilityLog::create($data);
                $msg = "Log {$this->utilityType} berhasil dicatat.";
            }

            $this->dispatch('notify', ['type' => 'success', 'title' => 'Tersimpan', 'message' => $msg]);
        });

        $this->showUtilityForm = false;
        $this->resetUtilityForm();
        unset($this->utilityLogs, $this->utilityStats, $this->hppUtilityPerKg);
    }

    public function deleteUtility(int $id): void
    {
        UtilityLog::findOrFail($id)->delete();
        $this->dispatch('notify', ['type' => 'info', 'title' => 'Dihapus', 'message' => 'Log utilitas berhasil dihapus.']);
        unset($this->utilityLogs, $this->utilityStats, $this->hppUtilityPerKg);
    }

    // ─── Helpers ──────────────────────────────────────────────────────

    private function resetItemForm(): void
    {
        $this->editingItemId = null;
        $this->sku           = '';
        $this->itemName      = '';
        $this->category      = 'CHEMICAL';
        $this->system_stock  = 0;
        $this->unit          = 'ml';
        $this->min_threshold = 1000;
        $this->usage_per_kg  = 15;
        $this->cost_per_unit = 0.08;
        $this->resetValidation();
    }

    private function resetUtilityForm(): void
    {
        $this->editingUtilityId = null;
        $this->utilityType      = 'LISTRIK_PLN';
        $this->periodDate       = now()->format('Y-m-d');
        $this->meterStart       = 0;
        $this->meterEnd         = 0;
        $this->usageVolume      = 0;
        $this->utilityUnit      = 'kWh';
        $this->totalCost        = 0;
        $this->recordedBy       = 'Admin';
        $this->utilityNotes     = '';
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.inventory.inventory-utility-crud')
            ->layout('layouts.app', ['pageTitle' => 'Inventaris & Utilitas']);
    }
}
