@extends('layouts.app', [
    'title' => '编辑饰品 - 全录笔记',
    'headerTitle' => '编辑饰品',
    'headerTxt' => '更新饰品价格信息',
    'breadcrumb' => [
        ['label' => '首页', 'url' => route('dashboard')],
        ['label' => '资金记录', 'url' => route('funds.index')],
        ['label' => '饰品管理', 'url' => route('funds.skins.index')],
        ['label' => '编辑饰品'],
    ],
])

@section('content')
    <div class="max-w-4xl">
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <h2 class="text-lg font-semibold text-slate-900 dark:text-slate-100">编辑饰品</h2>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                更新成本、平台价格和日租金等信息
            </p>

            @include('funds.skins._form', [
                'action' => route('funds.skins.update', $skin),
                'method' => 'PUT',
                'submitText' => '更新饰品',
                'skin' => $skin,
            ])
        </div>
    </div>
@endsection
