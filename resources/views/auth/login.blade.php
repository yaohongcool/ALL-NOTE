<!DOCTYPE html>
<html lang="zh-CN" x-data="appLayout()" x-init="init()" :class="{ 'dark': isDarkMode }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>登录 - 全录笔记</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 text-slate-900 antialiased dark:bg-slate-950 dark:text-slate-100">
    <div class="flex min-h-screen flex-col">
        <div class="flex flex-1 items-center justify-center px-4 py-10">
            <div class="w-full max-w-md rounded-3xl border border-slate-200 bg-white p-8 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <div class="text-center">
                    <div class="mx-auto flex h-16 w-16 items-center justify-center">
                        <img src="{{ asset('logo.png') }}"  class="w-full h-full object-cover">
                    </div>
                     <h1 class="mt-5 text-2xl font-bold tracking-tight text-slate-900 dark:text-slate-100">
                        登录全录笔记
                    </h1>
                    <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">
                        输入用户名和密码以继续访问系统
                    </p>
                </div>

                @if (session('success'))
                    <div class="mt-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700 dark:border-emerald-900 dark:bg-emerald-950/40 dark:text-emerald-300">
                        {{ session('success') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mt-6 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 dark:border-red-900 dark:bg-red-950/40 dark:text-red-300">
                        <ul class="space-y-1 list-disc pl-5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('login.attempt') }}" class="mt-6 space-y-5">
                    @csrf

                    <div>
                        <label for="username" class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-200">
                            用户名
                        </label>
                        <input
                            id="username"
                            name="username"
                            type="text"
                            value="{{ old('username') }}"
                            class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-100 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:focus:border-blue-500 dark:focus:ring-blue-950"
                            placeholder="请输入用户名"
                        >
                    </div>

                    <div>
                        <label for="password" class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-200">
                            密码
                        </label>
                        <div class="relative">
                            <input
                                id="password"
                                name="password"
                                type="password"
                                class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 pr-16 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-100 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:focus:border-blue-500 dark:focus:ring-blue-950"
                                placeholder="请输入密码"
                            >
                            <button
                                type="button"
                                data-toggle-password
                                data-target="password"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-xs font-medium text-slate-500 transition hover:text-slate-900 dark:text-slate-400 dark:hover:text-slate-100"
                            >
                                显示
                            </button>
                        </div>
                    </div>

                    <div class="flex items-center justify-between gap-3">
                        <label class="flex items-center gap-3 text-sm text-slate-600 dark:text-slate-300">
                            <input
                                type="checkbox"
                                name="remember"
                                value="1"
                                class="h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500 dark:border-slate-700 dark:bg-slate-950"
                            >
                            <span>记住登录状态</span>
                        </label>
                        <a href="{{ route('password.change') }}" class="text-sm font-medium text-blue-600 transition hover:text-blue-700">
                            更改密码
                        </a>
                    </div>

                    <button
                        type="submit"
                        class="inline-flex w-full items-center justify-center rounded-2xl bg-blue-600 px-4 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700"
                    >
                        登录
                    </button>
                </form>

                <div class="mt-6 flex items-center justify-between text-sm">
                    <a href="{{ route('home') }}" class="text-slate-500 transition hover:text-slate-900 dark:text-slate-400 dark:hover:text-slate-100">
                        返回首页
                    </a>
                    <a href="{{ route('register') }}" class="font-medium text-blue-600 transition hover:text-blue-700">
                        去注册
                    </a>
                </div>
            </div>
        </div>

        @include('partials.footer')
    </div>

    <script>
        document.querySelectorAll('[data-toggle-password]').forEach((button) => {
            button.addEventListener('click', () => {
                const input = document.getElementById(button.dataset.target);
                if (!input) return;

                const show = input.type === 'password';
                input.type = show ? 'text' : 'password';
                button.textContent = show ? '隐藏' : '显示';
            });
        });
    </script>
</body>
</html>
