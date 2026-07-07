@extends('layouts.app', [
    'title' => '登记收益 - 全录笔记',
    'headerTitle' => '登记累计收益',
    'headerTxt' => '为虚拟资产添加累计收益记录',
    'breadcrumb' => [
        ['label' => '首页', 'url' => route('dashboard')],
        ['label' => '资金记录', 'url' => route('funds.index')],
        ['label' => '虚拟资产', 'url' => route('funds.skins.index')],
        ['label' => $skin->name, 'url' => route('funds.skins.edit', $skin)],
        ['label' => '登记收益'],
    ],
])

@section('content')
    <div class="max-w-4xl">
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <h2 class="text-lg font-semibold text-slate-900 dark:text-slate-100">登记累计收益</h2>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                选择虚拟资产登记月度累计收益
            </p>

            @include('funds.skin_earnings._form', [
                'action' => route('funds.skin-earnings.store', $skin),
                'method' => 'POST',
                'submitText' => '保存收益记录',
                'earning' => $earning,
                'skins' => $skins,
                'isCreate' => true,
            ])
        </div>
    </div>
@endsection
