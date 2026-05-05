<header class="sticky top-0 z-20 border-b border-slate-200 bg-white/90 backdrop-blur dark:border-slate-800 dark:bg-slate-900/90">
    <div class="flex h-16 items-center justify-between px-4 sm:px-6 lg:px-8">
        <div class="flex items-center gap-3">
            <button
                type="button"
                class="inline-flex h-10 w-10 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-600 transition hover:bg-slate-50 hover:text-slate-900 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-slate-100 lg:hidden"
                @click="sidebarOpen = true"
            >
                ☰
            </button>

            <div>
                <h1 class="text-lg font-semibold text-slate-900 dark:text-slate-100">
                    {{ $headerTitle ?? '首页' }}
                </h1>
                <p class="text-sm text-slate-500 dark:text-slate-400">
                     <!-- 一个简洁、安全（我不负责的哦）面向个人的密码、资产与期限备忘系统 -->
                    {{ $headerTxt ?? '副标题' }}
                </p>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <button
                type="button"
                @click="toggleTheme()"
                class="inline-flex items-center gap-2 rounded-2xl border border-slate-200 bg-white px-3 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-50 hover:text-slate-900 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-slate-100"
            >
                
                <span x-text="isDarkMode ? '深色' : '浅色'"></span>
            </button>

            @auth
                <div class="hidden items-center gap-3 rounded-2xl border border-slate-200 bg-white px-3 py-2 dark:border-slate-700 dark:bg-slate-900 sm:flex">
                    <div class="flex items-center gap-3">
                    <p class="text-sm font-medium text-slate-900 dark:text-slate-100">
                       {{ auth()->user()->username }}
                    </p>
                    <p class="text-xs text-slate-500 dark:text-slate-400">已登录</p>
                </div>
                </div>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button
                        type="submit"
                        class="inline-flex items-center gap-2 rounded-2xl border border-slate-200 bg-white px-3 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-50 hover:text-slate-900 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-slate-100"
                    >
                        退出
                    </button>
                </form>
            @endauth
        </div>
    </div>
</header>
