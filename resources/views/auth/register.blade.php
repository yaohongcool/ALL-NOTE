<!DOCTYPE html>
<html lang="zh-CN" x-data="registerPage()" x-init="init()" :class="{ 'dark': isDarkMode }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>注册 - 全录笔记</title>
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
                        创建账户
                    </h1>
                    <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">
                        创建你的全录笔记账号
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

                <form method="POST" action="{{ route('register.store') }}" class="mt-6 space-y-5">
                    @csrf

                    <div>
                        <label for="username" class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-200">
                            创建用户
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
                            创建密码
                        </label>
                        <div class="relative">
                            <input
                                id="password"
                                name="password"
                                type="password"
                                x-model="password"
                                @input="updateStrength()"
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

                        <div class="mt-3">
                            <div class="flex items-center justify-between text-xs">
                                <span class="text-slate-500 dark:text-slate-400">密码强度</span>
                                <span class="font-medium" :class="strengthTextClass" x-text="strengthText"></span>
                            </div>

                            <div class="mt-2 h-2 w-full overflow-hidden rounded-full bg-slate-200 dark:bg-slate-800">
                                <div
                                    class="h-2 rounded-full transition-all duration-300"
                                    :class="strengthBarClass"
                                    :style="`width: ${strengthWidth}`"
                                ></div>
                            </div>

                            <p class="mt-2 text-xs leading-5 text-slate-500 dark:text-slate-400">
                                密码长度至少 8 位，且至少包含大写字母、小写字母、数字、特殊字符中的 3 类。
                            </p>
                        </div>
                    </div>

                    <div>
                        <label for="password_confirmation" class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-200">
                            确认密码
                        </label>
                        <div class="relative">
                            <input
                                id="password_confirmation"
                                name="password_confirmation"
                                type="password"
                                x-model="passwordConfirmation"
                                class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 pr-16 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-100 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:focus:border-blue-500 dark:focus:ring-blue-950"
                                placeholder="请再次输入密码"
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

                        <p
                            class="mt-2 text-sm"
                            x-show="passwordConfirmation.length > 0"
                            :class="passwordsMatch ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400'"
                            x-text="passwordsMatch ? '两次密码输入一致' : '两次密码输入不一致'"
                            style="display: none;"
                        ></p>
                    </div>

                    <button
                        type="submit"
                        class="inline-flex w-full items-center justify-center rounded-2xl bg-blue-600 px-4 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700"
                    >
                        注册并登录
                    </button>
                </form>

                <div class="mt-6 flex items-center justify-between text-sm">
                    <a href="{{ route('home') }}" class="text-slate-500 transition hover:text-slate-900 dark:text-slate-400 dark:hover:text-slate-100">
                        返回首页
                    </a>
                    <a href="{{ route('login') }}" class="font-medium text-blue-600 transition hover:text-blue-700">
                        去登录
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
