<?php

namespace App\Livewire\Settings;

use App\Models\InventoryItem;
use App\Models\ServicePackage;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Component;

class BranchServiceCrud extends Component
{
    // ─── UI State ──────────────────────────────────────────────
    public bool   $showForm          = false;
    public bool   $showCategoryModal = false;
    public string $inspectCategory   = 'KILOAN';
    public bool   $confirmVoid       = false;
    public string $search            = '';
    public string $filterCat         = '';

    // ─── Form Fields ────────────────────────────────────────────
    public ?int    $editingId        = null;
    public string  $name             = '';
    public string  $category         = 'KILOAN';
    public string  $unit             = 'Kg';
    public int     $price_per_unit   = 0;
    public int     $estimated_hours  = 48;
    public float   $detergent_ml     = 15.00;
    public float   $softener_ml      = 10.00;
    public float   $perfume_ml       = 5.00;
    public bool    $is_active        = true;

    // ─── Delete confirm ─────────────────────────────────────────
    public ?int    $deletingId       = null;

    // ─── Computed ───────────────────────────────────────────────

    #[Computed]
    public function packages(): Collection
    {
        return ServicePackage::withTrashed()
            ->when($this->search, fn ($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->when($this->filterCat, fn ($q) => $q->where('category', $this->filterCat))
            ->orderBy('category')
            ->orderBy('name')
            ->get();
    }

    #[Computed]
    public function inspectedPackages(): Collection
    {
        return ServicePackage::withTrashed()
            ->where('category', $this->inspectCategory)
            ->orderBy('is_active', 'desc')
            ->orderBy('name')
            ->get();
    }

    #[Computed]
    public function inventoryChemicals(): Collection
    {
        return InventoryItem::whereIn('category', ['CHEMICAL', 'PARFUM'])
            ->orderBy('category')
            ->orderBy('name')
            ->get(['id', 'name', 'sku', 'category', 'cost_per_unit', 'unit']);
    }

    #[Computed]
    public function categoryStats(): array
    {
        return ServicePackage::selectRaw('category, COUNT(*) as total, SUM(CASE WHEN is_active=1 AND deleted_at IS NULL THEN 1 ELSE 0 END) as active')
            ->withTrashed()
            ->groupBy('category')
            ->get()
            ->keyBy('category')
            ->toArray();
    }

    // ─── BOM Preview ─────────────────────────────────────────────
    #[Computed]
    public function bomPreview(): array
    {
        return [
            'deterjen_ml' => round($this->detergent_ml, 2),
            'softener_ml' => round($this->softener_ml, 2),
            'parfum_ml'   => round($this->perfume_ml, 2),
            'total_ml'    => round($this->detergent_ml + $this->softener_ml + $this->perfume_ml, 2),
        ];
    }

    // ─── Category Inspect Modal ──────────────────────────────────

    public function openCategoryInspect(string $category): void
    {
        $this->inspectCategory = strtoupper($category);
        $this->showCategoryModal = true;
    }

    public function closeCategoryInspect(): void
    {
        $this->showCategoryModal = false;
    }

    public function createForCategory(string $category): void
    {
        $this->resetForm();
        $this->category = strtoupper($category);
        $this->unit = match ($this->category) {
            'SATUAN' => 'Pcs',
            default  => 'Kg',
        };
        $this->is_active = true;
        $this->showCategoryModal = false;
        $this->showForm = true;
    }

    public function autoApproveCategory(string $category): void
    {
        $category = strtoupper($category);
        $count = ServicePackage::where('category', $category)
            ->where('is_active', false)
            ->update(['is_active' => true]);

        unset($this->packages, $this->categoryStats, $this->inspectedPackages);

        $this->dispatch('notify', [
            'type'    => 'success',
            'title'   => 'Auto-Approve Berhasil',
            'message' => "Semua paket {$category} otomatis di-approve & aktif di kasir POS ({$count} paket diaktifkan).",
        ]);
    }

    // ─── Open / Close Modal Form ─────────────────────────────────

    public function openCreate(): void
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function openEdit(int $id): void
    {
        $pkg = ServicePackage::withTrashed()->findOrFail($id);
        $this->editingId       = $pkg->id;
        $this->name            = $pkg->name;
        $this->category        = $pkg->category;
        $this->unit            = $pkg->unit;
        $this->price_per_unit  = $pkg->price_per_unit;
        $this->estimated_hours = $pkg->estimated_hours;
        $this->detergent_ml    = (float) $pkg->detergent_ml_per_unit;
        $this->softener_ml     = (float) $pkg->softener_ml_per_unit;
        $this->perfume_ml      = (float) $pkg->perfume_ml_per_unit;
        $this->is_active       = (bool) $pkg->is_active;

        $this->showCategoryModal = false;
        $this->showForm        = true;
    }

    public function closeForm(): void
    {
        $this->showForm = false;
        $this->resetForm();
    }

    // ─── CRUD ────────────────────────────────────────────────────

    public function save(): void
    {
        $this->validate($this->rules());

        DB::transaction(function () {
            $data = [
                'name'                  => trim($this->name),
                'category'              => $this->category,
                'unit'                  => $this->unit,
                'price_per_unit'        => $this->price_per_unit,
                'estimated_hours'       => $this->estimated_hours,
                'detergent_ml_per_unit' => $this->detergent_ml,
                'softener_ml_per_unit'  => $this->softener_ml,
                'perfume_ml_per_unit'   => $this->perfume_ml,
                'is_active'             => $this->is_active,
            ];

            if ($this->editingId) {
                ServicePackage::withTrashed()->findOrFail($this->editingId)->update($data);
                $msg = "Layanan «{$this->name}» berhasil diperbarui.";
            } else {
                ServicePackage::create($data);
                $msg = "Layanan «{$this->name}» berhasil ditambahkan dan langsung aktif.";
            }

            $this->dispatch('notify', ['type' => 'success', 'title' => 'Tersimpan', 'message' => $msg]);
        });

        $this->closeForm();
        unset($this->packages, $this->categoryStats, $this->inspectedPackages);
    }

    public function toggleActive(int $id): void
    {
        $pkg = ServicePackage::withTrashed()->findOrFail($id);
        $pkg->update(['is_active' => ! $pkg->is_active]);
        $status = $pkg->is_active ? 'diaktifkan (Auto-Approved)' : 'dinonaktifkan';
        $this->dispatch('notify', ['type' => 'info', 'title' => 'Status Diubah', 'message' => "Layanan «{$pkg->name}» {$status}."]);
        unset($this->packages, $this->categoryStats, $this->inspectedPackages);
    }

    public function confirmDelete(int $id): void
    {
        $this->deletingId = $id;
        $this->showCategoryModal = false;
        $this->confirmVoid = true;
    }

    public function deletePackage(): void
    {
        if (! $this->deletingId) {
            return;
        }

        $pkg = ServicePackage::findOrFail($this->deletingId);

        // Cegah hapus jika masih ada order aktif
        $activeOrders = $pkg->orders()->whereNotIn('status', ['SELESAI', 'VOID'])->count();
        if ($activeOrders > 0) {
            $this->dispatch('notify', ['type' => 'error', 'title' => 'Tidak Bisa Dihapus', 'message' => "Ada {$activeOrders} order aktif menggunakan layanan ini."]);
            $this->confirmVoid = false;
            return;
        }

        $name = $pkg->name;
        $pkg->delete(); // soft delete

        $this->dispatch('notify', ['type' => 'success', 'title' => 'Dihapus', 'message' => "Layanan «{$name}» berhasil dihapus."]);
        $this->confirmVoid = false;
        $this->deletingId  = null;
        unset($this->packages, $this->categoryStats, $this->inspectedPackages);
    }

    public function restorePackage(int $id): void
    {
        ServicePackage::withTrashed()->findOrFail($id)->restore();
        $this->dispatch('notify', ['type' => 'success', 'title' => 'Dipulihkan', 'message' => 'Layanan berhasil dipulihkan dari sampah.']);
        unset($this->packages, $this->categoryStats, $this->inspectedPackages);
    }

    // ─── Helpers ─────────────────────────────────────────────────

    private function resetForm(): void
    {
        $this->editingId       = null;
        $this->name            = '';
        $this->category        = 'KILOAN';
        $this->unit            = 'Kg';
        $this->price_per_unit  = 0;
        $this->estimated_hours = 48;
        $this->detergent_ml    = 15.00;
        $this->softener_ml     = 10.00;
        $this->perfume_ml      = 5.00;
        $this->is_active       = true;
        $this->resetValidation();
    }

    private function rules(): array
    {
        return [
            'name'            => ['required', 'string', 'min:3', 'max:100'],
            'category'        => ['required', Rule::in(['KILOAN', 'SATUAN', 'EXPRESS'])],
            'unit'            => ['required', Rule::in(['Kg', 'Pcs', 'Meter'])],
            'price_per_unit'  => ['required', 'integer', 'min:500'],
            'estimated_hours' => ['required', 'integer', 'min:1', 'max:336'],
            'detergent_ml'    => ['required', 'numeric', 'min:0'],
            'softener_ml'     => ['required', 'numeric', 'min:0'],
            'perfume_ml'      => ['required', 'numeric', 'min:0'],
        ];
    }

    public function updatedCategory(): void
    {
        // Auto-set unit saat kategori berubah
        $this->unit = match ($this->category) {
            'SATUAN'  => 'Pcs',
            default   => 'Kg',
        };
    }

    public function render()
    {
        return view('livewire.settings.branch-service-crud')
            ->layout('layouts.app', ['pageTitle' => 'Master Layanan & BOM']);
    }
}
