@props(['headers' => [], 'colspan' => 1])
<section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
    <div class="responsive-table-wrap overflow-x-auto">
        <table class="responsive-table min-w-full divide-y divide-slate-200 dark:divide-slate-800">
            @if(!empty($headers))
            <thead class="bg-slate-50 dark:bg-slate-800/60">
                <tr>
                    @foreach($headers as $header)
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">{{ $header }}</th>
                    @endforeach
                </tr>
            </thead>
            @endif
            {{ $slot }}
        </table>
    </div>
</section>
