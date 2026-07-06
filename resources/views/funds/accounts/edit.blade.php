@extends('layouts.app', [
    'title' => '编辑账户 - 全录笔记',
    'headerTitle' => '编辑账户',
    'headerTxt' => '更新账户信息',
    'breadcrumb' => [
        ['label' => '首页', 'url' => route('dashboard')],
        ['label' => '资金记录', 'url' => route('funds.index')],
        ['label' => '账户管理', 'url' => route('funds.accounts.index')],
        ['label' => '编辑账户'],
    ],
])

@section('content')
    <div class="max-w-4xl">
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <h2 class="text-lg font-semibold text-slate-900 dark:text-slate-100">编辑账户</h2>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                更新账户名称、余额等信息
            </p>

            @include('funds.accounts._form', [
                'action' => route('funds.accounts.update', $account),
                'method' => 'PUT',
                'submitText' => '更新账户',
                'account' => $account,
            ])
        </div>
    </div>
@endsection
