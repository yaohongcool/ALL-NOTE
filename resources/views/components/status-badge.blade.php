@props(['status', 'badgeClass' => 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300'])
<span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $badgeClass }}">
    {{ $status }}
</span>
