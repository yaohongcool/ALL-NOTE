@extends('layouts.app', [
    'title' => '添加期限备忘 - 全录笔记',
    'headerTitle' => '添加期限备忘',
    'headerTxt' => '记录证件、会员、物品和其它事项的到期时间',
    'breadcrumb' => [
        ['label' => '首页', 'url' => route('dashboard')],
        ['label' => '期限备忘', 'url' => route('documents.index')],
        ['label' => '添加期限备忘'],
    ],
])
@section('content')
    <div class="max-w-4xl">
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <h2 class="text-lg font-semibold text-slate-900 dark:text-slate-100">登记期限备忘</h2>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                当前支持证件、会员、物品、其它
            </p>

            @include('documents._form', [
                'action' => route('documents.store'),
                'method' => 'POST',
                'submitText' => '保存期限备忘',
                'documentModel' => $document,
                'categories' => $categories,
            ])
        </div>
    </div>
@endsection
