@extends('layouts.app', [
    'title' => $event->title . ' - 全录笔记',
    'headerTitle' => '事件详情',
    'headerTxt' => '查看事件基础信息和处理记录',
    'breadcrumb' => [
        ['label' => '首页', 'url' => route('dashboard')],
        ['label' => '事件记录', 'url' => route('events.index')],
        ['label' => $event->title],
    ],
])

@section('content')
    <div class="space-y-6">
        <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
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

            <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                <div class="min-w-0">
                    <h2 class="text-2xl font-semibold text-slate-900 dark:text-slate-100">{{ $event->title }}</h2>

                    <div class="mt-4 flex flex-wrap gap-2">
                        <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $statusClass }}">{{ $event->status }}</span>
                        <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $visibilityClass }}">{{ $event->visibility_label }}</span>
                    </div>
                </div>

                <div class="flex flex-wrap gap-2">
                    @if($isOwner)
                        <a
                            href="{{ route('events.edit', $event) }}"
                            class="inline-flex items-center justify-center rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-medium text-slate-700 transition hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200 dark:hover:bg-slate-800"
                        >
                            编辑
                        </a>

                        <form method="POST" action="{{ route('events.destroy', $event) }}" onsubmit="return confirm('确定删除这个事件及其全部处理记录吗？');">
                            @csrf
                            @method('DELETE')
                            <button
                                type="submit"
                                class="inline-flex items-center justify-center rounded-2xl border border-red-200 bg-white px-4 py-3 text-sm font-medium text-red-600 transition hover:bg-red-50 dark:border-red-900 dark:bg-slate-900 dark:text-red-400 dark:hover:bg-red-950/40"
                            >
                                删除
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            <dl class="mt-6 grid grid-cols-1 gap-4 md:grid-cols-3">
                <div class="rounded-2xl bg-slate-50 p-4 dark:bg-slate-950/60">
                    <dt class="text-xs font-semibold uppercase tracking-wider text-slate-400">来源/对象</dt>
                    <dd class="mt-2 text-sm text-slate-800 dark:text-slate-100">{{ $event->subject ?: '-' }}</dd>
                </div>

                <div class="rounded-2xl bg-slate-50 p-4 dark:bg-slate-950/60">
                    <dt class="text-xs font-semibold uppercase tracking-wider text-slate-400">发生日期</dt>
                    <dd class="mt-2 text-sm text-slate-800 dark:text-slate-100">{{ $event->occurred_on?->format('Y-m-d') ?: '未记录发生日期' }}</dd>
                </div>

                <div class="rounded-2xl bg-slate-50 p-4 dark:bg-slate-950/60">
                    <dt class="text-xs font-semibold uppercase tracking-wider text-slate-400">记录人</dt>
                    <dd class="mt-2 text-sm text-slate-800 dark:text-slate-100">{{ $event->user?->username ?: '-' }}</dd>
                </div>
            </dl>

            @if($isOwner && $event->tags->isNotEmpty())
                <div class="mt-5 flex flex-wrap gap-2">
                    @foreach ($event->tags as $tag)
                        <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-600 dark:bg-slate-800 dark:text-slate-300">{{ $tag->name }}</span>
                    @endforeach
                </div>
            @endif

            <div class="mt-5 rounded-2xl bg-slate-50 p-4 dark:bg-slate-950/60">
                <h3 class="text-xs font-semibold uppercase tracking-wider text-slate-400">问题描述</h3>
                <p class="mt-2 whitespace-pre-line text-sm leading-6 text-slate-800 dark:text-slate-100">{{ $event->description ?: '-' }}</p>
            </div>
        </section>

        <section class="space-y-4">
            <div class="flex items-center justify-between gap-4">
                <h3 class="text-lg font-semibold text-slate-900 dark:text-slate-100">处理记录</h3>

                @if($isOwner)
                    <a
                        href="{{ route('event-records.create', $event) }}"
                        class="inline-flex items-center justify-center rounded-2xl bg-blue-600 px-4 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700"
                    >
                        添加处理记录
                    </a>
                @endif
            </div>

            @forelse ($event->records as $record)
                @php
                    $canEditRecord = $record->isRecorder(auth()->id());
                    $attachments = $record->files->where('usage', \App\Models\EventFile::USAGE_ATTACHMENT);
                    $contentService = app(\App\Services\EventContentService::class);
                @endphp
                <article class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                    <div class="flex flex-col gap-2 border-b border-slate-200 pb-4 dark:border-slate-800 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <p class="text-sm font-semibold text-slate-900 dark:text-slate-100">
                                {{ $record->user?->username ?: '记录人' }}
                            </p>
                            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                                {{ $record->created_at?->format('Y-m-d H:i') }}
                            </p>
                        </div>

                        @if($canEditRecord)
                            <div class="flex flex-wrap gap-2">
                                <a
                                    href="{{ route('event-records.edit', $record) }}"
                                    class="rounded-xl border border-slate-200 px-3 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-100 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800"
                                >
                                    编辑
                                </a>

                                <form method="POST" action="{{ route('event-records.destroy', $record) }}" onsubmit="return confirm('确定删除这条处理记录吗？');">
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
                        @endif
                    </div>

                    <div class="mt-5 grid grid-cols-1 gap-5 lg:grid-cols-2">
                        <div>
                            <h4 class="text-sm font-semibold text-slate-900 dark:text-slate-100">过程</h4>
                            <div class="mt-3 min-h-32 rounded-2xl bg-slate-50 p-4 text-sm leading-6 text-slate-700 dark:bg-slate-950/60 dark:text-slate-200">
                                {!! $contentService->renderDisplay($record->process, $record, \App\Models\EventFile::CONTEXT_PROCESS) !!}
                            </div>
                        </div>

                        <div>
                            <h4 class="text-sm font-semibold text-slate-900 dark:text-slate-100">结果</h4>
                            <div class="mt-3 min-h-32 rounded-2xl bg-slate-50 p-4 text-sm leading-6 text-slate-700 dark:bg-slate-950/60 dark:text-slate-200">
                                {!! $contentService->renderDisplay($record->result, $record, \App\Models\EventFile::CONTEXT_RESULT) !!}
                            </div>
                        </div>
                    </div>

                    @if($attachments->isNotEmpty())
                        <div class="mt-5 border-t border-slate-200 pt-5 dark:border-slate-800">
                            <h4 class="text-sm font-semibold text-slate-900 dark:text-slate-100">附件</h4>
                            <div class="mt-3 grid grid-cols-1 gap-2 md:grid-cols-2">
                                @foreach ($attachments as $file)
                                    <a
                                        href="{{ route('event-files.download', $file) }}"
                                        class="flex items-center justify-between gap-3 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm transition hover:bg-slate-100 dark:border-slate-800 dark:bg-slate-950 dark:hover:bg-slate-800"
                                    >
                                        <span class="min-w-0 truncate text-slate-700 dark:text-slate-200">{{ $file->original_name ?: basename($file->path) }}</span>
                                        <span class="shrink-0 text-xs text-slate-500 dark:text-slate-400">
                                            {{ $file->size ? number_format($file->size / 1024, 1) . ' KB' : '下载' }}
                                        </span>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </article>
            @empty
                <div class="rounded-2xl border border-slate-200 bg-white px-6 py-10 text-center text-sm text-slate-500 shadow-sm dark:border-slate-800 dark:bg-slate-900 dark:text-slate-400">
                    暂无处理记录。
                </div>
            @endforelse
        </section>
    </div>
@endsection
