<!DOCTYPE html>
<html lang="zh-CN" x-data="appLayout()" x-init="init()" :class="{ 'dark': isDarkMode }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? '全录笔记' }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 text-slate-900 antialiased dark:bg-slate-950 dark:text-slate-100">
    <div class="min-h-screen">
        <div class="flex min-h-screen">
            <aside
                class="fixed inset-y-0 left-0 z-40 w-64 transform border-r border-slate-200 bg-white transition-transform duration-300 dark:border-slate-800 dark:bg-slate-900 lg:translate-x-0"
                :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
            >
                @include('partials.sidebar')
            </aside>

            <div
                class="fixed inset-0 z-30 bg-slate-950/40 lg:hidden"
                x-show="sidebarOpen"
                x-transition.opacity
                @click="sidebarOpen = false"
                style="display: none;"
            ></div>

            <div class="flex min-h-screen flex-1 flex-col lg:pl-64">
                @include('partials.header')

                <main class="flex-1 px-4 py-6 sm:px-6 lg:px-8">
                    @if (session('success'))
                        <div class="mb-4 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700 dark:border-emerald-900 dark:bg-emerald-950/40 dark:text-emerald-300">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="mb-4 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 dark:border-red-900 dark:bg-red-950/40 dark:text-red-300">
                            <p class="font-semibold">请检查以下问题：</p>
                            <ul class="mt-2 list-disc space-y-1 pl-5">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @isset($breadcrumb)
                        @if (is_array($breadcrumb) && count($breadcrumb) > 0)
                            <nav class="mb-5" aria-label="Breadcrumb">
                                <ol class="flex flex-wrap items-center gap-y-2 text-sm text-slate-500 dark:text-slate-400">
                                    @foreach ($breadcrumb as $index => $item)
                                        @php
                                            $isLast = $index === count($breadcrumb) - 1;
                                            $label = is_array($item) ? ($item['label'] ?? '') : $item;
                                            $url = is_array($item) ? ($item['url'] ?? null) : null;
                                        @endphp

                                        <li class="flex items-center">
                                            @if (! $isLast)
                                                @if ($url)
                                                    <a
                                                        href="{{ $url }}"
                                                        class="inline-flex items-center rounded-md bg-slate-100 px-2 py-1 font-medium text-slate-700 dark:bg-slate-800 dark:text-slate-200"
                                                    >
                                                        <span>{{ $label }}</span>
                                                        <span class="absolute inset-x-1 -bottom-0.5 h-1 rounded-full bg-blue-500/20 opacity-0 transition-opacity group-hover:opacity-100"></span>
                                                    </a>
                                                @else
                                                    <span class="inline-flex items-center px-1 py-0.5">
                                                        {{ $label }}
                                                    </span>
                                                @endif

                                                <span class="mx-2 select-none text-slate-300 dark:text-slate-600">/</span>
                                            @else
                                                <span >
                                                    {{ $label }}
                                                </span>
                                            @endif
                                        </li>
                                    @endforeach
                                </ol>
                            </nav>
                        @endif
                    @endisset

                    {{ $slot ?? '' }}

                    @yield('content')
                </main>

                @include('partials.footer')
            </div>
        </div>
    </div>

    <div
        x-data="toastCenter()"
        x-init="init()"
        class="pointer-events-none fixed left-4 right-4 top-4 z-[100] flex w-auto flex-col gap-3 sm:left-auto sm:right-6 sm:top-6 sm:w-full sm:max-w-sm"
    >
        <template x-for="toast in toasts" :key="toast.id">
            <div
                x-show="toast.visible"
                x-transition:enter="transform ease-out duration-200"
                x-transition:enter-start="translate-y-2 opacity-0"
                x-transition:enter-end="translate-y-0 opacity-100"
                x-transition:leave="transform ease-in duration-150"
                x-transition:leave-start="translate-y-0 opacity-100"
                x-transition:leave-end="translate-y-2 opacity-0"
                class="pointer-events-auto overflow-hidden rounded-2xl border bg-white shadow-lg dark:bg-slate-900"
                :class="toast.type === 'error'
                    ? 'border-red-200 dark:border-red-900'
                    : 'border-emerald-200 dark:border-emerald-900'"
                style="display: none;"
            >
                <div class="px-4 py-3">
                    <p
                        class="text-sm font-medium"
                        :class="toast.type === 'error'
                            ? 'text-red-700 dark:text-red-300'
                            : 'text-emerald-700 dark:text-emerald-300'"
                        x-text="toast.message"
                    ></p>
                </div>
            </div>
        </template>
    </div>
</body>
</html>
