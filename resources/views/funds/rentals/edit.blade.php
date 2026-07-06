@extends('layouts.app', [
    'title' => '编辑租赁记录 - 全录笔记',
    'headerTitle' => '编辑租赁记录',
    'headerTxt' => '更新租赁记录信息',
    'breadcrumb' => [
        ['label' => '首页', 'url' => route('dashboard')],
        ['label' => '资金记录', 'url' => route('funds.index')],
        ['label' => '虚拟资产', 'url' => route('funds.skins.index')],
        ['label' => $skin->name, 'url' => route('funds.skins.edit', $skin)],
        ['label' => '编辑租赁记录'],
    ],
])

@section('content')
    <div class="max-w-4xl">
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <h2 class="text-lg font-semibold text-slate-900 dark:text-slate-100">编辑租赁记录</h2>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                更新「{{ $skin->name }}」的租赁记录
            </p>

            @include('funds.rentals._form', [
                'action' => route('funds.rentals.update', [$skin, $rental]),
                'method' => 'PUT',
                'submitText' => '更新租赁记录',
                'rental' => $rental,
            ])
        </div>
    </div>
@endsection
