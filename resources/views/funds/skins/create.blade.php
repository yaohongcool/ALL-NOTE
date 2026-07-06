@extends('layouts.app', [
    'title' => '添加虚拟资产 - 全录笔记',
    'headerTitle' => '添加虚拟资产',
    'headerTxt' => '添加游戏虚拟资产信息',
    'breadcrumb' => [
        ['label' => '首页', 'url' => route('dashboard')],
        ['label' => '资金记录', 'url' => route('funds.index')],
        ['label' => '虚拟资产', 'url' => route('funds.skins.index')],
        ['label' => '添加虚拟资产'],
    ],
])

@section('content')
    <div class="max-w-4xl">
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <h2 class="text-lg font-semibold text-slate-900 dark:text-slate-100">创建虚拟资产</h2>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                添加游戏虚拟资产，记录成本、平台价格及日租金
            </p>

            @include('funds.skins._form', [
                'action' => route('funds.skins.store'),
                'method' => 'POST',
                'submitText' => '保存虚拟资产',
                'skin' => new \App\Models\FundSkin(['uu_fee_rate' => 0.02, 'buff_fee_rate' => 0.025]),
            ])
        </div>
    </div>
@endsection
