@extends('layouts.app', [
    'title' => '资产列表 - 全录笔记',
    'headerTitle' => '资产列表',
    'headerTxt' => '存储物理设备和数字资产信息',
    'breadcrumb' => [
        ['label' => '首页', 'url' => route('dashboard')],
        ['label' => '资产列表'],
    ],
])

@section('content')
    <div class="space-y-6">
        <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                <form method="GET" action="{{ route('assets.index') }}" class="grid flex-1 grid-cols-1 gap-4 md:grid-cols-3 xl:grid-cols-4">
                    <div>
                        <select
                            id="category"
                            name="category"
                            onchange="this.form.submit()"
                            class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:focus:border-blue-500 dark:focus:ring-blue-950"
                        >
                            <option value="">全部分类</option>
                            @foreach ($categories as $item)
                                <option value="{{ $item }}" @selected($category === $item)>{{ $item }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <select
                            id="status"
                            name="status"
                            onchange="this.form.submit()"
                            class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:focus:border-blue-500 dark:focus:ring-blue-950"
                        >
                            <option value="">全部状态</option>
                            @foreach ($statuses as $item)
                                <option value="{{ $item }}" @selected($status === $item)>{{ $item }}</option>
                            @endforeach
                        </select>
                    </div>

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

                    <div class="flex flex-wrap gap-3 items-end">
                        <a
                            href="{{ route('assets.index') }}"
                            class="inline-flex items-center justify-center rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-medium text-slate-700 transition hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200 dark:hover:bg-slate-800"
                        >
                            重置
                        </a>
                    </div>
                </form>

                <div class="flex-shrink-0">
                    <a
                        href="{{ route('assets.create') }}"
                        class="inline-flex items-center justify-center rounded-2xl bg-blue-600 px-4 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700"
                    >
                        添加资产
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
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">分类</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">状态</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">信息</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">到期日期</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">最后修改</th>
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

                                <td data-label="最后修改" class="px-4 py-4 align-middle">
                                    <span class="text-sm text-slate-600 dark:text-slate-300">
                                        {{ $asset->updated_at?->format('Y-m-d H:i') }}
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

                                        <form method="POST" action="{{ route('assets.destroy', $asset) }}" onsubmit="return confirm('确定删除这条资产记录吗？');">
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
                                    暂无资产记录，点击“添加资产”开始创建。
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
