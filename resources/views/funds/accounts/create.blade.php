@extends('layouts.app', [
    'title' => '添加账户 - 全录笔记',
    'headerTitle' => '添加账户',
    'headerTxt' => '创建新的资金账户',
    'breadcrumb' => [
        ['label' => '首页', 'url' => route('dashboard')],
        ['label' => '资金记录', 'url' => route('funds.index')],
        ['label' => '账户管理', 'url' => route('funds.accounts.index')],
        ['label' => '添加账户'],
    ],
])

@section('content')
    <div class="max-w-4xl">
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <h2 class="text-lg font-semibold text-slate-900 dark:text-slate-100">创建账户</h2>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                添加现金、平台或虚拟资产账户
            </p>

            @include('funds.accounts._form', [
                'action' => route('funds.accounts.store'),
                'method' => 'POST',
                'submitText' => '保存账户',
                'account' => new \App\Models\FundAccount(['balance' => 0]),
            ])
        </div>
    </div>
@endsection
