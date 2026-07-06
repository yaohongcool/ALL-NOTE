@extends('layouts.app', [
    'title' => '历史收益 - 全录笔记',
    'headerTitle' => '历史收益',
    'headerTxt' => '各饰品月度收益汇总',
    'breadcrumb' => [
        ['label' => '首页', 'url' => route('dashboard')],
        ['label' => '资金记录', 'url' => route('funds.index')],
        ['label' => '历史收益'],
    ],
])

@section('content')
    <div class="space-y-6">
        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <div class="responsive-table-wrap overflow-x-auto">
                <table class="responsive-table min-w-full divide-y divide-slate-200 dark:divide-slate-800">
                    <thead class="bg-slate-50 dark:bg-slate-800/60">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">月份</th>
                            @foreach($skins as $skin)
                            <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">{{ $skin->name }}</th>
                            @endforeach
                            <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">合计</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                        @forelse ($earningsByMonth as $row)
                            @php
                                $monthName = \Carbon\Carbon::parse($row['ym'] . '-01')->format('Y年m月');
                                $monthTotal = collect($skins)->sum(fn ($s) => $row['items'][$s->id] ?? 0);
                            @endphp
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40">
                                <td data-label="月份" class="px-4 py-4 align-middle">
                                    <span class="text-sm font-medium text-slate-700 dark:text-slate-200">{{ $monthName }}</span>
                                </td>
                                @foreach($skins as $skin)
                                <td data-label="{{ $skin->name }}" class="px-4 py-4 text-right align-middle">
                                    <span class="text-sm text-emerald-600 dark:text-emerald-400">¥{{ number_format($row['items'][$skin->id] ?? 0, 2) }}</span>
                                </td>
                                @endforeach
                                <td data-label="合计" class="px-4 py-4 text-right align-middle">
                                    <span class="text-sm font-semibold text-emerald-700 dark:text-emerald-300">¥{{ number_format($monthTotal, 2) }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ 2 + $skins->count() }}" class="px-4 py-10 text-center align-middle text-sm text-slate-500 dark:text-slate-400">
                                    暂无历史收益记录。
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if(!empty($earningsByMonth))
                    <tfoot class="bg-slate-50 dark:bg-slate-800/60">
                        <tr>
                            <td class="px-4 py-4 text-sm font-semibold text-slate-700 dark:text-slate-200">合计</td>
                            @foreach($skins as $skin)
                            <td class="px-4 py-4 text-right align-middle">
                                <span class="text-sm font-bold text-emerald-700 dark:text-emerald-300">¥{{ number_format($totalsBySkin[$skin->id] ?? 0, 2) }}</span>
                            </td>
                            @endforeach
                            <td class="px-4 py-4 text-right align-middle">
                                <span class="text-sm font-bold text-emerald-800 dark:text-emerald-200">¥{{ number_format(collect($totalsBySkin)->sum(), 2) }}</span>
                            </td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
        </section>

        <div class="flex justify-start">
            @if($skins->isNotEmpty())
            <a href="{{ route('funds.skin-earnings.create', $skins->first()) }}"
                class="inline-flex items-center justify-center rounded-2xl bg-blue-600 px-4 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700 sm:w-auto">
                登记月度收益
            </a>
            @endif
        </div>
    </div>
@endsection
