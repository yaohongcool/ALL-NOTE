@extends('layouts.app', [
    'title' => '添加密码 - 全录笔记',
    'headerTitle' => '添加密码',
    'headerTxt' => '存储各类账号密码，支持一键复制，密码已加密存储',
    'breadcrumb' => [
        ['label' => '首页', 'url' => route('dashboard')],
        ['label' => '密码列表', 'url' => route('passwords.index')],
        ['label' => '添加密码'],
    ],
])
@section('content')
    <div class="max-w-3xl">
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <h2 class="text-lg font-semibold text-slate-900 dark:text-slate-100">创建密码记录</h2>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                所有密码内容将加密存储
            </p>

            @include('passwords._form', [
                'action' => route('passwords.store'),
                'method' => 'POST',
                'submitText' => '保存密码',
                'passwordModel' => $password,
                'revealUrl' => null,
                'passwordRequired' => true,
            ])
        </div>
    </div>
@endsection
