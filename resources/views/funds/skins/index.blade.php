@extends('layouts.app', [
    'title' => '饰品管理 - 全录笔记',
    'headerTitle' => '饰品管理',
    'headerTxt' => '管理游戏饰品成本、价格与租赁',
    'breadcrumb' => [
        ['label' => '首页', 'url' => route('dashboard')],
        ['label' => '资金记录', 'url' => route('funds.index')],
        ['label' => '饰品管理'],
    ],
])

@section('content')
    <div class="space-y-6">
        <div class="flex justify-start">
            <a
                href="{{ route('funds.skins.create') }}"
                class="inline-flex w-full items-center justify-center rounded-2xl bg-blue-600 px-4 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700 sm:w-auto"
            >
                添加饰品
            </a>
        </div>

        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <div class="responsive-table-wrap overflow-x-auto">
                <table class="responsive-table min-w-full divide-y divide-slate-200 dark:divide-slate-800">
                    <thead class="bg-slate-50 dark:bg-slate-800/60">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">名称</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">成本</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">UU价格</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Buff价格</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">择优利润</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">估值</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">操作</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                        @forelse ($skins as $skin)
                            @php
                                $uuProfit = ($skin->uu_price * (1 - $skin->uu_fee_rate)) - $skin->cost;
                                $buffProfit = ($skin->buff_price * (1 - $skin->buff_fee_rate)) - $skin->cost;
                                $bestProfit = max($uuProfit, $buffProfit);
                                $valuation = $skin->valuation ?? max($skin->cost, $skin->uu_price, $skin->buff_price);
                            @endphp
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40">
                                <td data-label="名称" class="px-4 py-4 align-middle">
                                    <a href="{{ route('funds.skins.show', $skin) }}" class="text-sm font-semibold text-slate-900 transition hover:text-blue-700 dark:text-slate-100 dark:hover:text-blue-400">
                                        {{ $skin->name }}
                                    </a>
                                    @if($skin->note)
                                        <p class="mt-1 max-w-xs truncate text-xs text-slate-500 dark:text-slate-400">
                                            {{ $skin->note }}
                                        </p>
                                    @endif
                                </td>
                                <td data-label="成本" class="px-4 py-4 align-middle">
                                    <span class="text-sm text-slate-600 dark:text-slate-300">¥{{ number_format($skin->cost, 2) }}</span>
                                </td>
                                <td data-label="UU价格" class="px-4 py-4 align-middle">
                                    <span class="text-sm text-slate-600 dark:text-slate-300">¥{{ number_format($skin->uu_price, 2) }}</span>
                                </td>
                                <td data-label="Buff价格" class="px-4 py-4 align-middle">
                                    <span class="text-sm text-slate-600 dark:text-slate-300">¥{{ number_format($skin->buff_price, 2) }}</span>
                                </td>
                                <td data-label="择优利润" class="px-4 py-4 align-middle">
                                    <span class="text-sm font-semibold {{ $bestProfit >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' }}">
                                        ¥{{ number_format($bestProfit, 2) }}
                                    </span>
                                </td>
                                <td data-label="估值" class="px-4 py-4 align-middle">
                                    <span class="text-sm font-semibold text-slate-700 dark:text-slate-200">¥{{ number_format($valuation, 2) }}</span>
                                </td>
                                <td data-label="操作" class="px-4 py-4 align-middle">
                                    <div class="flex justify-end gap-2">
                                        <a
                                            href="{{ route('funds.skins.show', $skin) }}"
                                            class="rounded-xl border border-slate-200 px-3 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-100 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800"
                                        >
                                            详情
                                        </a>
                                        <a
                                            href="{{ route('funds.skins.edit', $skin) }}"
                                            class="rounded-xl border border-slate-200 px-3 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-100 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800"
                                        >
                                            编辑
                                        </a>
                                        <form method="POST" action="{{ route('funds.skins.destroy', $skin) }}" onsubmit="return confirm('确定删除这个饰品吗？');">
                                            @csrf
                                            @method('DELETE')
                                            <button
                                                type="submit"
                                                class="rounded-xl border border-red-200 px-3 py-2 text-sm font-medium text-red-600 transition hover:bg-red-50 dark:border-red-900 dark:text-red-400 dark:hover:bg-red-950/40"
                                            >
                                                删除
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <x-empty-row :colspan="7" message="暂无饰品记录，点击添加饰品开始创建。" />
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

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <p class="text-sm font-medium text-slate-500 dark:text-slate-400">总成本</p>
                <p class="mt-2 text-2xl font-bold text-slate-900 dark:text-slate-100">¥{{ number_format($totalCost ?? 0, 2) }}</p>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <p class="text-sm font-medium text-slate-500 dark:text-slate-400">总估值</p>
                <p class="mt-2 text-2xl font-bold text-slate-900 dark:text-slate-100">¥{{ number_format($totalValuation ?? 0, 2) }}</p>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <p class="text-sm font-medium text-slate-500 dark:text-slate-400">总利润</p>
                <p class="mt-2 text-2xl font-bold {{ ($totalProfit ?? 0) >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' }}">¥{{ number_format($totalProfit ?? 0, 2) }}</p>
            </div>
        </div>
    </div>
@endsection
