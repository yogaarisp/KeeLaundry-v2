<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? config('app.name', 'Kee Laundry') }}</title>

    @php
        $activeBranch = \App\Models\Branch::find(session('active_branch_id', 1)) ?? \App\Models\Branch::first();
        $brandName = $activeBranch?->name ?? 'Kee Laundry';
        $brandLogo = $activeBranch?->logo_url;
        $brandFavicon = $activeBranch?->favicon_url;
    @endphp

    @if($brandFavicon)
        <link rel="icon" href="{{ $brandFavicon }}">
        <link rel="apple-touch-icon" href="{{ $brandFavicon }}">
    @else
        <link rel="icon" href="/favicon.ico">
    @endif

    {{-- Google Font: Plus Jakarta Sans --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    {{-- Leaflet Map library for interactive map picking --}}
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
</head>
<body class="h-full bg-slate-50" x-data="appShell()" @keydown.escape.window="closeAllModals()">

{{-- ══════════════════════════════════════════════════════════════
{{-- ══════════════════════════════════════════════════════════════
     RESPONSIVE APP LAYOUT (Unified Livewire Single DOM)
══════════════════════════════════════════════════════════════════ --}}

{{-- ── Sidebar (Desktop / Tablet only) ───────────────────────── --}}
<aside
    class="hidden md:flex fixed inset-y-0 left-0 z-30 flex-col bg-white border-r border-slate-200/80 shadow-sm transition-[width] duration-300"
    :class="sidebarOpen ? 'w-64' : 'w-[72px]'"
>
    {{-- Logo --}}
    <div class="flex items-center gap-3 px-4 h-16 border-b border-slate-200/80 shrink-0">
        @if($brandLogo)
            <div class="shrink-0 flex items-center justify-center size-9 rounded-xl bg-white border border-slate-200/80 p-0.5 shadow-sm overflow-hidden">
                <img src="{{ $brandLogo }}" alt="{{ $brandName }}" class="size-full object-contain rounded-lg">
            </div>
        @else
            <div class="shrink-0 flex items-center justify-center size-9 rounded-xl bg-sky-600 text-white shadow-sm">
                <svg class="size-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.5 9.5a3.5 3.5 0 1 0 4.95 4.95"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 6h.01M7 9h.01"/>
                </svg>
            </div>
        @endif
        <div x-show="sidebarOpen" x-transition:enter="transition-opacity duration-150" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" class="overflow-hidden">
            <p class="text-sm font-bold text-slate-800 leading-tight whitespace-nowrap truncate max-w-[140px]">{{ $brandName }}</p>
            <p class="text-[11px] text-sky-600 font-semibold whitespace-nowrap truncate">Outlet Utama</p>
        </div>
    </div>

    {{-- Nav --}}
    <nav class="flex-1 overflow-y-auto py-3 px-2 space-y-0.5 scrollbar-thin">
        @php
            $activeRole = session('active_role', 'ADMIN');
            $allNav = [
                ['route' => '/',            'label' => 'Dashboard',     'icon' => 'chart-bar',          'roles' => ['ADMIN']],
                ['route' => '/pos',          'label' => 'Kasir POS',     'icon' => 'calculator',         'roles' => ['ADMIN', 'KASIR']],
                ['route' => '/antar-jemput', 'label' => 'Antar Jemput',  'icon' => 'truck',              'roles' => ['ADMIN', 'KASIR', 'KURIR']],
                ['route' => '/produksi',     'label' => 'Produksi',      'icon' => 'queue-list',         'roles' => ['ADMIN', 'PRODUKSI']],
                ['route' => '/pelanggan',    'label' => 'CRM Pelanggan', 'icon' => 'users',              'roles' => ['ADMIN', 'KASIR']],
                ['route' => '/karyawan',     'label' => 'HR & Payroll',  'icon' => 'identification',     'roles' => ['ADMIN']],
                ['route' => '/inventaris',   'label' => 'Inventaris',    'icon' => 'beaker',             'roles' => ['ADMIN', 'PRODUKSI']],
                ['route' => '/layanan',      'label' => 'Master Layanan','icon' => 'wrench-screwdriver','roles' => ['ADMIN']],
                ['route' => '/pengaturan',   'label' => 'Pengaturan',    'icon' => 'cog-6-tooth',        'roles' => ['ADMIN']],
            ];
            $navItems = array_filter($allNav, fn($item) => in_array($activeRole, $item['roles']));
        @endphp

        @foreach ($navItems as $item)
            @php
                $isActive = $item['route'] === '/'
                    ? request()->is('/')
                    : request()->is(ltrim($item['route'], '/') . '*');
            @endphp
            <a href="{{ $item['route'] }}"
               wire:navigate
               @class(['nav-link', 'active' => $isActive])
               :title="!sidebarOpen ? '{{ $item['label'] }}' : ''"
            >
                <x-icon name="{{ $item['icon'] }}" class="nav-icon shrink-0"/>
                <span x-show="sidebarOpen" class="whitespace-nowrap text-sm transition-opacity duration-150">
                    {{ $item['label'] }}
                </span>
            </a>
        @endforeach
    </nav>

    {{-- Branch badge --}}
    <div x-show="sidebarOpen" class="px-3 py-3 border-t border-slate-200/80">
        <div class="flex items-center gap-2 rounded-xl bg-sky-50 px-3 py-2.5 border border-sky-100">
            <span class="size-2 rounded-full bg-emerald-400 shrink-0 animate-pulse"></span>
            <p class="text-xs font-semibold text-sky-700 truncate">Kee Laundry – Pusat</p>
        </div>
    </div>

    {{-- Collapse toggle --}}
    <button @click="sidebarOpen = !sidebarOpen"
        class="flex items-center justify-center h-11 border-t border-slate-200/80 text-slate-400 hover:text-sky-600 transition-colors">
        <svg class="size-4 transition-transform duration-300" :class="sidebarOpen ? 'rotate-0' : 'rotate-180'"
             fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
        </svg>
    </button>
</aside>

{{-- ── Main Area (responsive margin on desktop, full width on mobile) ── --}}
<div class="flex flex-1 flex-col min-h-[100dvh] transition-[margin] duration-300" :class="sidebarOpen ? 'md:ml-64' : 'md:ml-[72px]'">

    {{-- ── Desktop Top Header (hidden on mobile) ──────────────── --}}
    <header class="hidden md:flex sticky top-0 z-20 items-center h-16 px-6 bg-white/90 backdrop-blur-md border-b border-slate-200/80 gap-4">
        {{-- Page title slot --}}
        <h1 class="text-base font-bold text-slate-800 flex-1 truncate">{{ $pageTitle ?? 'Dashboard' }}</h1>

        {{-- Role Switcher Dropdown --}}
        <div class="relative" x-data="{ openRole: false }">
            <button @click="openRole = !openRole" @click.outside="openRole = false"
                    class="flex items-center gap-2 px-3 py-1.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-xs font-semibold shadow-sm transition-colors">
                <span class="size-2 rounded-full {{ match($activeRole) { 'ADMIN' => 'bg-indigo-500', 'KASIR' => 'bg-emerald-500', 'PRODUKSI' => 'bg-amber-500', 'KURIR' => 'bg-sky-500', default => 'bg-indigo-500' } }}"></span>
                <span class="text-slate-500">Mode:</span>
                <span class="font-bold text-slate-800">{{ $activeRole }}</span>
                <svg class="size-3 text-slate-400 transition-transform duration-150" :class="openRole ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5"/></svg>
            </button>
            <div x-show="openRole" x-cloak class="absolute right-0 mt-2 w-52 rounded-2xl bg-white shadow-2xl border border-slate-200/80 py-1.5 z-50">
                <div class="px-3.5 py-1.5 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Ganti Role / Persona</div>
                @foreach ([
                    'ADMIN'    => ['Owner / Admin', '👑', 'Akses semua modul toko'],
                    'KASIR'    => ['Kasir POS',     '💰', 'POS, CRM & Antar Jemput'],
                    'PRODUKSI' => ['Tim Produksi',  '⚙️', 'Kanban Cuci, Setrika & Rak'],
                    'KURIR'    => ['Kurir Delivery','🛵', 'Jemput & Antar Cucian']
                ] as $rKey => [$rName, $rIcon, $rDesc])
                    <a href="/switch-role/{{ $rKey }}"
                       class="flex items-center gap-2.5 px-3.5 py-2 text-xs hover:bg-slate-50 transition-colors {{ $activeRole === $rKey ? 'bg-sky-50 text-sky-700 font-bold' : 'text-slate-700' }}">
                        <span class="text-base">{{ $rIcon }}</span>
                        <div class="min-w-0">
                            <p class="leading-snug truncate">{{ $rName }}</p>
                            <p class="text-[10px] text-slate-400 font-normal leading-tight mt-0.5 truncate">{{ $rDesc }}</p>
                        </div>
                    </a>
                @endforeach

                <div class="border-t border-slate-100 mt-1 pt-1">
                    <a href="/login" class="flex items-center gap-2 px-3.5 py-2 text-xs text-slate-600 hover:bg-slate-50 transition-colors">
                        <span>🔐</span>
                        <span>Halaman Login</span>
                    </a>
                    <a href="/logout" class="flex items-center gap-2 px-3.5 py-2 text-xs text-rose-600 hover:bg-rose-50 transition-colors font-medium">
                        <span>🚪</span>
                        <span>Keluar (Logout)</span>
                    </a>
                </div>
            </div>
        </div>

        {{-- Global search --}}
        <div class="relative w-60 hidden lg:block">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 size-4 text-slate-400"
                 fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/>
            </svg>
            <input type="search" placeholder="Cari nota, pelanggan…"
                class="w-full pl-9 pr-3 py-2 text-sm rounded-xl bg-slate-100 border-transparent
                       focus:bg-white focus:border-sky-300 focus:ring-2 focus:ring-sky-100 outline-none transition-all">
        </div>

        {{-- Quick add --}}
        <a href="/pos" wire:navigate class="btn-primary hidden sm:inline-flex">
            <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
            </svg>
            Transaksi Baru
        </a>

        {{-- Notification bell --}}
        <button class="btn-icon relative" @click="notifOpen = !notifOpen">
            <svg class="size-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0"/>
            </svg>
            <span id="notif-badge" class="absolute -top-0.5 -right-0.5 hidden size-2 rounded-full bg-rose-500 ring-2 ring-white"></span>
        </button>
    </header>

    {{-- ── Mobile Sticky Top Header (hidden on desktop) ────────── --}}
    <header class="flex md:hidden sticky top-0 z-30 items-center h-14 px-4 bg-white/90 backdrop-blur-md border-b border-slate-200/80 gap-2">
        {{-- Logo + branch --}}
        <div class="flex items-center gap-2 flex-1 min-w-0">
            @if($brandLogo)
                <div class="flex items-center justify-center size-8 rounded-xl bg-white border border-slate-200/80 p-0.5 shadow-sm overflow-hidden shrink-0">
                    <img src="{{ $brandLogo }}" alt="{{ $brandName }}" class="size-full object-contain rounded-lg">
                </div>
            @else
                <div class="flex items-center justify-center size-8 rounded-xl bg-sky-600 text-white shrink-0">
                    <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.5 9.5a3.5 3.5 0 1 0 4.95 4.95"/>
                    </svg>
                </div>
            @endif
            <div class="min-w-0">
                <p class="text-sm font-bold text-slate-800 leading-tight truncate">{{ $brandName }}</p>
                <span class="flex items-center gap-0.5 text-[11px] text-sky-600 font-semibold truncate">
                    Outlet Utama
                </span>
            </div>
        </div>

        {{-- Mobile Role Switcher Pill --}}
        <div class="relative" x-data="{ openRoleMobile: false }">
            <button @click="openRoleMobile = !openRoleMobile" @click.outside="openRoleMobile = false"
                    class="flex items-center gap-1 px-2 py-1 rounded-lg border border-slate-200 bg-white text-[11px] font-bold text-slate-700 shadow-sm">
                <span class="size-2 rounded-full {{ match($activeRole) { 'ADMIN' => 'bg-indigo-500', 'KASIR' => 'bg-emerald-500', 'PRODUKSI' => 'bg-amber-500', 'KURIR' => 'bg-sky-500', default => 'bg-indigo-500' } }}"></span>
                <span>{{ $activeRole }}</span>
            </button>
            <div x-show="openRoleMobile" x-cloak class="absolute right-0 mt-2 w-48 rounded-xl bg-white shadow-2xl border border-slate-200/80 py-1 z-50">
                <div class="px-3 py-1 text-[10px] font-bold text-slate-400 uppercase">Role Mode</div>
                @foreach (['ADMIN' => '👑 Owner', 'KASIR' => '💰 Kasir', 'PRODUKSI' => '⚙️ Produksi', 'KURIR' => '🛵 Kurir'] as $rKey => $rLabel)
                    <a href="/switch-role/{{ $rKey }}" class="block px-3 py-1.5 text-xs {{ $activeRole === $rKey ? 'font-bold text-sky-600 bg-sky-50' : 'text-slate-700' }}">
                        {{ $rLabel }}
                    </a>
                @endforeach
            </div>
        </div>

        {{-- More menu --}}
        <button class="btn-icon" @click="mobileMenuOpen = true">
            <svg class="size-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/>
            </svg>
        </button>
    </header>

    {{-- ── Single Page Content (Rendered ONCE!) ───────────────── --}}
    <main class="flex-1 overflow-y-auto pb-nav md:pb-6 p-4 lg:p-6">
        {{ $slot }}
    </main>

    {{-- ── Fixed Bottom Navigation Bar (Mobile only) ───────────── --}}
    <nav class="md:hidden fixed bottom-0 inset-x-0 z-40 h-16 bg-white/90 backdrop-blur-md border-t border-slate-200/80 flex items-center justify-around pb-safe" style="padding-bottom: env(safe-area-inset-bottom, 0px)">

        @if ($activeRole === 'KURIR')
            {{-- Kurir: Antar Jemput (Primary) --}}
            <a href="/antar-jemput" wire:navigate @class(['bottom-tab', 'active' => request()->is('antar-jemput*')])>
                <x-icon name="truck" class="size-5" />
                <span>Tugas Kirim</span>
            </a>
            <a href="/cek" wire:navigate @class(['bottom-tab', 'active' => request()->is('cek*')])>
                <x-icon name="document-text" class="size-5" />
                <span>Cek Resi</span>
            </a>
            <a href="/pelanggan" wire:navigate @class(['bottom-tab', 'active' => request()->is('pelanggan*')])>
                <x-icon name="users" class="size-5" />
                <span>Pelanggan</span>
            </a>

        @elseif ($activeRole === 'PRODUKSI')
            {{-- Produksi: Kanban Board & Gudang --}}
            <a href="/produksi" wire:navigate @class(['bottom-tab', 'active' => request()->is('produksi*')])>
                <x-icon name="queue-list" class="size-5" />
                <span>Kanban</span>
            </a>
            <a href="/inventaris" wire:navigate @class(['bottom-tab', 'active' => request()->is('inventaris*')])>
                <x-icon name="beaker" class="size-5" />
                <span>Gudang</span>
            </a>
            <a href="/cek" wire:navigate @class(['bottom-tab', 'active' => request()->is('cek*')])>
                <x-icon name="document-text" class="size-5" />
                <span>Cek Cucian</span>
            </a>

        @elseif ($activeRole === 'KASIR')
            {{-- Kasir: Riwayat/Antar-Jemput, Center POS FAB, CRM --}}
            <a href="/antar-jemput" wire:navigate @class(['bottom-tab', 'active' => request()->is('antar-jemput*')])>
                <x-icon name="truck" class="size-5" />
                <span>Antar-Jemput</span>
            </a>

            {{-- FAB Tengah: Kasir --}}
            <div class="bottom-tab px-2">
                <a href="/pos" wire:navigate
                   class="relative -mt-6 flex items-center justify-center size-14 rounded-2xl bg-sky-600 text-white shadow-lg shadow-sky-600/40 hover:bg-sky-700 active:scale-95 transition-all duration-150">
                    <svg class="size-6" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                    </svg>
                </a>
                <span class="text-[10px] text-sky-600 font-semibold mt-1">Kasir</span>
            </div>

            <a href="/pelanggan" wire:navigate @class(['bottom-tab', 'active' => request()->is('pelanggan*')])>
                <x-icon name="users" class="size-5" />
                <span>CRM</span>
            </a>

            <a href="/produksi" wire:navigate @class(['bottom-tab', 'active' => request()->is('produksi*')])>
                <x-icon name="queue-list" class="size-5" />
                <span>Produksi</span>
            </a>

        @else
            {{-- Admin: Full set --}}
            <a href="/" wire:navigate @class(['bottom-tab', 'active' => request()->is('/')])>
                <x-icon name="chart-bar" class="size-5" />
                <span>Beranda</span>
            </a>

            <a href="/antar-jemput" wire:navigate @class(['bottom-tab', 'active' => request()->is('antar-jemput*')])>
                <x-icon name="truck" class="size-5" />
                <span>Antar</span>
            </a>

            {{-- FAB Tengah: Kasir --}}
            <div class="bottom-tab px-2">
                <a href="/pos" wire:navigate
                   class="relative -mt-6 flex items-center justify-center size-14 rounded-2xl bg-sky-600 text-white shadow-lg shadow-sky-600/40 hover:bg-sky-700 active:scale-95 transition-all duration-150">
                    <svg class="size-6" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                    </svg>
                </a>
                <span class="text-[10px] text-sky-600 font-semibold mt-1">Kasir</span>
            </div>

            <a href="/produksi" wire:navigate @class(['bottom-tab', 'active' => request()->is('produksi*')])>
                <x-icon name="queue-list" class="size-5" />
                <span>Produksi</span>
            </a>

            <a href="/pelanggan" wire:navigate @class(['bottom-tab', 'active' => request()->is('pelanggan*')])>
                <x-icon name="users" class="size-5" />
                <span>CRM</span>
            </a>
        @endif
    </nav>
</div>

{{-- ══════════════════════════════════════════════════════════════
     GLOBAL COMPONENTS (visible on all screen sizes)
══════════════════════════════════════════════════════════════════ --}}

{{-- ── Toast Notification Stack (Modern Floating Capsule Pill) ───────────────────────────────── --}}
<div
    id="toast-container"
    aria-live="polite"
    aria-atomic="false"
    class="fixed top-4 left-1/2 -translate-x-1/2 z-[100] flex flex-col items-center gap-2 w-full max-w-md px-4 pointer-events-none"
    x-data="toastManager()"
    @toast.window="addToast($event.detail)"
>
    <template x-for="t in toasts" :key="t.id">
        <div
            x-show="t.visible"
            x-transition:enter="transition ease-out duration-250"
            x-transition:enter-start="opacity-0 -translate-y-3 scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 -translate-y-2 scale-95"
            class="pointer-events-auto flex items-center gap-2.5 bg-slate-900/95 text-white backdrop-blur-md px-4 py-2.5 rounded-full shadow-2xl border border-slate-700/60 text-xs sm:text-sm font-medium transition-all"
        >
            {{-- Status Dot Indicator --}}
            <span class="size-2 rounded-full shrink-0" :class="{
                'bg-emerald-400 ring-2 ring-emerald-400/30': t.type === 'success',
                'bg-rose-400 ring-2 ring-rose-400/30':       t.type === 'error',
                'bg-amber-400 ring-2 ring-amber-400/30':     t.type === 'warning',
                'bg-sky-400 ring-2 ring-sky-400/30':         t.type === 'info',
            }"></span>

            {{-- Text Content --}}
            <div class="flex items-center gap-1.5 min-w-0">
                <span class="font-semibold text-white truncate" x-text="t.title || t.message"></span>
                <span class="text-slate-300 font-normal truncate hidden sm:inline"
                      x-show="t.title && t.message && t.title !== t.message"
                      x-text="'· ' + t.message"></span>
            </div>

            {{-- Close Button --}}
            <button @click="removeToast(t.id)" class="text-slate-400 hover:text-white p-0.5 rounded-full transition-colors shrink-0 ml-1" title="Tutup">
                <svg class="size-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
            </button>
        </div>
    </template>
</div>

{{-- ── Bottom Sheet / Modal: Generic Slot ─────────────────────── --}}
{{-- Individual Livewire components manage their own modals.       --}}
{{-- This overlay is shared via Alpine events.                     --}}

{{-- ── Mobile More Menu Drawer ───────────────────────────────── --}}
<div x-show="mobileMenuOpen" x-cloak>
    {{-- Backdrop --}}
    <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-40 md:hidden" @click="mobileMenuOpen = false"></div>

    {{-- Drawer --}}
    <div class="fixed inset-y-0 right-0 z-50 w-72 bg-white shadow-2xl flex flex-col md:hidden"
         x-show="mobileMenuOpen"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="translate-x-full"
         x-transition:enter-end="translate-x-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="translate-x-0"
         x-transition:leave-end="translate-x-full"
    >
        <div class="flex items-center justify-between px-5 py-4 border-b border-slate-200/80">
            <p class="font-bold text-slate-800">Menu Lainnya</p>
            <button @click="mobileMenuOpen = false" class="btn-icon">
                <svg class="size-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <nav class="flex-1 overflow-y-auto p-4 space-y-1">
            @php $mobileLinks = [
                ['/antar-jemput', 'Antar Jemput Kurir', 'truck'],
                ['/karyawan',   'HR & Payroll',      'identification'],
                ['/inventaris', 'Inventaris',         'beaker'],
                ['/layanan',    'Master Layanan',     'wrench-screwdriver'],
                ['/pengaturan', 'Pengaturan Toko',    'cog-6-tooth'],
                ['/cek',        'Cek Status Cucian',  'document-text'],
            ]; @endphp
            @foreach ($mobileLinks as [$href, $label, $icon])
                <a href="{{ $href }}" wire:navigate @click="mobileMenuOpen = false"
                   class="flex items-center gap-3 px-3 py-3 rounded-xl hover:bg-sky-50 text-slate-600 hover:text-sky-700 transition-colors">
                    <x-icon name="{{ $icon }}" class="size-5 shrink-0 text-slate-400"/>
                    <span class="text-sm font-medium">{{ $label }}</span>
                </a>
            @endforeach

            <div class="border-t border-slate-100 pt-3 mt-2 space-y-1">
                <a href="/login" @click="mobileMenuOpen = false"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-slate-50 text-slate-700 text-xs font-semibold transition-colors">
                    <span>🔐</span>
                    <span>Halaman Login</span>
                </a>
                <a href="/logout" @click="mobileMenuOpen = false"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-rose-50 text-rose-600 text-xs font-semibold transition-colors">
                    <span>🚪</span>
                    <span>Keluar (Logout)</span>
                </a>
            </div>
        </nav>
        <div class="p-4 border-t border-slate-200/80">
            <p class="text-xs text-slate-400 text-center">Kee Laundry ERP v2.0</p>
        </div>
    </div>
</div>

@stack('scripts')
@livewireScripts

<script>
// ── Alpine.js Global App Shell ─────────────────────────────────
function appShell() {
    return {
        sidebarOpen: localStorage.getItem('sidebarOpen') !== 'false',
        mobileMenuOpen: false,
        notifOpen: false,

        init() {
            this.$watch('sidebarOpen', val => localStorage.setItem('sidebarOpen', val));
        },

        closeAllModals() {
            this.mobileMenuOpen = false;
            this.notifOpen = false;
        },
    };
}

// ── Toast Manager (Clean & Resilient) ─────────────────────────
function toastManager() {
    return {
        toasts: [],
        nextId: 0,

        addToast(payload) {
            const data = Array.isArray(payload) ? (payload[0] || {}) : (payload || {});
            const type = data.type || 'info';
            const title = data.title || '';
            const message = data.message || '';
            const duration = data.duration || 3000;

            const id = ++this.nextId;
            this.toasts.push({ id, type, title, message, visible: true });

            // Auto-remove after duration
            setTimeout(() => this.removeToast(id), duration);
        },

        removeToast(id) {
            const t = this.toasts.find(t => t.id === id);
            if (t) t.visible = false;
            setTimeout(() => {
                this.toasts = this.toasts.filter(t => t.id !== id);
            }, 250);
        },
    };
}

// ── Livewire event → Alpine toast bridge ──────────────────────
document.addEventListener('livewire:initialized', () => {
    Livewire.on('notify', (data) => {
        window.dispatchEvent(new CustomEvent('toast', { detail: data }));
    });
});
</script>

</body>
</html>
