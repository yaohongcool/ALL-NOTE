@props([
    'title',
    'value' => '0',
    'hint' => '',
    'createUrl' => '#',
    'manageUrl' => '#',
])

<div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
    <div class="flex items-start justify-between gap-4">
        <div class="min-w-0">
            <p class="text-sm font-medium text-slate-500 dark:text-slate-400">{{ $title }}</p>
            <p class="mt-3 text-3xl font-bold tracking-tight text-slate-900 dark:text-slate-100">
                {{ $value }}
            </p>

            @if($hint)
                <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">
                    {{ $hint }}
                </p>
            @endif
        </div>

        <div class="flex shrink-0 flex-col items-end gap-3">
            <a
                href="{{ $createUrl }}"
                class="inline-flex items-center justify-center rounded-xl bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700"
            >
                添加
            </a>

            <a
                href="{{ $manageUrl }}"
                class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200 dark:hover:bg-slate-800"
            >
                管理
            </a>
        </div>
    </div>
</div>