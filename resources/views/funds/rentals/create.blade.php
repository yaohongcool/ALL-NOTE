@extends('layouts.app', [
    'title' => '添加租赁记录 - 全录笔记',
    'headerTitle' => '添加租赁记录',
    'headerTxt' => '为饰品添加租赁记录',
    'breadcrumb' => [
        ['label' => '首页', 'url' => route('dashboard')],
        ['label' => '资金记录', 'url' => route('funds.index')],
        ['label' => '饰品管理', 'url' => route('funds.skins.index')],
        ['label' => $skin->name, 'url' => route('funds.skins.show', $skin)],
        ['label' => '添加租赁记录'],
    ],
])

@section('content')
    <div class="max-w-4xl">
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <h2 class="text-lg font-semibold text-slate-900 dark:text-slate-100">创建租赁记录</h2>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                为饰品「{{ $skin->name }}」添加租赁记录
            </p>

            @include('funds.rentals._form', [
                'action' => route('funds.rentals.store', $skin),
                'method' => 'POST',
                'submitText' => '保存租赁记录',
                'rental' => new \App\Models\FundRental(['rate' => 3.8, 'discount' => 0.8, 'offhand_days' => 8, 'fee_rate' => 0.99]),
            ])
        </div>
    </div>
@endsection
