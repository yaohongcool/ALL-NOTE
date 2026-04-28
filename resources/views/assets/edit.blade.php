@extends('layouts.app', [
    'title' => '编辑资产 - 全录笔记',
    'headerTitle' => '编辑资产',
    'headerTxt' => '存储物理设备和数字资产信息',
    'breadcrumb' => [
        ['label' => '首页', 'url' => route('dashboard')],
        ['label' => '资产列表', 'url' => route('assets.index')],
        ['label' => '编辑资产'],
    ],
])
@section('content')
    <div class="max-w-4xl">
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <h2 class="text-lg font-semibold text-slate-900 dark:text-slate-100">编辑资产记录</h2>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                更新资产信息
            </p>

            @include('assets._form', [
                'action' => route('assets.update', $asset),
                'method' => 'PUT',
                'submitText' => '更新资产',
                'assetModel' => $asset,
                'categories' => $categories,
            ])
        </div>
    </div>
@endsection