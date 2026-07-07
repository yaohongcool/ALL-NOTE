@extends('layouts.app', [
    'title' => '租赁记录 - 全录笔记',
    'headerTitle' => '租赁记录',
    'headerTxt' => '',
    'breadcrumb' => [
        ['label' => '首页', 'url' => route('dashboard')],
        ['label' => '资金记录', 'url' => route('funds.index')],
        ['label' => '租赁记录'],
    ],
])

@section('content')
    <div class="space-y-6">
        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <p class="text-center text-sm text-slate-500 dark:text-slate-400">暂无数据</p>
        </section>
    </div>
@endsection
