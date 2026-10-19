<?php

namespace App\Livewire\Settings;

use App\Models\Branch;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Component;

class BranchSettings extends Component
{
    // ── Active tab ─────────────────────────────────────────────────
    public string $activeTab = 'profile'; // profile | receipt | whatsapp

    // ── Branch Profile fields ──────────────────────────────────────
    public string $code               = '';
    public string $name               = '';
    public string $phone              = '';
    public string $address            = '';
    public string $operational_hours  = '';
    public int    $target_hpp_per_kg  = 1850;
    public bool   $is_active          = true;

    // ── Receipt fields ─────────────────────────────────────────────
    public string $receipt_footer     = '';

    // ── Printer Thermal fields ─────────────────────────────────────
    public string $printer_type        = 'SYSTEM'; // BLUETOOTH | USB | SYSTEM
    public string $printer_paper_size  = '58mm';   // 58mm | 80mm
    public bool   $printer_auto_print  = false;
    public string $printer_device_name = '';

    // ── WhatsApp Template fields ───────────────────────────────────
    public string $wa_template_ready  = '';
    public string $wa_template_done   = '';
    public string $wa_template_promo  = '';

    // ── WA Preview ─────────────────────────────────────────────────
    public string $previewInvoice     = 'INV-20261019-0001';
    public string $previewName        = 'Dewi Kusuma';
    public string $previewRak         = 'Rak A-03';

    public function mount(): void
    {
        $this->fillFromBranch();
    }

    private function fillFromBranch(): void
    {
        $branch = Branch::find(session('active_branch_id', 1));
        if (! $branch) {
            return;
        }

        $this->code                = $branch->code;
        $this->name                = $branch->name;
        $this->phone               = $branch->phone;
        $this->address             = $branch->address;
        $this->operational_hours   = $branch->operational_hours ?? '';
        $this->target_hpp_per_kg   = $branch->target_hpp_per_kg;
        $this->is_active           = (bool) $branch->is_active;
        $this->receipt_footer      = $branch->receipt_footer ?? '';
        $this->printer_type        = $branch->printer_type ?? 'SYSTEM';
        $this->printer_paper_size  = $branch->printer_paper_size ?? '58mm';
        $this->printer_auto_print  = (bool) ($branch->printer_auto_print ?? false);
        $this->printer_device_name = $branch->printer_device_name ?? '';
        $this->wa_template_ready   = $branch->wa_template_ready  ?? $this->defaultWaReady();
        $this->wa_template_done    = $branch->wa_template_done   ?? $this->defaultWaDone();
        $this->wa_template_promo   = $branch->wa_template_promo  ?? $this->defaultWaPromo();
    }

    #[Computed]
    public function branch(): ?Branch
    {
        return Branch::find(session('active_branch_id', 1));
    }

    // ── Computed: WA message previews with variable substitution ───

    #[Computed]
    public function previewReady(): string
    {
        return $this->substituteVars($this->wa_template_ready);
    }

    #[Computed]
    public function previewDone(): string
    {
        return $this->substituteVars($this->wa_template_done);
    }

    #[Computed]
    public function previewPromo(): string
    {
        return $this->substituteVars($this->wa_template_promo);
    }

    private function substituteVars(string $template): string
    {
        return str_replace(
            ['{nama}', '{invoice}', '{rak}', '{toko}'],
            [$this->previewName, $this->previewInvoice, $this->previewRak, $this->name ?: 'Kee Laundry'],
            $template
        );
    }

    // ── Save: Profile ──────────────────────────────────────────────

    public function saveProfile(): void
    {
        $this->validate([
            'name'               => ['required', 'string', 'min:3', 'max:100'],
            'phone'              => ['required', 'string', 'min:8', 'max:20'],
            'address'            => ['required', 'string', 'min:10'],
            'target_hpp_per_kg'  => ['required', 'integer', 'min:100'],
        ], [], [
            'target_hpp_per_kg'  => 'Target HPP/Kg',
        ]);

        DB::transaction(function () {
            Branch::where('id', session('active_branch_id', 1))->update([
                'name'               => trim($this->name),
                'phone'              => trim($this->phone),
                'address'            => trim($this->address),
                'operational_hours'  => trim($this->operational_hours) ?: null,
                'target_hpp_per_kg'  => $this->target_hpp_per_kg,
                'is_active'          => $this->is_active,
            ]);
        });

        unset($this->branch);
        $this->dispatch('notify', ['type' => 'success', 'title' => 'Profil Tersimpan', 'message' => 'Data profil toko berhasil diperbarui.']);
    }

    // ── Save: Receipt ──────────────────────────────────────────────

    public function saveReceipt(): void
    {
        $this->validate([
            'receipt_footer' => ['nullable', 'string', 'max:300'],
        ]);

        Branch::where('id', session('active_branch_id', 1))->update([
            'receipt_footer' => trim($this->receipt_footer) ?: null,
        ]);

        unset($this->branch);
        $this->dispatch('notify', ['type' => 'success', 'title' => 'Format Struk Tersimpan', 'message' => 'Footer struk thermal berhasil diperbarui.']);
    }

    // ── Save: Printer Thermal ──────────────────────────────────────

    public function savePrinter(): void
    {
        $this->validate([
            'printer_type'       => ['required', 'in:BLUETOOTH,USB,SYSTEM'],
            'printer_paper_size' => ['required', 'in:58mm,80mm'],
            'printer_auto_print' => ['boolean'],
        ]);

        Branch::where('id', session('active_branch_id', 1))->update([
            'printer_type'        => $this->printer_type,
            'printer_paper_size'  => $this->printer_paper_size,
            'printer_auto_print'  => $this->printer_auto_print,
            'printer_device_name' => trim($this->printer_device_name) ?: null,
        ]);

        unset($this->branch);
        $this->dispatch('notify', [
            'type'    => 'success',
            'title'   => 'Pengaturan Printer Disimpan',
            'message' => 'Konfigurasi printer thermal telah diperbarui.',
        ]);
    }

    public function updatePairedDevice(string $type, string $deviceName): void
    {
        $this->printer_type        = $type;
        $this->printer_device_name = $deviceName;
        $this->savePrinter();
    }

    // ── Save: WhatsApp Templates ───────────────────────────────────

    public function saveWhatsApp(): void
    {
        $this->validate([
            'wa_template_ready' => ['required', 'string', 'min:10', 'max:500'],
            'wa_template_done'  => ['required', 'string', 'min:10', 'max:500'],
            'wa_template_promo' => ['required', 'string', 'min:10', 'max:500'],
        ], [], [
            'wa_template_ready' => 'Template Siap Diambil',
            'wa_template_done'  => 'Template Sudah Diambil',
            'wa_template_promo' => 'Template Promo Churn',
        ]);

        Branch::where('id', session('active_branch_id', 1))->update([
            'wa_template_ready' => trim($this->wa_template_ready),
            'wa_template_done'  => trim($this->wa_template_done),
            'wa_template_promo' => trim($this->wa_template_promo),
        ]);

        unset($this->branch, $this->previewReady, $this->previewDone, $this->previewPromo);
        $this->dispatch('notify', ['type' => 'success', 'title' => 'Template WA Tersimpan', 'message' => 'Template pesan WhatsApp berhasil diperbarui.']);
    }

    public function resetToDefault(string $type): void
    {
        match ($type) {
            'ready' => $this->wa_template_ready = $this->defaultWaReady(),
            'done'  => $this->wa_template_done  = $this->defaultWaDone(),
            'promo' => $this->wa_template_promo = $this->defaultWaPromo(),
        };
        unset($this->previewReady, $this->previewDone, $this->previewPromo);
    }

    private function defaultWaReady(): string
    {
        return 'Halo {nama}! Cucian Anda dengan nota *{invoice}* sudah selesai dan siap diambil 🧺 Lokasi rak: *{rak}*. Terima kasih sudah mempercayakan cucian ke {toko}!';
    }

    private function defaultWaDone(): string
    {
        return 'Halo {nama}! Terima kasih sudah mengambil cucian Anda di {toko} 😊 Semoga pakaiannya bersih, wangi, dan nyaman ya! Sampai jumpa lagi!';
    }

    private function defaultWaPromo(): string
    {
        return 'Halo {nama}! {toko} kangen cucian Anda 😊 Sudah lama tidak cuci? Yuk, ada promo spesial bulan ini khusus pelanggan setia. Hubungi kami sekarang!';
    }

    public function render()
    {
        return view('livewire.settings.branch-settings')
            ->layout('layouts.app', ['pageTitle' => 'Pengaturan Toko']);
    }
}
