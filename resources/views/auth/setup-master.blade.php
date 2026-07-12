<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>设置主密码 - 全录笔记</title>
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
                        设置主密码
                    </h1>
                    <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">
                        主密码用于加密你的密码记录，请妥善保管。<br>主密码丢失后将无法读取已存储的密码。
                    </p>
                </div>

                @if (session('warning'))
                    <div class="mt-6 rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-700 dark:border-amber-900 dark:bg-amber-950/40 dark:text-amber-300">
                        {{ session('warning') }}
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

                <form method="POST" action="{{ route('master-password.setup.store') }}" class="mt-6 space-y-5">
                    @csrf

                    <div>
                        <label for="master_password" class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-200">
                            主密码
                        </label>
                        <div class="relative">
                            <input
                                id="master_password"
                                name="master_password"
                                type="password"
                                class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 pr-16 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-100 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:focus:border-blue-500 dark:focus:ring-blue-950"
                                placeholder="请输入主密码"
                            >
                            <button
                                type="button"
                                data-toggle-password
                                data-target="master_password"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-xs font-medium text-slate-500 transition hover:text-slate-900 dark:text-slate-400 dark:hover:text-slate-100"
                            >
                                显示
                            </button>
                        </div>
                    </div>

                    <div>
                        <label for="master_password_confirmation" class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-200">
                            确认主密码
                        </label>
                        <div class="relative">
                            <input
                                id="master_password_confirmation"
                                name="master_password_confirmation"
                                type="password"
                                class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 pr-16 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-100 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:focus:border-blue-500 dark:focus:ring-blue-950"
                                placeholder="请再次输入主密码"
                            >
                            <button
                                type="button"
                                data-toggle-password
                                data-target="master_password_confirmation"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-xs font-medium text-slate-500 transition hover:text-slate-900 dark:text-slate-400 dark:hover:text-slate-100"
                            >
                                显示
                            </button>
                        </div>
                    </div>

                    <div class="rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-700 dark:border-amber-900 dark:bg-amber-950/40 dark:text-amber-300">
                        <p class="font-semibold">⚠ 重要提示</p>
                        <ul class="mt-1 list-disc space-y-1 pl-5">
                            <li>主密码与登录密码不同，用于加密/解密你的密码记录</li>
                            <li>设置后，所有现有密码记录将自动重新加密</li>
                            <li>主密码一旦丢失，已存储的密码将无法读取</li>
                            <li>建议与登录密码使用不同的密码</li>
                        </ul>
                    </div>

                    <button
                        type="submit"
                        class="inline-flex w-full items-center justify-center rounded-2xl bg-blue-600 px-4 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700"
                    >
                        设置主密码
                    </button>
                </form>

                <div class="mt-6 flex items-center justify-center text-sm">
                    <a href="{{ route('dashboard') }}" class="text-slate-500 transition hover:text-slate-900 dark:text-slate-400 dark:hover:text-slate-100">
                        返回首页
                    </a>
                </div>
            </div>
        </div>

        @include('partials.footer')
    </div>
</body>
</html>
