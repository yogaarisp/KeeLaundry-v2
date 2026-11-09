<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Lacak Cucian – Kee Laundry' }}</title>
    <meta name="description" content="Lacak status cucian Anda di Kee Laundry secara real-time tanpa perlu login.">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="min-h-full bg-gradient-to-br from-sky-50 via-white to-slate-50"
      x-data="{ showToast: false, toastMsg: '' }">

    {{ $slot }}

    {{-- Toast --}}
    <div class="fixed top-4 right-4 z-50 pointer-events-none" id="pub-toast-container"
         x-data="toastManager()" @toast.window="addToast($event.detail)">
        <template x-for="t in toasts" :key="t.id">
            <div x-show="t.visible"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-2 scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                 class="toast pointer-events-auto mb-2">
                <div class="shrink-0" :class="{ 'text-emerald-500': t.type==='success', 'text-rose-500': t.type==='error' }">
                    <svg class="size-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <template x-if="t.type === 'success'"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></template>
                        <template x-if="t.type !== 'success'"><path stroke-linecap="round" stroke-linejoin="round" d="m9.75 9.75 4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></template>
                    </svg>
                </div>
                <div class="flex-1"><p class="text-sm font-semibold text-slate-800" x-text="t.title"></p><p class="text-xs text-slate-500" x-text="t.message" x-show="t.message"></p></div>
            </div>
        </template>
    </div>

    @stack('scripts')
    @livewireScripts

    <script>
    function toastManager() {
        return {
            toasts: [], nextId: 0,
            addToast({ type='info', title='', message='', duration=4000 }) {
                const id = ++this.nextId;
                this.toasts.push({ id, type, title, message, visible: true });
                setTimeout(() => { const t = this.toasts.find(t => t.id === id); if (t) t.visible = false; setTimeout(() => { this.toasts = this.toasts.filter(t => t.id !== id); }, 300); }, duration);
            },
        };
    }
    document.addEventListener('livewire:initialized', () => {
        Livewire.on('notify', (data) => window.dispatchEvent(new CustomEvent('toast', { detail: data })));
    });
    </script>
</body>
</html>
