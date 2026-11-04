import './bootstrap';
import Chart from 'chart.js/auto';
import thermalPrinter from './thermal-printer';

// Expose Chart globally
window.Chart = Chart;

// Expose printer singleton globally — dipakai oleh Alpine.js di blade
window.thermalPrinter = thermalPrinter;

// Listen status perubahan koneksi agar bisa dipakai secara global
thermalPrinter.addEventListener('connection-changed', (e) => {
    window.dispatchEvent(new CustomEvent('printer-status-changed', { detail: e.detail }));
});

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
