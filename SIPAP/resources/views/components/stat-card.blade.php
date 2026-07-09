@props([
    'title',
    'value',
    'description' => null,
])

<article {{ $attributes->merge(['class' => 'group h-[160px] rounded-[28px] border border-slate-200/80 bg-white p-6 shadow-[0_16px_40px_rgba(15,23,42,0.06)] transition-all duration-300 hover:-translate-y-1 hover:shadow-[0_22px_55px_rgba(15,23,42,0.12)]']) }}>
    <div class="flex h-full flex-col justify-between">
        <div class="flex items-start justify-between gap-4">
            <div class="min-w-0">
                <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-400">
                    {{ $title }}
                </p>

                <div class="mt-3 text-4xl font-black tracking-tight text-slate-900">
                    {{ $value }}
                </div>
            </div>

            <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-[#081B4B] via-[#1C3E8A] to-[#7C3AED] text-white shadow-[0_18px_30px_rgba(49,46,129,0.25)] ring-1 ring-white/20">
                {{ $icon ?? '' }}
            </div>
        </div>

        @if($description)
            <p class="text-sm leading-6 text-slate-500">
                {{ $description }}
            </p>
        @endif
    </div>
</article>