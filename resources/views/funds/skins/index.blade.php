@extends('layouts.app', [
    'title' => '虚拟资产估值一览表 - 全录笔记',
    'headerTitle' => '虚拟资产估值一览表',
    'headerTxt' => '虚拟资产成本、价格与租赁收益概览',
    'breadcrumb' => [
        ['label' => '首页', 'url' => route('dashboard')],
        ['label' => '资金记录', 'url' => route('funds.index')],
        ['label' => '一览表'],
    ],
])

@section('content')
    <div class="space-y-6">
        <div class="flex justify-start">
            <a
                href="{{ route('funds.skins.create') }}"
                class="inline-flex w-full items-center justify-center rounded-2xl bg-blue-600 px-4 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700 sm:w-auto"
            >
                新增虚拟资产
            </a>
        </div>

        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <div class="responsive-table-wrap overflow-x-auto">
                <table class="responsive-table min-w-full divide-y divide-slate-200 dark:divide-slate-800">
                    <thead class="bg-slate-50 dark:bg-slate-800/60">
                        <tr>
                            <th class="px-3 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">名称</th>
                            <th class="px-3 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">购入时间</th>
                            <th class="px-3 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">购入价</th>
                            <th class="px-3 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">uu价值</th>
                            <th class="px-3 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">uu售出收益</th>
                            <th class="px-3 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">buff价值</th>
                            <th class="px-3 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">buff售出收益</th>
                            <th class="px-3 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">择优收益</th>
                            <th class="px-3 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">估值</th>
                            <th class="px-3 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">租金</th>
                            <th class="px-3 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">日均收益</th>
                            <th class="px-3 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">月均收益</th>
                            <th class="px-3 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">年化</th>
                            <th class="px-3 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">操作</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                        @forelse ($skins as $skin)
                            @php
                                $uuSale = ($skin->uu_price * (1 - $skin->uu_fee_rate)) - $skin->cost;
                                $buffSale = ($skin->buff_price * (1 - $skin->buff_fee_rate)) - $skin->cost;
                                $bestProfit = max($uuSale, $buffSale);
                                $valuation = $skin->cost + $bestProfit;
                                $rent = $skin->daily_rental;
                                $dailyProfit = $rent * 0.8 * 0.5 * 0.99;
                                $monthlyProfit = $dailyProfit * 30.5;
                                $annualRate = $skin->cost > 0 ? ($dailyProfit * 365 / $skin->cost) * 100 : 0;
                            @endphp
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40">
                                <td data-label="名称" class="px-3 py-4 align-middle">
                                    <span class="text-sm font-semibold text-purple-600 dark:text-purple-400">{{ $skin->name }}</span>
                                </td>
                                <td data-label="购入时间" class="px-3 py-4 align-middle">
                                    <span class="text-sm text-slate-600 dark:text-slate-300">{{ $skin->purchased_at ? $skin->purchased_at->format('Y-m-d') : '-' }}</span>
                                </td>
                                <td data-label="购入价" class="px-3 py-4 align-middle">
                                    <span class="text-sm text-slate-600 dark:text-slate-300">¥{{ number_format($skin->cost, 2) }}</span>
                                </td>
                                <td data-label="uu价值" class="px-3 py-4 align-middle">
                                    <span class="text-sm text-slate-600 dark:text-slate-300">¥{{ number_format($skin->uu_price, 2) }}</span>
                                </td>
                                <td data-label="uu售出收益" class="px-3 py-4 align-middle">
                                    <span class="text-sm {{ $uuSale >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' }}">¥{{ number_format($uuSale, 2) }}</span>
                                </td>
                                <td data-label="buff价值" class="px-3 py-4 align-middle">
                                    <span class="text-sm text-slate-600 dark:text-slate-300">¥{{ number_format($skin->buff_price, 2) }}</span>
                                </td>
                                <td data-label="buff售出收益" class="px-3 py-4 align-middle">
                                    <span class="text-sm {{ $buffSale >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' }}">¥{{ number_format($buffSale, 2) }}</span>
                                </td>
                                <td data-label="择优收益" class="px-3 py-4 align-middle">
                                    <span class="text-sm font-semibold {{ $bestProfit >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' }}">¥{{ number_format($bestProfit, 2) }}</span>
                                </td>
                                <td data-label="估值" class="px-3 py-4 align-middle">
                                    <span class="text-sm font-semibold text-purple-600 dark:text-purple-400">¥{{ number_format($valuation, 2) }}</span>
                                </td>
                                <td data-label="租金" class="px-3 py-4 align-middle">
                                    <span class="text-sm text-slate-600 dark:text-slate-300">¥{{ number_format($rent, 2) }}</span>
                                </td>
                                <td data-label="日均收益" class="px-3 py-4 align-middle">
                                    <span class="text-sm text-slate-600 dark:text-slate-300">¥{{ number_format($dailyProfit, 2) }}</span>
                                </td>
                                <td data-label="月均收益" class="px-3 py-4 align-middle">
                                    <span class="text-sm text-slate-600 dark:text-slate-300">¥{{ number_format($monthlyProfit, 2) }}</span>
                                </td>
                                <td data-label="年化" class="px-3 py-4 align-middle">
                                    <span class="text-sm text-slate-600 dark:text-slate-300">{{ number_format($annualRate, 1) }}%</span>
                                </td>
                                <td data-label="操作" class="px-3 py-4 align-middle">
                                    <x-row-actions
                                        :editRoute="route('funds.skins.edit', $skin)"
                                        :deleteRoute="route('funds.skins.destroy', $skin)"
                                        deleteConfirm="确定删除这个虚拟资产吗？"
                                    />
                                </td>
                            </tr>
                        @empty
                            <x-empty-row :colspan="14" message="暂无一览表数据，请先添加虚拟资产。" />
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($skins->hasPages())
                <div class="border-t border-slate-200 px-4 py-4 dark:border-slate-800">
                    {{ $skins->links() }}
                </div>
            @endif
        </section>

        {{-- 合计卡片 --}}
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <p class="text-sm font-medium text-slate-500 dark:text-slate-400">合计购入价</p>
                <p class="mt-2 text-2xl font-bold text-slate-900 dark:text-slate-100">¥{{ number_format($totalCost ?? 0, 2) }}</p>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <p class="text-sm font-medium text-slate-500 dark:text-slate-400">合计估值</p>
                <p class="mt-2 text-2xl font-bold text-purple-600 dark:text-purple-400">¥{{ number_format($totalValuation ?? 0, 2) }}</p>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <p class="text-sm font-medium text-slate-500 dark:text-slate-400">合计择优收益</p>
                <p class="mt-2 text-2xl font-bold {{ ($totalProfit ?? 0) >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' }}">¥{{ number_format($totalProfit ?? 0, 2) }}</p>
            </div>
        </div>
    </div>
@endsection
