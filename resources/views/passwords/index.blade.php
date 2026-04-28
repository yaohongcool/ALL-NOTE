@extends('layouts.app', [
    'title' => '密码列表 - 全录笔记',
    'headerTitle' => '密码列表',
    'headerTxt' => '存储各类账号密码，支持一键复制，密码已加密存储',
    'breadcrumb' => [
        ['label' => '首页', 'url' => route('dashboard')],
        ['label' => '密码列表'],
    ],
])

@section('content')
    <div class="space-y-6">
        <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                <form method="GET" action="{{ route('passwords.index') }}" class="grid flex-1 grid-cols-1 gap-4 md:grid-cols-2">
                    <div>
                        <select
                            id="sort"
                            name="sort"
                            onchange="this.form.submit()"
                            class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:focus:border-blue-500 dark:focus:ring-blue-950"
                        >
                            <option value="updated_desc" @selected($sort === 'updated_desc')>最近修改（新到旧）</option>
                            <option value="updated_asc" @selected($sort === 'updated_asc')>最近修改（旧到新）</option>
                            <option value="name_asc" @selected($sort === 'name_asc')>名称（A-Z）</option>
                            <option value="name_desc" @selected($sort === 'name_desc')>名称（Z-A）</option>
                        </select>
                    </div>

                    <div class="flex flex-wrap items-end gap-3">
                        <a
                            href="{{ route('passwords.index') }}"
                            class="inline-flex items-center justify-center rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-medium text-slate-700 transition hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200 dark:hover:bg-slate-800"
                        >
                            重置
                        </a>
                    </div>
                </form>

                <div class="flex-shrink-0">
                    <a
                        href="{{ route('passwords.create') }}"
                        class="inline-flex items-center justify-center rounded-2xl bg-blue-600 px-4 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700"
                    >
                        添加密码
                    </a>
                </div>
            </div>
        </section>

        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <div class="responsive-table-wrap overflow-x-auto">
                <table class="responsive-table min-w-full divide-y divide-slate-200 dark:divide-slate-800">
                    <thead class="bg-slate-50 dark:bg-slate-800/60">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">名称</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">账号</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">密码</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">绑定手机/邮箱</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">备注</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">最后修改</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">操作</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                        @forelse ($passwords as $password)
                            <tr
                                class="hover:bg-slate-50 dark:hover:bg-slate-800/40"
                                x-data="passwordRow({{ (int) $password->id }}, '{{ route('passwords.reveal', $password) }}')"
                            >
                                <td data-label="名称" class="px-4 py-4 align-middle">
                                    <p class="text-sm font-semibold text-slate-900 dark:text-slate-100">
                                        {{ $password->name }}
                                    </p>
                                </td>

                                <td data-label="账号" class="px-4 py-4 align-middle">
                                    <div class="flex items-center gap-2">
                                        <span class="text-sm text-slate-700 dark:text-slate-200">{{ $password->account }}</span>
                                        <button
                                            type="button"
                                            @click="copyTextWithToast(@js($password->account), '账号已复制到剪贴板。')"
                                            class="rounded-lg border border-slate-200 px-2 py-1 text-xs text-slate-600 transition hover:bg-slate-100 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800"
                                        >
                                            复制
                                        </button>
                                    </div>
                                </td>

                                <td data-label="密码" class="px-4 py-4 align-middle">
                                    <div class="flex items-center gap-2">
                                        <span class="text-sm text-slate-700 dark:text-slate-200" x-text="revealed ? plainPassword : maskedText"></span>

                                        <button
                                            type="button"
                                            class="rounded-lg border border-slate-200 px-2 py-1 text-xs text-slate-600 transition hover:bg-slate-100 disabled:cursor-not-allowed disabled:opacity-60 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800"
                                            :disabled="loading"
                                            @click="handleReveal()"
                                        >
                                            <span x-text="loading ? '读取中...' : (revealed ? '已查看' : '查看')"></span>
                                        </button>

                                        <button
                                            type="button"
                                            class="rounded-lg border border-slate-200 px-2 py-1 text-xs text-slate-600 transition hover:bg-slate-100 disabled:cursor-not-allowed disabled:opacity-60 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800"
                                            :disabled="loading"
                                            @click="handleCopy()"
                                        >
                                            复制
                                        </button>
                                    </div>
                                </td>

                                <td data-label="绑定手机/邮箱" class="px-4 py-4 align-middle">
                                    @if(!$password->phone && !$password->email)
                                        <span class="text-sm text-slate-600 dark:text-slate-300">-</span>
                                    @else
                                        <div class="space-y-2">
                                            @if($password->phone)
                                                <div class="flex items-center gap-2">
                                                    <span class="text-sm text-slate-700 dark:text-slate-200">
                                                        {{ $password->phone }}
                                                    </span>
                                                    <button
                                                        type="button"
                                                        @click="copyTextWithToast(@js($password->phone), '绑定手机已复制到剪贴板。')"
                                                        class="rounded-lg border border-slate-200 px-2 py-1 text-xs text-slate-600 transition hover:bg-slate-100 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800"
                                                    >
                                                        复制
                                                    </button>
                                                </div>
                                            @endif

                                            @if($password->email)
                                                <div class="flex items-center gap-2">
                                                    <span class="text-sm text-slate-700 dark:text-slate-200">
                                                        {{ $password->email }}
                                                    </span>
                                                    <button
                                                        type="button"
                                                        @click="copyTextWithToast(@js($password->email), '绑定邮箱已复制到剪贴板。')"
                                                        class="rounded-lg border border-slate-200 px-2 py-1 text-xs text-slate-600 transition hover:bg-slate-100 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800"
                                                    >
                                                        复制
                                                    </button>
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                </td>

                                <td data-label="备注" class="px-4 py-4 align-middle">
                                    <span class="text-sm text-slate-600 dark:text-slate-300">
                                        {{ $password->note ?: '-' }}
                                    </span>
                                </td>

                                <td data-label="最后修改" class="px-4 py-4 align-middle">
                                    <span class="text-sm text-slate-600 dark:text-slate-300">
                                        {{ $password->updated_at?->format('Y-m-d H:i') }}
                                    </span>
                                </td>

                                <td data-label="操作" class="px-4 py-4 align-middle">
                                    <div class="flex justify-end gap-2">
                                        <a
                                            href="{{ route('passwords.edit', $password) }}"
                                            class="rounded-xl border border-slate-200 px-3 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-100 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800"
                                        >
                                            编辑
                                        </a>

                                        <form method="POST" action="{{ route('passwords.destroy', $password) }}" onsubmit="return confirm('确定删除这条密码记录吗？');">
                                            @csrf
                                            @method('DELETE')
                                            <button
                                                type="submit"
                                                class="rounded-xl border border-red-200 px-3 py-2 text-sm font-medium text-red-600 transition hover:bg-red-50 dark:border-red-900 dark:text-red-400 dark:hover:bg-red-950/40"
                                            >
                                                删除
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-10 text-center align-middle text-sm text-slate-500 dark:text-slate-400">
                                    暂无密码记录，点击“添加密码”开始创建。
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($passwords->hasPages())
                <div class="border-t border-slate-200 px-4 py-4 dark:border-slate-800">
                    {{ $passwords->links() }}
                </div>
            @endif
        </section>
    </div>
@endsection
