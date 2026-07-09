@props([
    'title',
    'subtitle' => null,
    'breadcrumb' => null,
])

<div class="flex flex-col gap-1.5">
    @if($breadcrumb)
        <p class="text-[11px] font-semibold uppercase tracking-[0.28em] text-slate-400">
            {{ $breadcrumb }}
        </p>
    @endif

    <div class="space-y-1">
        <h2 class="text-xl font-semibold tracking-tight text-slate-900 sm:text-2xl">
            {{ $title }}
        </h2>

        @if($subtitle)
            <p class="text-sm text-slate-500">
                {{ $subtitle }}
            </p>
        @endif
    </div>
</div>