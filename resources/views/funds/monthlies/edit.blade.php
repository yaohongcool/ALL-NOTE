@extends('layouts.app', [
    'title' => '编辑月度记录 - 全录笔记',
    'headerTitle' => '编辑月度记录',
    'headerTxt' => '更新月度收支和存款数据',
    'breadcrumb' => [
        ['label' => '首页', 'url' => route('dashboard')],
        ['label' => '资金记录', 'url' => route('funds.index')],
        ['label' => '月度记录', 'url' => route('funds.monthlies.index')],
        ['label' => '编辑月度记录'],
    ],
])

@section('content')
    <div class="max-w-4xl">
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <h2 class="text-lg font-semibold text-slate-900 dark:text-slate-100">编辑月度记录</h2>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                更新月度收支和存款信息
            </p>

            @include('funds.monthlies._form', [
                'action' => route('funds.monthlies.update', $monthly),
                'method' => 'PUT',
                'submitText' => '更新月度记录',
                'monthly' => $monthly,
            ])
        </div>
    </div>
@endsection
