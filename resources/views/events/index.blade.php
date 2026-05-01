@extends('layouts.app', [
    'title' => '事件记录 - 全录笔记',
    'headerTitle' => '事件记录',
    'headerTxt' => '知识库，归档问题处理过程和结果',
    'breadcrumb' => [
        ['label' => '首页', 'url' => route('dashboard')],
        ['label' => '事件记录'],
    ],
])

@section('content')
    @php
        $eventContentService = app(\App\Services\EventContentService::class);
    @endphp

    <div class="space-y-6">
        <div class="flex justify-start">
            <a
                href="{{ route('events.create') }}"
                class="inline-flex w-full items-center justify-center rounded-2xl bg-blue-600 px-4 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700 sm:w-auto"
            >
                添加事件
            </a>
        </div>

        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <div class="responsive-table-wrap overflow-x-auto">
                <table class="responsive-table min-w-full divide-y divide-slate-200 dark:divide-slate-800">
                    <thead class="bg-slate-50 dark:bg-slate-800/60">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">标题</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">状态</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">来源/对象</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">发生日期</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">标签</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">记录数</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">内容</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">可见性（后期推出共享功能）</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">操作</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                        @forelse ($events as $event)
                            @php
                                $statusClass = match ($event->status) {
                                    '已处理' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300',
                                    '处理中' => 'bg-blue-50 text-blue-700 dark:bg-blue-950/40 dark:text-blue-300',
                                    '待处理' => 'bg-amber-50 text-amber-700 dark:bg-amber-950/40 dark:text-amber-300',
                                    '无需处理' => 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300',
                                    default => 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300',
                                };

                                $visibilityClass = $event->visibility === 'public'
                                    ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-950/40 dark:text-indigo-300'
                                    : 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300';
                            @endphp
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40">
                                <td data-label="标题" class="px-4 py-4 align-middle">
                                    <a href="{{ route('events.show', $event) }}" class="text-sm font-semibold text-slate-900 transition hover:text-blue-700 dark:text-slate-100 dark:hover:text-blue-400">
                                        {{ $event->title }}
                                    </a>
                                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                                        创建于 {{ $event->created_at?->format('Y-m-d H:i') }}
                                    </p>
                                </td>

                                <td data-label="状态" class="px-4 py-4 align-middle">
                                    <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $statusClass }}">
                                        {{ $event->status }}
                                    </span>
                                </td>

                                <td data-label="来源/对象" class="px-4 py-4 align-middle">
                                    <span class="text-sm text-slate-600 dark:text-slate-300">
                                        {{ $event->subject ?: '-' }}
                                    </span>
                                </td>

                                <td data-label="发生日期" class="px-4 py-4 align-middle">
                                    <span class="text-sm text-slate-600 dark:text-slate-300">
                                        {{ $event->occurred_on?->format('Y-m-d') ?: '未记录' }}
                                    </span>
                                </td>

                                <td data-label="标签" class="px-4 py-4 align-middle">
                                    @if($event->tags->isNotEmpty())
                                        <div class="flex max-w-xs flex-wrap gap-1.5">
                                            @foreach ($event->tags->take(3) as $tag)
                                                <span class="rounded-full bg-slate-100 px-2 py-1 text-xs font-medium text-slate-600 dark:bg-slate-800 dark:text-slate-300">{{ $tag->name }}</span>
                                            @endforeach
                                            @if($event->tags->count() > 3)
                                                <span class="rounded-full bg-slate-100 px-2 py-1 text-xs font-medium text-slate-600 dark:bg-slate-800 dark:text-slate-300">+{{ $event->tags->count() - 3 }}</span>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-sm text-slate-400">-</span>
                                    @endif
                                </td>

                                <td data-label="记录数" class="px-4 py-4 align-middle">
                                    <span class="text-sm text-slate-600 dark:text-slate-300">
                                        {{ $event->records_count }}
                                    </span>
                                </td>

                                <td data-label="内容" class="px-4 py-4 align-middle">
                                    <span class="text-sm text-slate-600 dark:text-slate-300">
                                        {{ $eventContentService->textSummary($event->summaryRecord?->process) }}
                                    </span>
                                </td>

                                <td data-label="可见性（后期推出共享功能）" class="px-4 py-4 align-middle">
                                    <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $visibilityClass }}">
                                        {{ $event->visibility_label }}
                                    </span>
                                </td>

                                <td data-label="操作" class="px-4 py-4 align-middle">
                                    <div class="flex justify-end gap-2">
                                        <a
                                            href="{{ route('events.show', $event) }}"
                                            class="rounded-xl border border-slate-200 px-3 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-100 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800"
                                        >
                                            编辑
                                        </a>

                                        <form method="POST" action="{{ route('events.destroy', $event) }}" onsubmit="return confirm('确定删除这个事件及其全部处理记录吗？');">
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
                                <td colspan="9" class="px-4 py-10 text-center align-middle text-sm text-slate-500 dark:text-slate-400">
                                    暂无事件记录，点击“添加事件”开始创建。
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($events->hasPages())
                <div class="border-t border-slate-200 px-4 py-4 dark:border-slate-800">
                    {{ $events->links() }}
                </div>
            @endif
        </section>
    </div>
@endsection
