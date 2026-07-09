@props([
    'title',
    'description' => null,
])

<section {{ $attributes->merge(['class' => 'rounded-[28px] border border-slate-200/80 bg-white shadow-[0_18px_50px_rgba(15,23,42,0.06)]']) }}>
    <div class="p-6 sm:p-8">
        <div class="flex flex-col gap-4 border-b border-slate-100 pb-5 sm:flex-row sm:items-end sm:justify-between">
            <div class="max-w-2xl">
                <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-400">
                    Premium Panel
                </p>
                <h3 class="mt-2 text-2xl font-semibold tracking-tight text-slate-900">
                    {{ $title }}
                </h3>

                @if($description)
                    <p class="mt-2 text-sm leading-6 text-slate-500">
                        {{ $description }}
                    </p>
                @endif
            </div>

            @isset($actions)
                <div class="shrink-0">
                    {{ $actions }}
                </div>
            @endisset
        </div>

        <div class="pt-6">
            {{ $slot }}
        </div>
    </div>
</section>