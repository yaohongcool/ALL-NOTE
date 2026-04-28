@extends('layouts.app', [
    'title' => '编辑证件 - 全录笔记',
    'headerTitle' => '编辑证件',
    'headerTxt' => '记录个人证件有效时间',
    'breadcrumb' => [
        ['label' => '首页', 'url' => route('dashboard')],
        ['label' => '证件管理', 'url' => route('documents.index')],
        ['label' => '编辑证件'],
    ],
])
@section('content')
    <div class="max-w-4xl">
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <h2 class="text-lg font-semibold text-slate-900 dark:text-slate-100">编辑证件</h2>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                证件状态会根据到期日期自动判断。
            </p>

            @include('documents._form', [
                'action' => route('documents.update', $document),
                'method' => 'PUT',
                'submitText' => '更新证件',
                'documentModel' => $document,
                'categories' => $categories,
            ])
        </div>
    </div>
@endsection