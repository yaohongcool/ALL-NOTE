@extends('layouts.app', [
    'title' => 'IT资产 - 全录笔记',
    'headerTitle' => 'IT资产',
    'headerTxt' => '存储物理设备和数字财产信息',
    'breadcrumb' => [
        ['label' => '首页', 'url' => route('dashboard')],
        ['label' => 'IT资产'],
    ],
])

@section('content')
    <div class="space-y-6">
        <div class="flex justify-start">
            <a
                href="{{ route('assets.create') }}"
                class="inline-flex w-full items-center justify-center rounded-2xl bg-blue-600 px-4 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700 sm:w-auto"
            >
                添加IT资产
            </a>
        </div>

        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <div class="responsive-table-wrap overflow-x-auto">
                <table class="responsive-table min-w-full divide-y divide-slate-200 dark:divide-slate-800">
                    <thead class="bg-slate-50 dark:bg-slate-800/60">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">名称</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">分类</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">状态</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">信息</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">到期日期</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">距离到期</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">操作</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                        @forelse ($assets as $asset)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40">
                                <td data-label="名称" class="px-4 py-4 align-middle">
                                    <p class="text-sm font-semibold text-slate-900 dark:text-slate-100">
                                        {{ $asset->name }}
                                    </p>
                                    @if($asset->note)
                                        <p class="mt-1 max-w-xs truncate text-xs text-slate-500 dark:text-slate-400">
                                            {{ $asset->note }}
                                        </p>
                                    @endif
                                </td>

                                <td data-label="分类" class="px-4 py-4 align-middle">
                                    <span class="text-sm text-slate-700 dark:text-slate-200">{{ $asset->category }}</span>
                                </td>

                                <td data-label="状态" class="px-4 py-4 align-middle">
                                    @php
                                        $statusClass = match ($asset->computed_status) {
                                            '正常' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300',
                                            '即将到期' => 'bg-amber-50 text-amber-700 dark:bg-amber-950/40 dark:text-amber-300',
                                            '已过期' => 'bg-red-50 text-red-700 dark:bg-red-950/40 dark:text-red-300',
                                            default => 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300',
                                        };
                                    @endphp
                                    <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $statusClass }}">
                                        {{ $asset->computed_status }}
                                    </span>
                                </td>

                                <td data-label="信息" class="px-4 py-4 align-middle">
                                    <span class="text-sm text-slate-600 dark:text-slate-300">
                                        {{ $asset->summary ?: '-' }}
                                    </span>
                                </td>

                                <td data-label="到期日期" class="px-4 py-4 align-middle">
                                    <span class="text-sm text-slate-600 dark:text-slate-300">
                                        {{ $asset->due_date?->format('Y-m-d') ?: '-' }}
                                    </span>
                                </td>

                                <td data-label="距离到期" class="px-4 py-4 align-middle">
                                    <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $statusClass }}">
                                        {{ $asset->days_until_due_label }}
                                    </span>
                                </td>

                                <td data-label="操作" class="px-4 py-4 align-middle">
                                    <div class="flex justify-end gap-2">
                                        <a
                                            href="{{ route('assets.edit', $asset) }}"
                                            class="rounded-xl border border-slate-200 px-3 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-100 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800"
                                        >
                                            编辑
                                        </a>

                                        <form method="POST" action="{{ route('assets.destroy', $asset) }}" onsubmit="return confirm('确定删除这条IT资产记录吗？');">
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
                                    暂无IT资产记录，点击“添加IT资产”开始创建。
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($assets->hasPages())
                <div class="border-t border-slate-200 px-4 py-4 dark:border-slate-800">
                    {{ $assets->links() }}
                </div>
            @endif
        </section>
    </div>
@endsection
