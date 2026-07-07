@extends('layouts.app', [
    'title' => '月度记录 - 全录笔记',
    'headerTitle' => '月度记录',
    'headerTxt' => '按月度记录金额',
    'breadcrumb' => [
        ['label' => '首页', 'url' => route('dashboard')],
        ['label' => '资金记录', 'url' => route('funds.index')],
        ['label' => '月度记录'],
    ],
])

@section('content')
    <div class="space-y-6">

        <div class="flex justify-start">
            <a
                href="{{ route('funds.monthlies.create') }}"
                class="inline-flex w-full items-center justify-center rounded-2xl bg-blue-600 px-4 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700 sm:w-auto"
            >
                新增月度记录
            </a>
        </div>

        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <div class="responsive-table-wrap overflow-x-auto">
                <table class="responsive-table min-w-full divide-y divide-slate-200 dark:divide-slate-800">
                    <thead class="bg-slate-50 dark:bg-slate-800/60">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">时间</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">金额</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">环比增长</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">备注</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">操作</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                        @forelse ($monthlies as $m)
                            @php
                                $growth = $growthData[$m->id] ?? null;
                            @endphp
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40">
                                <td data-label="时间" class="px-4 py-4 align-middle">
                                    <span class="text-sm text-slate-700 dark:text-slate-200">{{ $m->month instanceof \Carbon\Carbon ? $m->month->format('Y-m-d') : $m->month }}</span>
                                </td>
                                <td data-label="金额" class="px-4 py-4 text-right align-middle">
                                    <span class="text-sm font-semibold text-emerald-600 dark:text-emerald-400">¥{{ number_format($m->income, 2) }}</span>
                                </td>
                                <td data-label="环比增长" class="px-4 py-4 text-right align-middle">
                                    @if($growth !== null)
                                        <span class="text-sm font-medium {{ ($growth ?? 0) >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' }}">
                                            ¥{{ number_format($growth, 2) }}
                                        </span>
                                    @else
                                        <span class="text-sm text-slate-400">-</span>
                                    @endif
                                </td>
                                <td data-label="备注" class="px-4 py-4 align-middle">
                                    <span class="text-sm text-slate-600 dark:text-slate-300">{{ $m->note ?: '-' }}</span>
                                </td>
                                <td data-label="操作" class="px-4 py-4 align-middle">
                                    <x-row-actions
                                        :editRoute="route('funds.monthlies.edit', $m)"
                                        :deleteRoute="route('funds.monthlies.destroy', $m)"
                                        deleteConfirm="确定删除这条月度记录吗？"
                                    />
                                </td>
                            </tr>
                        @empty
                            <x-empty-row :colspan="5" message="暂无月度记录。" />
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($monthlies->hasPages())
                <div class="border-t border-slate-200 px-4 py-4 dark:border-slate-800">
                    {{ $monthlies->links() }}
                </div>
            @endif
        </section>
    </div>
@endsection
