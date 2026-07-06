@extends('layouts.app', [
    'title' => '编辑收益 - 全录笔记',
    'headerTitle' => '编辑历史收益',
    'headerTxt' => '更新饰品历史收益记录',
    'breadcrumb' => [
        ['label' => '首页', 'url' => route('dashboard')],
        ['label' => '资金记录', 'url' => route('funds.index')],
        ['label' => '饰品管理', 'url' => route('funds.skins.index')],
        ['label' => $skin->name, 'url' => route('funds.skins.edit', $skin)],
        ['label' => '编辑收益'],
    ],
])

@section('content')
    <div class="max-w-4xl">
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <h2 class="text-lg font-semibold text-slate-900 dark:text-slate-100">编辑历史收益</h2>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                更新「{{ $skin->name }}」的历史收益记录
            </p>

            @include('funds.skin_earnings._form', [
                'action' => route('funds.skin-earnings.update', [$skin, $earning]),
                'method' => 'PUT',
                'submitText' => '更新收益记录',
                'earning' => $earning,
            ])
        </div>
    </div>
@endsection
