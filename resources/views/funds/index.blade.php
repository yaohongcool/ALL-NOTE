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
    <div class="space-y-6">
        <section class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <p class="text-sm font-medium text-slate-500 dark:text-slate-400">总资产金额</p>
                <p class="mt-3 text-3xl font-bold tracking-tight text-slate-900 dark:text-slate-100">
                    ¥{{ number_format($totalBalance ?? 0, 2) }}
                </p>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <p class="text-sm font-medium text-slate-500 dark:text-slate-400">本月收入</p>
                <p class="mt-3 text-3xl font-bold tracking-tight text-emerald-600 dark:text-emerald-400">
                    ¥{{ number_format($currentMonthIncome ?? 0, 2) }}
                </p>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <p class="text-sm font-medium text-slate-500 dark:text-slate-400">本月支出</p>
                <p class="mt-3 text-3xl font-bold tracking-tight text-red-600 dark:text-red-400">
                    ¥{{ number_format($currentMonthExpense ?? 0, 2) }}
                </p>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <p class="text-sm font-medium text-slate-500 dark:text-slate-400">本月结余</p>
                <p class="mt-3 text-3xl font-bold tracking-tight text-blue-600 dark:text-blue-400">
                    ¥{{ number_format(($currentMonthIncome ?? 0) - ($currentMonthExpense ?? 0), 2) }}
                </p>
            </div>
        </section>

        <section class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <div class="mb-4 flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-slate-900 dark:text-slate-100">账户列表</h3>
                    <a href="{{ route('funds.accounts.index') }}" class="text-xs font-medium text-blue-600 hover:text-blue-700 dark:text-blue-400">查看全部</a>
                </div>

                @if(isset($accounts) && $accounts->isNotEmpty())
                    <div class="space-y-2">
                        @foreach($accounts->take(5) as $account)
                            <div class="flex items-center justify-between rounded-xl border border-slate-100 px-3 py-2 dark:border-slate-800">
                                <span class="text-sm font-medium text-slate-700 dark:text-slate-200">{{ $account->name }}</span>
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
                    <h3 class="text-sm font-semibold text-slate-900 dark:text-slate-100">最近月份</h3>
                    <a href="{{ route('funds.monthlies.index') }}" class="text-xs font-medium text-blue-600 hover:text-blue-700 dark:text-blue-400">查看全部</a>
                </div>

                @if(isset($recentMonthlies) && $recentMonthlies->isNotEmpty())
                    <div class="space-y-2">
                        @foreach($recentMonthlies->take(6) as $monthly)
                            <div class="flex items-center justify-between rounded-xl border border-slate-100 px-3 py-2 dark:border-slate-800">
                                <span class="text-sm font-medium text-slate-700 dark:text-slate-200">{{ $monthly->month }}</span>
                                <div class="flex items-center gap-3">
                                    <span class="text-xs text-emerald-600 dark:text-emerald-400">收入 ¥{{ number_format($monthly->income, 2) }}</span>
                                    <span class="text-xs text-red-600 dark:text-red-400">支出 ¥{{ number_format($monthly->expense, 2) }}</span>
                                    <span class="text-xs font-semibold text-slate-700 dark:text-slate-300">净额 ¥{{ number_format($monthly->income - $monthly->expense, 2) }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="py-4 text-center text-sm text-slate-500 dark:text-slate-400">暂无月度记录</p>
                @endif

                <div class="mt-4">
                    <a href="{{ route('funds.monthlies.index') }}" class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200 dark:hover:bg-slate-800">
                        管理月度记录
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

                    <a href="{{ route('funds.skins.index') }}" class="flex items-center justify-between rounded-xl border border-slate-200 px-4 py-3 transition hover:bg-slate-50 dark:border-slate-700 dark:hover:bg-slate-800">
                        <div>
                            <p class="text-sm font-medium text-slate-700 dark:text-slate-200">饰品管理</p>
                            <p class="text-xs text-slate-500 dark:text-slate-400">管理游戏饰品及租赁</p>
                        </div>
                        <span class="text-slate-400">&rarr;</span>
                    </a>
                </div>
            </div>
        </section>
    </div>
@endsection
