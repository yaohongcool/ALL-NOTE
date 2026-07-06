@extends('layouts.app', [
    'title' => $skin->name . ' - 饰品详情 - 全录笔记',
    'headerTitle' => '饰品详情',
    'headerTxt' => $skin->name,
    'breadcrumb' => [
        ['label' => '首页', 'url' => route('dashboard')],
        ['label' => '资金记录', 'url' => route('funds.index')],
        ['label' => '饰品管理', 'url' => route('funds.skins.index')],
        ['label' => $skin->name],
    ],
])

@section('content')
    @php
        $uuProfit = ($skin->uu_price * (1 - $skin->uu_fee_rate)) - $skin->cost;
        $buffProfit = ($skin->buff_price * (1 - $skin->buff_fee_rate)) - $skin->cost;
        $bestProfit = max($uuProfit, $buffProfit);
        $valuation = $skin->valuation ?? max($skin->cost, $skin->uu_price, $skin->buff_price);
    @endphp

    <div class="space-y-6">
        <div class="grid grid-cols-1 gap-5 md:grid-cols-2 lg:grid-cols-4">
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <p class="text-xs font-medium text-slate-500 dark:text-slate-400">成本</p>
                <p class="mt-2 text-xl font-bold text-slate-900 dark:text-slate-100">¥{{ number_format($skin->cost, 2) }}</p>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <p class="text-xs font-medium text-slate-500 dark:text-slate-400">UU价格</p>
                <p class="mt-2 text-xl font-bold text-slate-900 dark:text-slate-100">¥{{ number_format($skin->uu_price, 2) }}</p>
                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">手续费 {{ number_format($skin->uu_fee_rate * 100, 1) }}%</p>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <p class="text-xs font-medium text-slate-500 dark:text-slate-400">Buff价格</p>
                <p class="mt-2 text-xl font-bold text-slate-900 dark:text-slate-100">¥{{ number_format($skin->buff_price, 2) }}</p>
                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">手续费 {{ number_format($skin->buff_fee_rate * 100, 1) }}%</p>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <p class="text-xs font-medium text-slate-500 dark:text-slate-400">日租金</p>
                <p class="mt-2 text-xl font-bold text-slate-900 dark:text-slate-100">¥{{ number_format($skin->daily_rental, 2) }}</p>
                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">估值 ¥{{ number_format($valuation, 2) }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-5 md:grid-cols-3">
            <div class="rounded-2xl border border-emerald-100 bg-emerald-50 p-4 dark:border-emerald-900 dark:bg-emerald-950/40">
                <p class="text-xs font-medium text-emerald-600 dark:text-emerald-400">UU净利润</p>
                <p class="mt-1 text-lg font-bold text-emerald-700 dark:text-emerald-300">¥{{ number_format($uuProfit, 2) }}</p>
            </div>
            <div class="rounded-2xl border border-amber-100 bg-amber-50 p-4 dark:border-amber-900 dark:bg-amber-950/40">
                <p class="text-xs font-medium text-amber-600 dark:text-amber-400">Buff净利润</p>
                <p class="mt-1 text-lg font-bold text-amber-700 dark:text-amber-300">¥{{ number_format($buffProfit, 2) }}</p>
            </div>
            <div class="rounded-2xl border border-blue-100 bg-blue-50 p-4 dark:border-blue-900 dark:bg-blue-950/40">
                <p class="text-xs font-medium text-blue-600 dark:text-blue-400">择优利润</p>
                <p class="mt-1 text-lg font-bold text-blue-700 dark:text-blue-300">¥{{ number_format($bestProfit, 2) }}</p>
            </div>
        </div>

        @if($skin->note)
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <h3 class="text-sm font-semibold text-slate-900 dark:text-slate-100">备注</h3>
                <p class="mt-2 text-sm text-slate-600 dark:text-slate-300">{{ $skin->note }}</p>
            </div>
        @endif

        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <div class="flex items-center justify-between border-b border-slate-200 px-6 py-4 dark:border-slate-800">
                <h3 class="text-sm font-semibold text-slate-900 dark:text-slate-100">租赁记录</h3>
                <a
                    href="{{ route('funds.rentals.create', $skin) }}"
                    class="inline-flex items-center justify-center rounded-xl bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700"
                >
                    添加租赁记录
                </a>
            </div>

            <div class="responsive-table-wrap overflow-x-auto">
                <table class="responsive-table min-w-full divide-y divide-slate-200 dark:divide-slate-800">
                    <thead class="bg-slate-50 dark:bg-slate-800/60">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">类型</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">租金</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">天数</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">收益</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">操作</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                        @forelse ($rentals ?? [] as $rental)
                            @php
                                $totalDays = $rental->lease_days + $rental->offhand_days;
                                $revenue = $rental->rate * $rental->discount * $rental->lease_days;
                            @endphp
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40">
                                <td data-label="类型" class="px-4 py-4 align-middle">
                                    <span class="text-sm text-slate-700 dark:text-slate-200">{{ $rental->type }}</span>
                                </td>
                                <td data-label="租金" class="px-4 py-4 align-middle">
                                    <span class="text-sm text-slate-600 dark:text-slate-300">¥{{ number_format($rental->rate, 2) }}</span>
                                </td>
                                <td data-label="天数" class="px-4 py-4 align-middle">
                                    <span class="text-sm text-slate-600 dark:text-slate-300">{{ $totalDays }} 天（出租{{ $rental->lease_days }}天/空闲{{ $rental->offhand_days }}天）</span>
                                </td>
                                <td data-label="收益" class="px-4 py-4 align-middle">
                                    <span class="text-sm font-semibold text-emerald-600 dark:text-emerald-400">¥{{ number_format($revenue, 2) }}</span>
                                </td>
                                <td data-label="操作" class="px-4 py-4 align-middle">
                                    <x-row-actions
                                        :editRoute="route('funds.rentals.edit', [$skin, $rental])"
                                        :deleteRoute="route('funds.rentals.destroy', [$skin, $rental])"
                                        deleteConfirm="确定删除这条租赁记录吗？"
                                    />
                                </td>
                            </tr>
                        @empty
                            <x-empty-row :colspan="5" message="暂无租赁记录。" />
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
