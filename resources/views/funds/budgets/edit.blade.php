@extends('layouts.app', [
    'title' => '编辑预算 - 全录笔记',
    'headerTitle' => '编辑预算',
    'headerTxt' => '更新预算项目信息',
    'breadcrumb' => [
        ['label' => '首页', 'url' => route('dashboard')],
        ['label' => '资金记录', 'url' => route('funds.index')],
        ['label' => '预算管理', 'url' => route('funds.budgets.index')],
        ['label' => '编辑预算'],
    ],
])

@section('content')
    <div class="max-w-4xl">
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <h2 class="text-lg font-semibold text-slate-900 dark:text-slate-100">编辑预算</h2>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                更新预算金额等信息
            </p>

            @include('funds.budgets._form', [
                'action' => route('funds.budgets.update', $budget),
                'method' => 'PUT',
                'submitText' => '更新预算',
                'budget' => $budget,
            ])
        </div>
    </div>
@endsection
