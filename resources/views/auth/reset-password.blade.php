<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>重置密码 - 全录笔记</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script>
        (function(){var t=localStorage.getItem('theme'),p=t?t==='dark':window.matchMedia('(prefers-color-scheme: dark)').matches;if(p)document.documentElement.classList.add('dark');})();
    </script>
</head>
<body class="bg-slate-50 text-slate-900 antialiased dark:bg-slate-950 dark:text-slate-100">
    <div class="flex min-h-screen flex-col">
        <div class="flex flex-1 items-center justify-center px-4 py-10">
            <div class="w-full max-w-md rounded-3xl border border-slate-200 bg-white p-8 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <div class="text-center">
                    <h1 class="mt-2 text-2xl font-bold tracking-tight text-slate-900 dark:text-slate-100">
                        重置密码
                    </h1>
                    <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">
                        为账号 <strong class="text-slate-900 dark:text-slate-100">{{ $username }}</strong> 设置新登录密码
                    </p>
                </div>

                @if ($errors->any())
                    <div class="mt-6 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 dark:border-red-900 dark:bg-red-950/40 dark:text-red-300">
                        <ul class="space-y-1 list-disc pl-5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('password.reset.update') }}" class="mt-6 space-y-5">
                    @csrf

                    <input type="hidden" name="username" value="{{ $username }}">

                    <div>
                        <label for="password" class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-200">
                            新密码
                        </label>
                        <div class="relative">
                            <input
                                id="password"
                                name="password"
                                type="password"
                                class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 pr-16 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-100 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:focus:border-blue-500 dark:focus:ring-blue-950"
                                placeholder="请输入新密码"
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

                    <div>
                        <label for="password_confirmation" class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-200">
                            确认新密码
                        </label>
                        <div class="relative">
                            <input
                                id="password_confirmation"
                                name="password_confirmation"
                                type="password"
                                class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 pr-16 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-100 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:focus:border-blue-500 dark:focus:ring-blue-950"
                                placeholder="请再次输入新密码"
                            >
                            <button
                                type="button"
                                data-toggle-password
                                data-target="password_confirmation"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-xs font-medium text-slate-500 transition hover:text-slate-900 dark:text-slate-400 dark:hover:text-slate-100"
                            >
                                显示
                            </button>
                        </div>
                    </div>

                    <button
                        type="submit"
                        class="inline-flex w-full items-center justify-center rounded-2xl bg-blue-600 px-4 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700"
                    >
                        重置密码
                    </button>
                </form>

                <div class="mt-6 flex items-center justify-center text-sm">
                    <a href="{{ route('login') }}" class="text-slate-500 transition hover:text-slate-900 dark:text-slate-400 dark:hover:text-slate-100">
                        返回登录
                    </a>
                </div>
            </div>
        </div>

        @include('partials.footer')
    </div>
</body>
</html>
