@extends('layouts.app', [
    'title' => '编辑事件 - 全录笔记',
    'headerTitle' => '编辑事件',
    'headerTxt' => '更新事件基础信息',
    'breadcrumb' => [
        ['label' => '首页', 'url' => route('dashboard')],
        ['label' => '事件记录', 'url' => route('events.index')],
        ['label' => $event->title, 'url' => route('events.show', $event)],
        ['label' => '编辑事件'],
    ],
])

@section('content')
    <div class="max-w-6xl">
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <h2 class="text-lg font-semibold text-slate-900 dark:text-slate-100">编辑事件</h2>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                处理记录内容在详情页单独维护。
            </p>

            @include('events._form', [
                'action' => route('events.update', $event),
                'method' => 'PUT',
                'submitText' => '更新事件',
                'eventModel' => $event,
                'statuses' => $statuses,
                'tags' => $tags,
                'selectedEventTagIds' => $selectedEventTagIds,
                'includeRecord' => false,
            ])
        </div>
    </div>
@endsection
