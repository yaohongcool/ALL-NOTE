@extends('layouts.app', [
    'title' => '资金记录 - 全录笔记',
    'headerTitle' => '资金记录',
    'headerTxt' => '资产、账户、预算与饰品租赁管理',
    'breadcrumb' => [
        ['label' => '首页', 'url' => route('dashboard')],
        ['label' => '资金记录'],
    ],
])

@section('content')
    @php
        $currentMonthIncome = $thisMonth->income ?? 0;
        $currentMonthExpense = $thisMonth->expense ?? 0;
        $currentMonthSavingsTarget = $thisMonth->savings_target ?? 0;
        $currentMonthSavingsActual = $thisMonth->savings_actual ?? 0;
        $savingsPct = $currentMonthSavingsTarget > 0 ? min(100, ($currentMonthSavingsActual / $currentMonthSavingsTarget) * 100) : 0;

        $allBudgets = auth()->user()->fundBudgets()->get();
        $budgetExpenseTotal = $allBudgets->where('type', 'expense')->sum('monthly_amount');
        $budgetIncomeTotal = $allBudgets->where('type', 'income')->sum('monthly_amount');
        $overBudget = $budgetExpenseTotal > 0 && $currentMonthExpense > $budgetExpenseTotal;
    @endphp

    <div class="space-y-6">
        {{-- 统计卡片 --}}
        <section class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <p class="text-sm font-medium text-slate-500 dark:text-slate-400">总资产金额</p>
                <p class="mt-3 text-3xl font-bold tracking-tight text-slate-900 dark:text-slate-100">
                    ¥{{ number_format($totalAssets ?? 0, 2) }}
                </p>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <p class="text-sm font-medium text-slate-500 dark:text-slate-400">本月收入</p>
                <p class="mt-3 text-3xl font-bold tracking-tight text-emerald-600 dark:text-emerald-400">
                    ¥{{ number_format($currentMonthIncome, 2) }}
                </p>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <p class="text-sm font-medium text-slate-500 dark:text-slate-400">本月支出</p>
                <p class="mt-3 text-3xl font-bold tracking-tight text-red-600 dark:text-red-400">
                    ¥{{ number_format($currentMonthExpense, 2) }}
                </p>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <p class="text-sm font-medium text-slate-500 dark:text-slate-400">本月结余</p>
                <p class="mt-3 text-3xl font-bold tracking-tight text-blue-600 dark:text-blue-400">
                    ¥{{ number_format($currentMonthIncome - $currentMonthExpense, 2) }}
                </p>
            </div>
        </section>

        {{-- 预算对比 + 储蓄进度 --}}
        @if($budgetExpenseTotal > 0 || $currentMonthSavingsTarget > 0)
        <section class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            @if($budgetExpenseTotal > 0)
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <div class="flex items-center justify-between">
                    <p class="text-sm font-medium text-slate-500 dark:text-slate-400">本月预算 vs 实际</p>
                    <a href="{{ route('funds.budgets.index') }}" class="text-xs text-blue-600 hover:text-blue-700 dark:text-blue-400">预算明细</a>
                </div>
                <div class="mt-3 space-y-2">
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-slate-600 dark:text-slate-300">预算支出</span>
                        <span class="font-medium text-slate-900 dark:text-slate-100">¥{{ number_format($budgetExpenseTotal, 2) }}</span>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-slate-600 dark:text-slate-300">实际支出</span>
                        <span class="font-medium {{ $overBudget ? 'text-red-600 dark:text-red-400' : 'text-emerald-600 dark:text-emerald-400' }}">¥{{ number_format($currentMonthExpense, 2) }}</span>
                    </div>
                    @if($overBudget)
                    <p class="text-xs text-red-500">超支 ¥{{ number_format($currentMonthExpense - $budgetExpenseTotal, 2) }}</p>
                    @endif
                </div>
            </div>
            @endif

            @if($currentMonthSavingsTarget > 0)
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <div class="flex items-center justify-between">
                    <p class="text-sm font-medium text-slate-500 dark:text-slate-400">本月储蓄进度</p>
                    <a href="{{ route('funds.monthlies.index') }}" class="text-xs text-blue-600 hover:text-blue-700 dark:text-blue-400">查看月度</a>
                </div>
                <div class="mt-3">
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-slate-600 dark:text-slate-300">目标</span>
                        <span class="font-medium text-slate-900 dark:text-slate-100">¥{{ number_format($currentMonthSavingsTarget, 2) }}</span>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-slate-600 dark:text-slate-300">已存</span>
                        <span class="font-medium {{ $currentMonthSavingsActual >= $currentMonthSavingsTarget ? 'text-emerald-600 dark:text-emerald-400' : 'text-amber-600 dark:text-amber-400' }}">¥{{ number_format($currentMonthSavingsActual, 2) }}</span>
                    </div>
                    <div class="mt-2 h-2 w-full rounded-full bg-slate-100 dark:bg-slate-800">
                        <div class="h-full rounded-full {{ $savingsPct >= 100 ? 'bg-emerald-500' : 'bg-blue-500' }}" style="width: {{ $savingsPct }}%;"></div>
                    </div>
                    <p class="mt-1 text-right text-xs text-slate-400">{{ number_format($savingsPct, 1) }}%</p>
                </div>
            </div>
            @endif
        </section>
        @endif

        {{-- 三列底栏 --}}
        <section class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <div class="mb-4 flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-slate-900 dark:text-slate-100">账户列表</h3>
                    <a href="{{ route('funds.accounts.index') }}" class="text-xs font-medium text-blue-600 hover:text-blue-700 dark:text-blue-400">查看全部</a>
                </div>

                @if(isset($accounts) && $accounts->isNotEmpty())
                    <div class="space-y-2">
                        @foreach($accounts->take(5) as $account)
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
                                $color = $typeColors[$account->type] ?? 'text-slate-600 bg-slate-50 dark:bg-slate-800';
                            @endphp
                            <div class="flex items-center justify-between rounded-xl border border-slate-100 px-3 py-2 dark:border-slate-800">
                                <div class="flex items-center gap-2">
                                    <span class="inline-block h-2 w-2 rounded-full {{ str_replace(['text-', 'bg-', 'dark:'], '', $color) }}"></span>
                                    <span class="text-sm font-medium text-slate-700 dark:text-slate-200">{{ $account->name }}</span>
                                </div>
                                <span class="text-sm font-semibold text-slate-900 dark:text-slate-100">¥{{ number_format($account->balance, 2) }}</span>
                            </div>
                        @endforeach
                    </div>
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
                    <h3 class="text-sm font-semibold text-slate-900 dark:text-slate-100">饰品管理</h3>
                    <a href="{{ route('funds.skins.index') }}" class="text-xs font-medium text-blue-600 hover:text-blue-700 dark:text-blue-400">查看全部</a>
                </div>

                <div class="space-y-3">
                    @if(isset($skins) && $skins->isNotEmpty())
                    <div class="grid grid-cols-2 gap-3">
                        <div class="rounded-xl border border-slate-100 bg-slate-50 p-3 dark:border-slate-700 dark:bg-slate-800/60">
                            <p class="text-xs text-slate-500 dark:text-slate-400">饰品数量</p>
                            <p class="mt-1 text-lg font-bold text-slate-900 dark:text-slate-100">{{ $skins->count() }}</p>
                        </div>
                        <div class="rounded-xl border border-slate-100 bg-slate-50 p-3 dark:border-slate-700 dark:bg-slate-800/60">
                            <p class="text-xs text-slate-500 dark:text-slate-400">总估值</p>
                            <p class="mt-1 text-lg font-bold text-indigo-600 dark:text-indigo-400">¥{{ number_format($totalSkinValuation ?? 0, 2) }}</p>
                        </div>
                    </div>
                    @endif
                    <a href="{{ route('funds.skins.index') }}" class="flex items-center justify-between rounded-xl border border-slate-200 px-4 py-3 transition hover:bg-slate-50 dark:border-slate-700 dark:hover:bg-slate-800">
                        <span class="text-sm font-medium text-slate-700 dark:text-slate-200">虚拟产品估值一览表</span>
                        <span class="text-slate-400">&rarr;</span>
                    </a>
                    <a href="{{ route('funds.historical-earnings') }}" class="flex items-center justify-between rounded-xl border border-slate-200 px-4 py-3 transition hover:bg-slate-50 dark:border-slate-700 dark:hover:bg-slate-800">
                        <span class="text-sm font-medium text-slate-700 dark:text-slate-200">历史收益</span>
                        <span class="text-slate-400">&rarr;</span>
                    </a>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <div class="mb-4">
                    <h3 class="text-sm font-semibold text-slate-900 dark:text-slate-100">快捷入口</h3>
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
