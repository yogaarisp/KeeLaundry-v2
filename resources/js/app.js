import './bootstrap';
import Chart from 'chart.js/auto';
import * as ThermalPrinter from './thermal-printer';

// Expose Chart globally so Alpine.js and Blade components can use it
window.Chart = Chart;

// Expose ThermalPrinter globally so Alpine.js thermalPrinterManager() can use it
window.ThermalPrinter = ThermalPrinter;

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
