@extends('layouts.app', [
    'title' => '预算管理 - 全录笔记',
    'headerTitle' => '预算管理',
    'headerTxt' => '管理月度/年度预算项目',
    'breadcrumb' => [
        ['label' => '首页', 'url' => route('dashboard')],
        ['label' => '资金记录', 'url' => route('funds.index')],
        ['label' => '预算管理'],
    ],
])

@section('content')
    <div class="space-y-6">
        <div class="flex justify-start">
            <a
                href="{{ route('funds.budgets.create') }}"
                class="inline-flex w-full items-center justify-center rounded-2xl bg-blue-600 px-4 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700 sm:w-auto"
            >
                添加预算
            </a>
        </div>

        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <div class="responsive-table-wrap overflow-x-auto">
                <table class="responsive-table min-w-full divide-y divide-slate-200 dark:divide-slate-800">
                    <thead class="bg-slate-50 dark:bg-slate-800/60">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">名称</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">类型</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">月金额</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">年金额</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">操作</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                        @forelse ($budgets as $budget)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40">
                                <td data-label="名称" class="px-4 py-4 align-middle">
                                    <p class="text-sm font-semibold text-slate-900 dark:text-slate-100">
                                        {{ $budget->name }}
                                    </p>
                                    @if($budget->note)
                                        <p class="mt-1 max-w-xs truncate text-xs text-slate-500 dark:text-slate-400">
                                            {{ $budget->note }}
                                        </p>
                                    @endif
                                </td>
                                <td data-label="类型" class="px-4 py-4 align-middle">
                                    @php
                                        $typeClass = $budget->type === 'income'
                                            ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300'
                                            : 'bg-red-50 text-red-700 dark:bg-red-950/40 dark:text-red-300';
                                        $typeLabel = $budget->type === 'income' ? '收入' : '支出';
                                    @endphp
                                    <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $typeClass }}">
                                        {{ $typeLabel }}
                                    </span>
                                </td>
                                <td data-label="月金额" class="px-4 py-4 align-middle">
                                    <span class="text-sm font-semibold {{ $budget->type === 'income' ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' }}">
                                        ¥{{ number_format($budget->monthly_amount, 2) }}
                                    </span>
                                </td>
                                <td data-label="年金额" class="px-4 py-4 align-middle">
                                    <span class="text-sm text-slate-600 dark:text-slate-300">¥{{ number_format($budget->annual_amount, 2) }}</span>
                                </td>
                                <td data-label="操作" class="px-4 py-4 align-middle">
                                    <x-row-actions
                                        :editRoute="route('funds.budgets.edit', $budget)"
                                        :deleteRoute="route('funds.budgets.destroy', $budget)"
                                        deleteConfirm="确定删除这个预算项吗？"
                                    />
                                </td>
                            </tr>
                        @empty
                            <x-empty-row :colspan="5" message="暂无预算记录，点击添加预算开始创建。" />
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if(isset($budgets) && method_exists($budgets, 'hasPages') && $budgets->hasPages())
                <div class="border-t border-slate-200 px-4 py-4 dark:border-slate-800">
                    {{ $budgets->links() }}
                </div>
            @endif
        </section>

        @if(isset($monthlyIncomeTotal) || isset($monthlyExpenseTotal))
            @php
                $monthlyExpenseTotal = $monthlyExpenseTotal ?? 0;
                $monthlyIncomeTotal = $monthlyIncomeTotal ?? 0;
            @endphp
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                <div class="rounded-2xl border border-red-100 bg-red-50 p-4 dark:border-red-900 dark:bg-red-950/40">
                    <p class="text-xs font-medium text-red-600 dark:text-red-400">月总支出</p>
                    <p class="mt-1 text-lg font-bold text-red-700 dark:text-red-300">¥{{ number_format($monthlyExpenseTotal, 2) }}</p>
                </div>
                <div class="rounded-2xl border border-emerald-100 bg-emerald-50 p-4 dark:border-emerald-900 dark:bg-emerald-950/40">
                    <p class="text-xs font-medium text-emerald-600 dark:text-emerald-400">月总收入</p>
                    <p class="mt-1 text-lg font-bold text-emerald-700 dark:text-emerald-300">¥{{ number_format($monthlyIncomeTotal, 2) }}</p>
                </div>
                <div class="rounded-2xl border border-blue-100 bg-blue-50 p-4 dark:border-blue-900 dark:bg-blue-950/40">
                    <p class="text-xs font-medium text-blue-600 dark:text-blue-400">月净额</p>
                    <p class="mt-1 text-lg font-bold text-blue-700 dark:text-blue-300">¥{{ number_format($monthlyIncomeTotal - $monthlyExpenseTotal, 2) }}</p>
                </div>
            </div>
        @endif
    </div>
@endsection
