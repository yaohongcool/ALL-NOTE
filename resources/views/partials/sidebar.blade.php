<div class="flex h-full flex-col">
    <div class="flex h-16 items-center gap-3 border-slate-200 px-6 dark:border-slate-800">
       
        <div class="flex h-10 w-10 items-center justify-center">
            <img src="{{ asset('logo.png') }}" class="h-full w-full object-cover">
        </div>
        <div>
            <p class="text-sm font-semibold text-slate-900 dark:text-slate-100">全录笔记</p>
            <p class="text-xs text-slate-500 dark:text-slate-400">ALL NOTE</p>
        </div>
    </div>

    <nav class="flex-1 space-y-8 px-4 py-6">
        <div>
            <p class="mb-3 px-3 text-xs font-semibold uppercase tracking-wider text-slate-400">
                概览
            </p>
            <div class="space-y-1">
                <a
                    href="{{ route('dashboard') }}"
                    class="flex items-center gap-3 rounded-xl px-3 py-2 text-sm font-medium transition hover:bg-slate-100 hover:text-slate-900 dark:hover:bg-slate-800 dark:hover:text-slate-100 {{ request()->routeIs('dashboard') ? 'bg-blue-50 text-blue-700 dark:bg-slate-800 dark:text-blue-400' : 'text-slate-600 dark:text-slate-300' }}"
                >
                    <span>首页</span>
                </a>
            </div>
        </div>

        <div>
            <p class="mb-3 px-3 text-xs font-semibold uppercase tracking-wider text-slate-400">
                详细列表
            </p>
            <div class="space-y-1">
                <a
                    href="{{ route('passwords.index') }}"
                    class="flex items-center gap-3 rounded-xl px-3 py-2 text-sm font-medium transition hover:bg-slate-100 hover:text-slate-900 dark:hover:bg-slate-800 dark:hover:text-slate-100 {{ request()->routeIs('passwords.*') ? 'bg-blue-50 text-blue-700 dark:bg-slate-800 dark:text-blue-400' : 'text-slate-600 dark:text-slate-300' }}"
                >
                    <span>密码管理</span>
                </a>

                <a
                    href="{{ route('assets.index') }}"
                    class="flex items-center gap-3 rounded-xl px-3 py-2 text-sm font-medium transition hover:bg-slate-100 hover:text-slate-900 dark:hover:bg-slate-800 dark:hover:text-slate-100 {{ request()->routeIs('assets.*') ? 'bg-blue-50 text-blue-700 dark:bg-slate-800 dark:text-blue-400' : 'text-slate-600 dark:text-slate-300' }}"
                >
                    <span>资产管理</span>
                </a>

                <a
                    href="{{ route('documents.index') }}"
                    class="flex items-center gap-3 rounded-xl px-3 py-2 text-sm font-medium transition hover:bg-slate-100 hover:text-slate-900 dark:hover:bg-slate-800 dark:hover:text-slate-100 {{ request()->routeIs('documents.*') ? 'bg-blue-50 text-blue-700 dark:bg-slate-800 dark:text-blue-400' : 'text-slate-600 dark:text-slate-300' }}"
                >
                    <span>证件管理</span>
                </a>

                <a
                    href="{{ route('events.index') }}"
                    class="flex items-center gap-3 rounded-xl px-3 py-2 text-sm font-medium transition hover:bg-slate-100 hover:text-slate-900 dark:hover:bg-slate-800 dark:hover:text-slate-100 {{ request()->routeIs('events.*') ? 'bg-blue-50 text-blue-700 dark:bg-slate-800 dark:text-blue-400' : 'text-slate-600 dark:text-slate-300' }}"
                >
                    <span>事件记录</span>
                </a>
            </div>
        </div>
    </nav>


</div>
