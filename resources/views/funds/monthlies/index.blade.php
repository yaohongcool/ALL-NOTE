@extends('layouts.app', [
    'title' => '月度记录 - 全录笔记',
    'headerTitle' => '月度记录',
    'headerTxt' => '按月汇总收入、支出和存款',
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
                添加月度记录
            </a>
        </div>

        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <div class="responsive-table-wrap overflow-x-auto">
                <table class="responsive-table min-w-full divide-y divide-slate-200 dark:divide-slate-800">
                    <thead class="bg-slate-50 dark:bg-slate-800/60">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">月份</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">收入</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">支出</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">净额</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">存钱目标</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">实际存款</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">差额</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">状态</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">操作</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                        @forelse ($monthlies as $monthly)
                            @php
                                $netAmount = $monthly->income - $monthly->expense;
                                $diff = $monthly->savings_actual - $monthly->savings_target;

                                $statusClass = match ($monthly->savings_status) {
                                    '达成' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300',
                                    '未达成' => 'bg-red-50 text-red-700 dark:bg-red-950/40 dark:text-red-300',
                                    '不适用' => 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300',
                                    default => 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300',
                                };
                            @endphp
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40">
                                <td data-label="月份" class="px-4 py-4 align-middle">
                                    <span class="text-sm font-semibold text-slate-900 dark:text-slate-100">{{ $monthly->month }}</span>
                                </td>
                                <td data-label="收入" class="px-4 py-4 align-middle">
                                    <span class="text-sm text-emerald-600 dark:text-emerald-400">¥{{ number_format($monthly->income, 2) }}</span>
                                </td>
                                <td data-label="支出" class="px-4 py-4 align-middle">
                                    <span class="text-sm text-red-600 dark:text-red-400">¥{{ number_format($monthly->expense, 2) }}</span>
                                </td>
                                <td data-label="净额" class="px-4 py-4 align-middle">
                                    <span class="text-sm font-semibold {{ $netAmount >= 0 ? 'text-slate-700 dark:text-slate-200' : 'text-red-600 dark:text-red-400' }}">
                                        ¥{{ number_format($netAmount, 2) }}
                                    </span>
                                </td>
                                <td data-label="存钱目标" class="px-4 py-4 align-middle">
                                    <span class="text-sm text-slate-600 dark:text-slate-300">¥{{ number_format($monthly->savings_target, 2) }}</span>
                                </td>
                                <td data-label="实际存款" class="px-4 py-4 align-middle">
                                    <span class="text-sm text-slate-600 dark:text-slate-300">¥{{ number_format($monthly->savings_actual, 2) }}</span>
                                </td>
                                <td data-label="差额" class="px-4 py-4 align-middle">
                                    <span class="text-sm font-semibold {{ $diff >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' }}">
                                        {{ $diff >= 0 ? '+' : '' }}¥{{ number_format($diff, 2) }}
                                    </span>
                                </td>
                                <td data-label="状态" class="px-4 py-4 align-middle">
                                    <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $statusClass }}">
                                        {{ $monthly->savings_status }}
                                    </span>
                                </td>
                                <td data-label="操作" class="px-4 py-4 align-middle">
                                    <x-row-actions
                                        :editRoute="route('funds.monthlies.edit', $monthly)"
                                        :deleteRoute="route('funds.monthlies.destroy', $monthly)"
                                        deleteConfirm="确定删除这条月度记录吗？"
                                    />
                                </td>
                            </tr>
                        @empty
                            <x-empty-row :colspan="9" message="暂无月度记录，点击添加月度记录开始创建。" />
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
