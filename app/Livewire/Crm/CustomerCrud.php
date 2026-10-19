<?php

namespace App\Livewire\Crm;

use App\Models\Complaint;
use App\Models\Customer;
use App\Models\Order;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Component;

class CustomerCrud extends Component
{
    // ── Tabs ───────────────────────────────────────────────────────
    public string $activeTab = 'customers'; // customers | churn | complaints

    // ── Customer list filters ──────────────────────────────────────
    public string $search    = '';
    public string $filterTier = '';

    // ── Customer form ──────────────────────────────────────────────
    public bool    $showForm     = false;
    public ?int    $editingId    = null;
    public string  $name         = '';
    public string  $phone        = '';
    public string  $address      = '';
    public string  $tier         = 'REGULAR';
    public string  $preferences  = '';

    // ── Delete confirm ─────────────────────────────────────────────
    public bool   $showDeleteConfirm = false;
    public ?int   $deletingId        = null;

    // ── Top-up Deposit modal ───────────────────────────────────────
    public bool   $showTopUp       = false;
    public ?int   $topUpCustomerId = null;
    public float  $topUpKg         = 10.0;
    public string $topUpNotes      = '';

    // ── Complaint form ─────────────────────────────────────────────
    public bool   $showComplaintForm   = false;
    public ?int   $editingComplaintId  = null;
    public ?int   $complaintCustomerId = null;
    public string $complaintInvoice    = '';
    public string $issueType           = '';
    public string $description         = '';
    public string $complaintStatus     = 'OPEN';
    public string $compensation        = '';

    // ── Complaint list filters ─────────────────────────────────────
    public string $complaintSearch = '';
    public string $complaintFilter = '';

    // ── Customer detail drawer ─────────────────────────────────────
    public bool  $showDetail    = false;
    public ?int  $detailCustomerId = null;

    // ─── Computed: Customer list ────────────────────────────────────

    #[Computed]
    public function customers(): Collection
    {
        return Customer::withTrashed()
            ->when($this->search, fn ($q) =>
                $q->where(fn ($q) =>
                    $q->where('name', 'like', "%{$this->search}%")
                      ->orWhere('phone', 'like', "%{$this->search}%")
                )
            )
            ->when($this->filterTier, fn ($q) => $q->where('tier', $this->filterTier))
            ->orderByDesc('last_order_date')
            ->get();
    }

    #[Computed]
    public function churnCustomers(): Collection
    {
        return Customer::where(function ($q) {
            $q->where('last_order_date', '<', now()->subDays(14))
              ->orWhereNull('last_order_date');
        })
        ->orderBy('last_order_date')
        ->get();
    }

    #[Computed]
    public function tierStats(): array
    {
        return Customer::selectRaw("tier, COUNT(*) as total")
            ->groupBy('tier')
            ->pluck('total', 'tier')
            ->toArray();
    }

    #[Computed]
    public function detailCustomer(): ?Customer
    {
        return $this->detailCustomerId
            ? Customer::withTrashed()->with(['orders' => fn ($q) => $q->withoutGlobalScope('branch')->orderByDesc('created_at')->limit(5), 'complaints'])->find($this->detailCustomerId)
            : null;
    }

    // ─── Computed: Complaints ────────────────────────────────────────

    #[Computed]
    public function complaints(): Collection
    {
        return Complaint::with('customer:id,name,phone')
            ->when($this->complaintSearch, fn ($q) =>
                $q->where(fn ($q) =>
                    $q->where('invoice_number', 'like', "%{$this->complaintSearch}%")
                      ->orWhereHas('customer', fn ($q) =>
                          $q->where('name', 'like', "%{$this->complaintSearch}%")
                      )
                )
            )
            ->when($this->complaintFilter, fn ($q) => $q->where('status', $this->complaintFilter))
            ->orderByRaw("FIELD(status,'OPEN','IN_PROGRESS','RESOLVED')")
            ->orderByDesc('created_at')
            ->get();
    }

    #[Computed]
    public function complaintStats(): array
    {
        return Complaint::selectRaw("status, COUNT(*) as total")
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();
    }

    // ─── Customer CRUD ────────────────────────────────────────────────

    public function openCreate(): void
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function openEdit(int $id): void
    {
        $c = Customer::withTrashed()->findOrFail($id);
        $this->editingId   = $c->id;
        $this->name        = $c->name;
        $this->phone       = $c->phone;
        $this->address     = $c->address ?? '';
        $this->tier        = $c->tier;
        $this->preferences = $c->preferences ?? '';
        $this->showForm    = true;
    }

    public function saveCustomer(): void
    {
        $rules = [
            'name'    => ['required', 'string', 'min:2', 'max:100'],
            'phone'   => ['required', 'string', 'min:8', 'max:20',
                          Rule::unique('customers', 'phone')->ignore($this->editingId)->whereNull('deleted_at')],
            'address' => ['nullable', 'string', 'max:255'],
            'tier'    => ['required', Rule::in(['REGULAR', 'SILVER', 'VIP'])],
        ];

        $this->validate($rules, [], ['name' => 'Nama', 'phone' => 'Nomor HP']);

        DB::transaction(function () {
            $data = [
                'name'        => trim($this->name),
                'phone'       => trim($this->phone),
                'address'     => trim($this->address) ?: null,
                'tier'        => $this->tier,
                'preferences' => trim($this->preferences) ?: null,
            ];

            if ($this->editingId) {
                Customer::withTrashed()->findOrFail($this->editingId)->update($data);
                $msg = "Data «{$this->name}» berhasil diperbarui.";
            } else {
                Customer::create($data);
                $msg = "Pelanggan «{$this->name}» berhasil didaftarkan.";
            }

            $this->dispatch('notify', ['type' => 'success', 'title' => 'Tersimpan', 'message' => $msg]);
        });

        $this->closeForm();
        unset($this->customers, $this->tierStats);
    }

    public function confirmDelete(int $id): void
    {
        $this->deletingId = $id;
        $this->showDeleteConfirm = true;
    }

    public function deleteCustomer(): void
    {
        $c = Customer::findOrFail($this->deletingId);

        $activeOrders = Order::withoutGlobalScope('branch')
            ->where('customer_id', $c->id)
            ->whereNotIn('status', ['SELESAI', 'VOID'])
            ->count();

        if ($activeOrders > 0) {
            $this->dispatch('notify', ['type' => 'error', 'title' => 'Tidak Bisa Dihapus', 'message' => "Masih ada {$activeOrders} order aktif."]);
            $this->showDeleteConfirm = false;
            return;
        }

        $name = $c->name;
        $c->delete();
        $this->dispatch('notify', ['type' => 'success', 'title' => 'Dihapus', 'message' => "Pelanggan «{$name}» berhasil dihapus."]);
        $this->showDeleteConfirm = false;
        $this->deletingId = null;
        unset($this->customers, $this->tierStats);
    }

    public function restoreCustomer(int $id): void
    {
        Customer::withTrashed()->findOrFail($id)->restore();
        $this->dispatch('notify', ['type' => 'success', 'title' => 'Dipulihkan', 'message' => 'Pelanggan berhasil dipulihkan.']);
        unset($this->customers);
    }

    public function upgradeTier(int $id, string $tier): void
    {
        $c = Customer::findOrFail($id);
        $old = $c->tier;
        $c->update(['tier' => $tier]);
        $this->dispatch('notify', ['type' => 'info', 'title' => 'Tier Diubah', 'message' => "{$c->name}: {$old} → {$tier}"]);
        unset($this->customers, $this->churnCustomers);
    }

    // ─── Top-Up Deposit Kg ───────────────────────────────────────────

    public function openTopUp(int $customerId): void
    {
        $this->topUpCustomerId = $customerId;
        $this->topUpKg         = 10.0;
        $this->topUpNotes      = '';
        $this->showTopUp       = true;
    }

    public function saveTopUp(): void
    {
        $this->validate([
            'topUpKg'    => ['required', 'numeric', 'min:0.5', 'max:500'],
            'topUpNotes' => ['nullable', 'string', 'max:200'],
        ], [], ['topUpKg' => 'Jumlah Kg']);

        DB::transaction(function () {
            Customer::findOrFail($this->topUpCustomerId)->increment('deposit_kg', $this->topUpKg);
        });

        $c = Customer::find($this->topUpCustomerId);
        $this->dispatch('notify', ['type' => 'success', 'title' => 'Top-Up Berhasil', 'message' => "+{$this->topUpKg} Kg untuk {$c?->name}. Saldo: {$c?->deposit_kg} Kg"]);
        $this->showTopUp = false;
        unset($this->customers, $this->detailCustomer);
    }

    // ─── Customer detail drawer ──────────────────────────────────────

    public function openDetail(int $id): void
    {
        $this->detailCustomerId = $id;
        $this->showDetail       = true;
        unset($this->detailCustomer);
    }

    // ─── Complaint CRUD ──────────────────────────────────────────────

    public function openCreateComplaint(?int $customerId = null): void
    {
        $this->resetComplaintForm();
        $this->complaintCustomerId = $customerId;
        $this->showComplaintForm   = true;
    }

    public function openEditComplaint(int $id): void
    {
        $c = Complaint::findOrFail($id);
        $this->editingComplaintId  = $c->id;
        $this->complaintCustomerId = $c->customer_id;
        $this->complaintInvoice    = $c->invoice_number;
        $this->issueType           = $c->issue_type;
        $this->description         = $c->description;
        $this->complaintStatus     = $c->status;
        $this->compensation        = $c->compensation ?? '';
        $this->showComplaintForm   = true;
    }

    public function saveComplaint(): void
    {
        $this->validate([
            'complaintCustomerId' => ['required', 'integer', 'exists:customers,id'],
            'complaintInvoice'    => ['required', 'string'],
            'issueType'           => ['required', 'string', 'min:3'],
            'description'         => ['required', 'string', 'min:10'],
            'complaintStatus'     => ['required', Rule::in(['OPEN', 'IN_PROGRESS', 'RESOLVED'])],
        ], [], [
            'complaintCustomerId' => 'Pelanggan',
            'complaintInvoice'    => 'Nomor invoice',
            'issueType'           => 'Jenis masalah',
            'description'         => 'Deskripsi',
        ]);

        DB::transaction(function () {
            $data = [
                'customer_id'    => $this->complaintCustomerId,
                'invoice_number' => trim($this->complaintInvoice),
                'issue_type'     => trim($this->issueType),
                'description'    => trim($this->description),
                'status'         => $this->complaintStatus,
                'compensation'   => trim($this->compensation) ?: null,
            ];

            if ($this->editingComplaintId) {
                Complaint::findOrFail($this->editingComplaintId)->update($data);
                $msg = 'Tiket komplain berhasil diperbarui.';
            } else {
                Complaint::create($data);
                $msg = 'Tiket komplain baru berhasil dicatat.';
            }

            $this->dispatch('notify', ['type' => 'success', 'title' => 'Tersimpan', 'message' => $msg]);
        });

        $this->showComplaintForm = false;
        $this->resetComplaintForm();
        unset($this->complaints, $this->complaintStats);
    }

    public function resolveComplaint(int $id): void
    {
        Complaint::findOrFail($id)->update(['status' => 'RESOLVED']);
        $this->dispatch('notify', ['type' => 'success', 'title' => 'Resolved', 'message' => 'Komplain ditutup sebagai RESOLVED.']);
        unset($this->complaints, $this->complaintStats);
    }

    public function deleteComplaint(int $id): void
    {
        $complaint = Complaint::findOrFail($id);
        $complaint->delete(); // hard delete (Complaint tidak punya SoftDeletes)
        $this->dispatch('notify', ['type' => 'info', 'title' => 'Dihapus', 'message' => 'Tiket komplain berhasil dihapus.']);
        unset($this->complaints, $this->complaintStats);
    }

    // ─── Helpers ─────────────────────────────────────────────────────

    private function resetForm(): void
    {
        $this->editingId   = null;
        $this->name        = '';
        $this->phone       = '';
        $this->address     = '';
        $this->tier        = 'REGULAR';
        $this->preferences = '';
        $this->resetValidation();
    }

    private function resetComplaintForm(): void
    {
        $this->editingComplaintId  = null;
        $this->complaintCustomerId = null;
        $this->complaintInvoice    = '';
        $this->issueType           = '';
        $this->description         = '';
        $this->complaintStatus     = 'OPEN';
        $this->compensation        = '';
        $this->resetValidation();
    }

    public function closeForm(): void
    {
        $this->showForm = false;
        $this->resetForm();
    }

    public function render()
    {
        return view('livewire.crm.customer-crud')
            ->layout('layouts.app', ['pageTitle' => 'CRM Pelanggan']);
    }
}
