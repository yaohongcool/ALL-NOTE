@extends('layouts.app', [
    'title' => '累计收益 - 全录笔记',
    'headerTitle' => '累计收益',
    'headerTxt' => '管理虚拟资产累计收益记录',
    'breadcrumb' => [
        ['label' => '首页', 'url' => route('dashboard')],
        ['label' => '资金记录', 'url' => route('funds.index')],
        ['label' => '虚拟资产', 'url' => route('funds.skins.index')],
        ['label' => $skin->name, 'url' => route('funds.skins.edit', $skin)],
        ['label' => '累计收益'],
    ],
])

@section('content')
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
        <div class="flex items-center justify-between border-b border-slate-200 px-6 py-4 dark:border-slate-800">
            <h3 class="text-sm font-semibold text-slate-900 dark:text-slate-100">收益记录</h3>
            <a
                href="{{ route('funds.skin-earnings.create', $skin) }}"
                class="inline-flex items-center justify-center rounded-xl bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700"
            >
                登记收益
            </a>
        </div>

        <div class="responsive-table-wrap overflow-x-auto">
            <table class="responsive-table min-w-full divide-y divide-slate-200 dark:divide-slate-800">
                <thead class="bg-slate-50 dark:bg-slate-800/60">
                    <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">时间</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">收益</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">备注</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">操作</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                    @forelse ($earnings as $earning)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40">
                                <td data-label="时间" class="px-4 py-4 align-middle">
                                    <span class="text-sm text-slate-700 dark:text-slate-200">{{ $earning->month->format('Y-m-d') }}</span>
                            </td>
                            <td data-label="收益" class="px-4 py-4 align-middle">
                                <span class="text-sm font-semibold text-emerald-600 dark:text-emerald-400">¥{{ number_format($earning->revenue, 2) }}</span>
                            </td>
                            <td data-label="备注" class="px-4 py-4 align-middle">
                                <span class="text-sm text-slate-600 dark:text-slate-300">{{ $earning->note ?? '-' }}</span>
                            </td>
                            <td data-label="操作" class="px-4 py-4 align-middle">
                                <x-row-actions
                                    :editRoute="route('funds.skin-earnings.edit', [$skin, $earning])"
                                    :deleteRoute="route('funds.skin-earnings.destroy', [$skin, $earning])"
                                    deleteConfirm="确定删除这条收益记录吗？"
                                />
                            </td>
                        </tr>
                    @empty
                        <x-empty-row :colspan="4" message="暂无累计收益记录" />
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($earnings->hasPages())
            <div class="border-t border-slate-200 px-6 py-4 dark:border-slate-800">
                {{ $earnings->links('components.pagination') }}
            </div>
        @endif
    </div>
@endsection
