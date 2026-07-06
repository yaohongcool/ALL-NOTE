@extends('layouts.app', [
    'title' => '账户管理 - 全录笔记',
    'headerTitle' => '账户管理',
    'headerTxt' => '管理各类资金账户',
    'breadcrumb' => [
        ['label' => '首页', 'url' => route('dashboard')],
        ['label' => '资金记录', 'url' => route('funds.index')],
        ['label' => '账户管理'],
    ],
])

@section('content')
    <div class="space-y-6">
        <div class="flex justify-start">
            <a
                href="{{ route('funds.accounts.create') }}"
                class="inline-flex w-full items-center justify-center rounded-2xl bg-blue-600 px-4 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700 sm:w-auto"
            >
                添加账户
            </a>
        </div>

        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <div class="responsive-table-wrap overflow-x-auto">
                <table class="responsive-table min-w-full divide-y divide-slate-200 dark:divide-slate-800">
                    <thead class="bg-slate-50 dark:bg-slate-800/60">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">名称</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">类型</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">余额</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">排序</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">操作</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                        @forelse ($accounts as $account)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40">
                                <td data-label="名称" class="px-4 py-4 align-middle">
                                    <p class="text-sm font-semibold text-slate-900 dark:text-slate-100">
                                        {{ $account->name }}
                                    </p>
                                    @if($account->note)
                                        <p class="mt-1 max-w-xs truncate text-xs text-slate-500 dark:text-slate-400">
                                            {{ $account->note }}
                                        </p>
                                    @endif
                                </td>
                                <td data-label="类型" class="px-4 py-4 align-middle">
                                    <span class="text-sm text-slate-700 dark:text-slate-200">{{ $account->type }}</span>
                                </td>
                                <td data-label="余额" class="px-4 py-4 align-middle">
                                    <span class="text-sm font-semibold text-slate-900 dark:text-slate-100">¥{{ number_format($account->balance, 2) }}</span>
                                </td>
                                <td data-label="排序" class="px-4 py-4 align-middle">
                                    <span class="text-sm text-slate-600 dark:text-slate-300">{{ $account->sort }}</span>
                                </td>
                                <td data-label="操作" class="px-4 py-4 align-middle">
                                    <x-row-actions
                                        :editRoute="route('funds.accounts.edit', $account)"
                                        :deleteRoute="route('funds.accounts.destroy', $account)"
                                        deleteConfirm="确定删除这个账户吗？"
                                    />
                                </td>
                            </tr>
                        @empty
                            <x-empty-row :colspan="5" message="暂无账户记录，点击添加账户开始创建。" />
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($accounts->hasPages())
                <div class="border-t border-slate-200 px-4 py-4 dark:border-slate-800">
                    {{ $accounts->links() }}
                </div>
            @endif
        </section>
    </div>
@endsection
