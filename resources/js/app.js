import './bootstrap';
import Chart from 'chart.js/auto';
import thermalPrinter from './thermal-printer';
import { printerSetup } from './printer-setup';

// ── Chart ──────────────────────────────────────────────────────────────────
window.Chart = Chart;

// ── Printer singleton (kompatibilitas modul lama) ──────────────────────────
window.thermalPrinter = thermalPrinter;
thermalPrinter.addEventListener('connection-changed', (e) => {
    window.dispatchEvent(new CustomEvent('printer-status-changed', { detail: e.detail }));
});

// ── Daftarkan Alpine.data sebelum Alpine start ─────────────────────────────
// Alpine di-bundle di dalam Livewire — gunakan event alpine:init
document.addEventListener('alpine:init', () => {
    Alpine.data('printerSetup', printerSetup);
});

// ── Global helpers ─────────────────────────────────────────────────────────
window.formatRp = (value) =>
    'Rp ' + new Intl.NumberFormat('id-ID').format(Math.round(value));

window.showToast = ({ type = 'info', title = '', message = '', duration = 4000 } = {}) => {
    window.dispatchEvent(new CustomEvent('toast', { detail: { type, title, message, duration } }));
};
