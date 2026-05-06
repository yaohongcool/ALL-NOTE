<!DOCTYPE html>
<html lang="zh-CN" x-data="appLayout()" x-init="init()" :class="{ 'dark': isDarkMode }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>全录笔记</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
</head>
<body class="bg-slate-50 text-slate-900 antialiased dark:bg-slate-950 dark:text-slate-100">
    <div class="flex min-h-screen flex-col">
        <div class="flex flex-1 items-center justify-center px-6">
            <div class="w-full max-w-3xl rounded-3xl border border-slate-200 bg-white p-8 shadow-sm dark:border-slate-800 dark:bg-slate-900 md:p-12">
                <div class="mx-auto max-w-2xl text-center">
                    <div class="mx-auto flex h-18 w-18 items-center justify-center rounded-full">
                        <img src="{{ asset('logo.png') }}"  class="w-full h-full object-cover">
                    </div>

                    <h1 class="mt-6 text-3xl font-bold tracking-tight text-slate-900 dark:text-slate-100 md:text-4xl">
                        全录笔记
                    </h1>
                    <h1 class="mt-1 text-xl font-bold tracking-tight text-slate-600 dark:text-slate-300 md:text-2xl">
                        ALL NOTE
                    </h1>
                    <p class="mt-4 text-base leading-7 text-slate-600 dark:text-slate-300">
                        一个简洁、安全，面向个人的存储账号密码、期限备忘、事件记录的统一管理工具
                    </p>

                    <div class="mt-8 flex flex-col items-center justify-center gap-3 sm:flex-row">
                        <a
                            href="{{ route('login') }}"
                            class="inline-flex items-center justify-center rounded-2xl bg-blue-600 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700"
                        >
                            登录系统
                        </a>

                        <a
                            href="{{ route('register') }}"
                            class="inline-flex items-center justify-center rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200 dark:hover:bg-slate-800"
                        >
                            注册账户
                        </a>
                    </div>

                    <div class="mt-10 grid grid-cols-1 gap-4 text-left md:grid-cols-3">
                        <div class="rounded-2xl bg-slate-50 p-4 dark:bg-slate-800/60">
                            <p class="text-sm font-semibold text-slate-900 dark:text-slate-100">信息管理</p>
                            <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">各类账密存储、一键复制。<br>时间记录，期限备忘。</p>
                        </div>

                        <div class="rounded-2xl bg-slate-50 p-4 dark:bg-slate-800/60">
                            <p class="text-sm font-semibold text-slate-900 dark:text-slate-100">事件记录</p>
                            <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">图文记录事件处理过程。<br>积累知识库。</p>
                        </div>

                        <div class="rounded-2xl bg-slate-50 p-4 dark:bg-slate-800/60">
                            <p class="text-sm font-semibold text-slate-900 dark:text-slate-100">加密存储</p>
                            <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">密码使用AES-256加密。<br>数据用户隔离。</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @include('partials.footer')
    </div>
</body>
</html>
