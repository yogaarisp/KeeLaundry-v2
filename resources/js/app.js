import './bootstrap';

// Alpine.js is bundled with Livewire 4 — no separate import needed.
// Chart.js is loaded via CDN in the dashboard view (wire:ignore block).

// ── Global utilities ───────────────────────────────────────────

/**
 * Format number as Indonesian Rupiah.
 * Usage: formatRp(35000) → "Rp 35.000"
 */
window.formatRp = (value) =>
    'Rp ' + new Intl.NumberFormat('id-ID').format(Math.round(value));

/**
 * Dispatch a toast notification from any JS context.
 * Usage: showToast({ type: 'success', title: 'Berhasil', message: 'Data tersimpan' })
 */
window.showToast = ({ type = 'info', title = '', message = '', duration = 4000 } = {}) => {
    window.dispatchEvent(new CustomEvent('toast', { detail: { type, title, message, duration } }));
};
