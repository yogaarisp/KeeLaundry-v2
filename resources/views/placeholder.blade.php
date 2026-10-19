@php $pageTitle = $title ?? 'Halaman'; @endphp

<x-layouts.app :pageTitle="$pageTitle">
<div class="flex flex-col items-center justify-center min-h-[60vh] text-center px-4">
    <div class="flex items-center justify-center size-20 rounded-3xl bg-sky-50 border-2 border-sky-100 text-sky-400 mb-6">
        <x-icon :name="$icon ?? 'chart-bar'" class="size-10"/>
    </div>
    <h2 class="text-xl font-bold text-slate-800 mb-2">{{ $title ?? 'Halaman ini' }}</h2>
    <p class="text-sm text-slate-400 max-w-xs leading-relaxed">
        Modul ini sedang dalam pengembangan. Akan segera hadir dengan fitur CRUD lengkap.
    </p>
    <a href="/" wire:navigate class="btn-primary mt-6">
        <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 15 3 9m0 0 6-6M3 9h12a6 6 0 0 1 0 12h-3"/>
        </svg>
        Kembali ke Dashboard
    </a>
</div>
</x-layouts.app>
