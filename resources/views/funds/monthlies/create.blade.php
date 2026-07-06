@extends('layouts.app', [
    'title' => '添加月度记录 - 全录笔记',
    'headerTitle' => '添加月度记录',
    'headerTxt' => '记录单月收支和存款情况',
    'breadcrumb' => [
        ['label' => '首页', 'url' => route('dashboard')],
        ['label' => '资金记录', 'url' => route('funds.index')],
        ['label' => '月度记录', 'url' => route('funds.monthlies.index')],
        ['label' => '添加月度记录'],
    ],
])

@section('content')
    <div class="max-w-4xl">
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <h2 class="text-lg font-semibold text-slate-900 dark:text-slate-100">创建月度记录</h2>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                记录当月收入、支出和存款进度
            </p>

            @include('funds.monthlies._form', [
                'action' => route('funds.monthlies.store'),
                'method' => 'POST',
                'submitText' => '保存月度记录',
                'monthly' => new \App\Models\FundMonthly([
                    'month' => now()->format('Y-m'),
                    'savings_target' => $defaultSavingsTarget ?? 0,
                ]),
            ])
        </div>
    </div>
@endsection
