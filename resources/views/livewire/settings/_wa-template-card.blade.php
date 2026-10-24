{{-- WA Template Card partial --}}
{{-- Props: $icon, $title, $desc, $model, $preview, $resetType --}}
<div class="card p-5 sm:p-6 space-y-4 shadow-sm border border-slate-200/80">
    <div class="flex items-start justify-between gap-3 border-b border-slate-100 pb-3">
        <div class="flex items-start gap-3">
            <span class="text-2xl leading-none mt-0.5">{{ $icon }}</span>
            <div>
                <p class="text-sm font-bold text-slate-900">{{ $title }}</p>
                <p class="text-xs text-slate-500 mt-0.5">{{ $desc }}</p>
            </div>
        </div>
        <button wire:click="resetToDefault('{{ $resetType }}')"
                class="btn-secondary text-xs py-1.5 px-3 shrink-0 text-amber-700 hover:text-amber-800 bg-amber-50 hover:bg-amber-100 border-amber-200/80 font-semibold transition-colors">
            Reset Default
        </button>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 items-start">
        {{-- Editor (lg:col-span-7) --}}
        <div class="lg:col-span-7 space-y-1.5">
            <div class="flex items-center justify-between">
                <label class="form-label text-xs font-bold text-slate-700">Teks Template Pesan</label>
                <span class="text-[10px] text-slate-400">Dukungan format teks *tebal*, _miring_</span>
            </div>
            <textarea wire:model.live="{{ $model }}" rows="5"
                      class="form-input resize-none text-xs sm:text-sm font-sans leading-relaxed"
                      placeholder="Tulis format pesan WhatsApp di sini…"></textarea>
            @error($model) <p class="form-error">{{ $message }}</p> @enderror
        </div>

        {{-- Simulated WhatsApp Bubble (lg:col-span-5) --}}
        <div class="lg:col-span-5 space-y-1.5">
            <div class="flex items-center justify-between">
                <label class="form-label text-xs font-bold text-slate-700">Pratinjau Balon Chat</label>
                <span class="text-[10px] font-mono text-emerald-600 font-semibold">{{ strlen($preview) }} karakter</span>
            </div>
            <div class="rounded-2xl bg-emerald-50/60 border border-emerald-100 p-3.5 min-h-[120px] flex flex-col justify-center">
                <div class="flex items-start gap-2.5">
                    <div class="size-8 rounded-full bg-emerald-600 text-white flex items-center justify-center shrink-0 shadow-2xs">
                        <svg class="size-4 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
                    </div>
                    <div class="flex-1 bg-white rounded-2xl rounded-tl-none p-3 shadow-sm border border-emerald-100/80 text-xs text-slate-800 leading-relaxed whitespace-pre-wrap">
                        {{ $preview ?: '…' }}
                        <div class="flex items-center justify-end gap-1 mt-1 text-[10px] text-slate-400">
                            <span>{{ now()->format('H:i') }}</span>
                            <span class="text-sky-500 font-bold">✓✓</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
