@extends('layouts.app', [
    'title' => '资金记录 - 全录笔记',
    'headerTitle' => '资金记录',
    'headerTxt' => '资产、账户、预算与虚拟资产租赁管理',
    'breadcrumb' => [
        ['label' => '首页', 'url' => route('dashboard')],
        ['label' => '资金记录'],
    ],
])

@section('content')
    <div class="space-y-6">
        {{-- 统计卡片 --}}
        <section class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <p class="text-sm font-medium text-slate-500 dark:text-slate-400">总资产金额</p>
                <p class="mt-3 text-3xl font-bold tracking-tight" style="color: #ef921c">
                    ¥{{ number_format($totalAssets ?? 0, 2) }}
                </p>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <p class="text-sm font-medium text-slate-500 dark:text-slate-400">环比增长</p>
                <p class="mt-3 text-3xl font-bold tracking-tight {{ ($avgGrowth ?? 0) >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' }}">
                    ¥{{ number_format($avgGrowth ?? 0, 2) }}
                </p>
                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">近12个月平均月环比增长</p>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <p class="text-sm font-medium text-slate-500 dark:text-slate-400">日均收益</p>
                <p class="mt-3 text-3xl font-bold tracking-tight text-emerald-600 dark:text-emerald-400">
                    ¥{{ number_format($totalDailyProfit ?? 0, 2) }}
                </p>
                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">虚拟资产日租金收益总和</p>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <p class="text-sm font-medium text-slate-500 dark:text-slate-400">月均收益</p>
                <p class="mt-3 text-3xl font-bold tracking-tight text-emerald-600 dark:text-emerald-400">
                    ¥{{ number_format($totalMonthlyProfit ?? 0, 2) }}
                </p>
                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">虚拟资产月租金收益总和</p>
            </div>
        </section>

        {{-- 三列底栏 --}}
        <section class="grid grid-cols-1 gap-6 lg:grid-cols-3 items-start">
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <div class="mb-4 flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-slate-900 dark:text-slate-100">账户列表</h3>
                    <a href="{{ route('funds.accounts.index') }}" class="text-xs font-medium text-blue-600 hover:text-blue-700 dark:text-blue-400">查看全部</a>
                </div>

                @if(isset($accounts) && $accounts->isNotEmpty())
                    <div class="space-y-2">
                        @foreach($accounts as $account)
                            @php
                                $typeColors = [
                                    'cash' => 'text-emerald-600 bg-emerald-50 dark:bg-emerald-950/40',
                                    'platform' => 'text-blue-600 bg-blue-50 dark:bg-blue-950/40',
                                    'wechat' => 'text-green-600 bg-green-50 dark:bg-green-950/40',
                                    'virtual' => 'text-purple-600 bg-purple-50 dark:bg-purple-950/40',
                                    'credit' => 'text-red-600 bg-red-50 dark:bg-red-950/40',
                                    'receivable' => 'text-amber-600 bg-amber-50 dark:bg-amber-950/40',
                                    'housing_base' => 'text-indigo-600 bg-indigo-50 dark:bg-indigo-950/40',
                                    'housing_payment' => 'text-indigo-600 bg-indigo-50 dark:bg-indigo-950/40',
                                ];
                                $color = $typeColors[$account->type->value] ?? 'text-slate-600 bg-slate-50 dark:bg-slate-800';
                            @endphp
                            <div class="flex items-center justify-between rounded-xl border border-slate-100 px-3 py-2 dark:border-slate-800">
                                <div class="flex items-center gap-2">
                                    <span class="inline-block h-2 w-2 rounded-full {{ str_replace(['text-', 'bg-', 'dark:'], '', $color) }}"></span>
                                    <span class="text-sm font-medium text-slate-700 dark:text-slate-200">{{ $account->name }}</span>
                                </div>
                                <span class="text-sm font-semibold {{ $account->balance >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' }}">¥{{ number_format($account->balance, 2) }}</span>
                            </div>
                        @endforeach
                    </div>

                    @if(($totalSkinValuation ?? 0) > 0)
                    <div class="mt-3 rounded-xl border border-dashed border-purple-200 bg-purple-50/50 px-3 py-2 dark:border-purple-900 dark:bg-purple-950/30">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="inline-block h-2 w-2 rounded-full bg-purple-400 dark:bg-purple-500"></span>
                                <span class="text-sm font-medium text-slate-700 dark:text-slate-200">虚拟资产</span>
                            </div>
                            <span class="text-sm font-semibold text-purple-600 dark:text-purple-400">¥{{ number_format($totalSkinValuation, 2) }}</span>
                        </div>
                    </div>
                    @endif
                @else
                    <p class="py-4 text-center text-sm text-slate-500 dark:text-slate-400">暂无账户记录</p>
                @endif

                <div class="mt-4">
                    <a href="{{ route('funds.accounts.index') }}" class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200 dark:hover:bg-slate-800">
                        管理账户
                    </a>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <div class="mb-4 flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-slate-900 dark:text-slate-100">虚拟资产</h3>
                    <a href="{{ route('funds.skins.index') }}" class="text-xs font-medium text-blue-600 hover:text-blue-700 dark:text-blue-400">查看全部</a>
                </div>

                <div class="space-y-3">
                    @if(isset($skins) && $skins->isNotEmpty())
                    <div class="grid grid-cols-3 gap-3">
                        <div class="rounded-xl border border-slate-100 bg-slate-50 px-4 py-3 dark:border-slate-700 dark:bg-slate-800/60">
                            <p class="text-xs text-slate-500 dark:text-slate-400">虚拟资产数量</p>
                            <p class="mt-1 text-lg font-bold text-slate-900 dark:text-slate-100">{{ $skins->count() }}</p>
                        </div>
                        <div class="rounded-xl border border-slate-100 bg-slate-50 px-4 py-3 dark:border-slate-700 dark:bg-slate-800/60">
                            <p class="text-xs text-slate-500 dark:text-slate-400">总估值</p>
                            <p class="mt-1 text-lg font-bold text-purple-600 dark:text-purple-400">¥{{ number_format($totalSkinValuation ?? 0, 2) }}</p>
                        </div>
                        <div class="rounded-xl border border-slate-100 bg-slate-50 px-4 py-3 dark:border-slate-700 dark:bg-slate-800/60">
                            <p class="text-xs text-slate-500 dark:text-slate-400">累计收益</p>
                            <p class="mt-1 text-lg font-bold text-emerald-600 dark:text-emerald-400">¥{{ number_format($totalCumulativeEarnings ?? 0, 2) }}</p>
                        </div>
                    </div>
                    @endif
                    <a href="{{ route('funds.skins.index') }}" class="flex items-center justify-between rounded-xl border border-slate-200 px-4 py-3 transition hover:bg-slate-50 dark:border-slate-700 dark:hover:bg-slate-800">
                        <span class="text-sm font-medium text-slate-700 dark:text-slate-200">虚拟资产估值一览表</span>
                        <span class="text-slate-400">&rarr;</span>
                    </a>
                    <a href="{{ route('funds.historical-earnings') }}" class="flex items-center justify-between rounded-xl border border-slate-200 px-4 py-3 transition hover:bg-slate-50 dark:border-slate-700 dark:hover:bg-slate-800">
                        <span class="text-sm font-medium text-slate-700 dark:text-slate-200">累计收益</span>
                        <span class="text-slate-400">&rarr;</span>
                    </a>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <div class="mb-4">
                    <h3 class="text-sm font-semibold text-slate-900 dark:text-slate-100">统计记录</h3>
                </div>
                <div class="space-y-3">
                    <a href="{{ route('funds.budgets.index') }}" class="flex items-center justify-between rounded-xl border border-slate-200 px-4 py-3 transition hover:bg-slate-50 dark:border-slate-700 dark:hover:bg-slate-800">
                        <div>
                            <p class="text-sm font-medium text-slate-700 dark:text-slate-200">预算管理</p>
                            <p class="text-xs text-slate-500 dark:text-slate-400">管理收支预算项</p>
                        </div>
                        <span class="text-slate-400">&rarr;</span>
                    </a>
                    <a href="{{ route('funds.monthlies.index') }}" class="flex items-center justify-between rounded-xl border border-slate-200 px-4 py-3 transition hover:bg-slate-50 dark:border-slate-700 dark:hover:bg-slate-800">
                        <div>
                            <p class="text-sm font-medium text-slate-700 dark:text-slate-200">月度记录</p>
                            <p class="text-xs text-slate-500 dark:text-slate-400">每月收入支出与储蓄</p>
                        </div>
                        <span class="text-slate-400">&rarr;</span>
                    </a>
                    <a href="{{ route('funds.statistics') }}" class="flex items-center justify-between rounded-xl border border-slate-200 px-4 py-3 transition hover:bg-slate-50 dark:border-slate-700 dark:hover:bg-slate-800">
                        <div>
                            <p class="text-sm font-medium text-slate-700 dark:text-slate-200">统计图表</p>
                            <p class="text-xs text-slate-500 dark:text-slate-400">收支趋势与储蓄进度</p>
                        </div>
                        <span class="text-slate-400">&rarr;</span>
                    </a>
                </div>
            </div>
        </section>
    </div>
@endsection
