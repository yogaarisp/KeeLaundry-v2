<div class="sm:mx-auto sm:w-full sm:max-w-md px-4">
    {{-- Brand Header --}}
    <div class="text-center mb-6">
        <div class="inline-flex items-center justify-center size-14 rounded-2xl bg-sky-600 text-white shadow-lg shadow-sky-600/30 mb-3">
            <svg class="size-8" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                <path stroke-linecap="round" stroke-linejoin="round" d="M9.5 9.5a3.5 3.5 0 1 0 4.95 4.95"/>
                <path stroke-linecap="round" stroke-linejoin="round" d="M7 6h.01M7 9h.01"/>
            </svg>
        </div>
        <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight">Kee Laundry ERP</h2>
        <p class="text-xs sm:text-sm text-slate-500 mt-1">Masuk untuk mengelola operasional laundry</p>
    </div>

    {{-- Main Login Card --}}
    <div class="bg-white py-6 px-6 sm:px-8 shadow-xl shadow-slate-200/60 rounded-3xl border border-slate-200/80 space-y-5">
        @if ($errorMessage)
            <div class="p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs font-semibold flex items-center gap-2">
                <svg class="size-4 shrink-0 text-rose-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z"/></svg>
                <span>{{ $errorMessage }}</span>
            </div>
        @endif

        {{-- Form Login Manual --}}
        <form wire:submit="login" class="space-y-4">
            <div>
                <label class="form-label">Email</label>
                <input type="email"
                       wire:model="email"
                       placeholder="nama@keelaundry.id"
                       class="form-input text-sm">
                @error('email') <p class="form-error">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="form-label">Kata Sandi (Password)</label>
                <input type="password"
                       wire:model="password"
                       placeholder="••••••••"
                       class="form-input text-sm">
                @error('password') <p class="form-error">{{ $message }}</p> @enderror
            </div>

            <div class="flex items-center justify-between text-xs">
                <label class="flex items-center gap-2 cursor-pointer text-slate-600">
                    <input type="checkbox" wire:model="remember" class="rounded border-slate-300 text-sky-600 focus:ring-sky-500">
                    <span>Ingat Saya</span>
                </label>
                <span class="text-slate-400">Password default: <code class="font-mono text-sky-700 font-bold">password</code></span>
            </div>

            <button type="submit"
                    wire:loading.attr="disabled"
                    class="btn-primary w-full justify-center py-2.5 text-sm font-bold shadow-md shadow-sky-600/20">
                <span wire:loading.remove wire:target="login">Masuk ke Sistem</span>
                <span wire:loading wire:target="login">Memproses...</span>
            </button>
        </form>

        {{-- Divider --}}
        <div class="relative my-4">
            <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-slate-200"></div></div>
            <div class="relative flex justify-center text-xs uppercase"><span class="bg-white px-2 text-slate-400 font-semibold tracking-wider">Akses Cepat Demo per Role</span></div>
        </div>

        {{-- 1-Click Role Accounts Grid --}}
        <div class="space-y-2">
            {{-- Admin --}}
            <button type="button"
                    wire:click="quickLogin('admin@keelaundry.id')"
                    class="w-full flex items-center justify-between p-3 rounded-2xl border border-indigo-100 bg-indigo-50/50 hover:bg-indigo-100/70 transition-all text-left group">
                <div class="flex items-center gap-3">
                    <div class="size-9 rounded-xl bg-indigo-600 text-white flex items-center justify-center text-base shrink-0 shadow-sm">
                        👑
                    </div>
                    <div>
                        <p class="text-xs font-bold text-slate-800 leading-tight group-hover:text-indigo-700">Owner / Admin</p>
                        <p class="text-[11px] text-slate-500">admin@keelaundry.id</p>
                    </div>
                </div>
                <span class="text-[11px] font-bold text-indigo-700 px-2.5 py-1 rounded-lg bg-white border border-indigo-200 group-hover:bg-indigo-600 group-hover:text-white transition-colors">
                    Login ➔
                </span>
            </button>

            {{-- Kasir --}}
            <button type="button"
                    wire:click="quickLogin('kasir@keelaundry.id')"
                    class="w-full flex items-center justify-between p-3 rounded-2xl border border-emerald-100 bg-emerald-50/50 hover:bg-emerald-100/70 transition-all text-left group">
                <div class="flex items-center gap-3">
                    <div class="size-9 rounded-xl bg-emerald-600 text-white flex items-center justify-center text-base shrink-0 shadow-sm">
                        💰
                    </div>
                    <div>
                        <p class="text-xs font-bold text-slate-800 leading-tight group-hover:text-emerald-700">Kasir POS (Siti Rahayu)</p>
                        <p class="text-[11px] text-slate-500">kasir@keelaundry.id</p>
                    </div>
                </div>
                <span class="text-[11px] font-bold text-emerald-700 px-2.5 py-1 rounded-lg bg-white border border-emerald-200 group-hover:bg-emerald-600 group-hover:text-white transition-colors">
                    Login ➔
                </span>
            </button>

            {{-- Produksi --}}
            <button type="button"
                    wire:click="quickLogin('produksi@keelaundry.id')"
                    class="w-full flex items-center justify-between p-3 rounded-2xl border border-amber-100 bg-amber-50/50 hover:bg-amber-100/70 transition-all text-left group">
                <div class="flex items-center gap-3">
                    <div class="size-9 rounded-xl bg-amber-600 text-white flex items-center justify-center text-base shrink-0 shadow-sm">
                        ⚙️
                    </div>
                    <div>
                        <p class="text-xs font-bold text-slate-800 leading-tight group-hover:text-amber-700">Tim Produksi (Budi Santoso)</p>
                        <p class="text-[11px] text-slate-500">produksi@keelaundry.id</p>
                    </div>
                </div>
                <span class="text-[11px] font-bold text-amber-700 px-2.5 py-1 rounded-lg bg-white border border-amber-200 group-hover:bg-amber-600 group-hover:text-white transition-colors">
                    Login ➔
                </span>
            </button>

            {{-- Kurir --}}
            <button type="button"
                    wire:click="quickLogin('kurir@keelaundry.id')"
                    class="w-full flex items-center justify-between p-3 rounded-2xl border border-sky-100 bg-sky-50/50 hover:bg-sky-100/70 transition-all text-left group">
                <div class="flex items-center gap-3">
                    <div class="size-9 rounded-xl bg-sky-600 text-white flex items-center justify-center text-base shrink-0 shadow-sm">
                        🛵
                    </div>
                    <div>
                        <p class="text-xs font-bold text-slate-800 leading-tight group-hover:text-sky-700">Kurir Delivery (Rina Handayani)</p>
                        <p class="text-[11px] text-slate-500">kurir@keelaundry.id</p>
                    </div>
                </div>
                <span class="text-[11px] font-bold text-sky-700 px-2.5 py-1 rounded-lg bg-white border border-sky-200 group-hover:bg-sky-600 group-hover:text-white transition-colors">
                    Login ➔
                </span>
            </button>
        </div>
    </div>

    {{-- Footer link to tracking --}}
    <div class="text-center mt-6">
        <a href="/cek" class="text-xs text-sky-600 hover:text-sky-700 font-semibold hover:underline">
            🔍 Pelanggan ingin cek status cucian? Klik di sini
        </a>
    </div>
</div>
