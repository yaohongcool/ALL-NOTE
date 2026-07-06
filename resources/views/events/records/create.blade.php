@extends('layouts.app', [
    'title' => '添加处理记录 - 全录笔记',
    'headerTitle' => '添加处理记录',
    'headerTxt' => '补充事件的处理过程和结果',
    'breadcrumb' => [
        ['label' => '首页', 'url' => route('dashboard')],
        ['label' => '事件记录', 'url' => route('events.index')],
        ['label' => $event->title, 'url' => route('events.show', $event)],
        ['label' => '添加处理记录'],
    ],
])

@section('content')
    <div class="max-w-6xl">
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <h2 class="text-lg font-semibold text-slate-900 dark:text-slate-100">添加处理记录</h2>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                {{ $event->title }}
            </p>

            @include('events.records._form', [
                'action' => route('event-records.store', $event),
                'method' => 'POST',
                'submitText' => '保存处理记录',
                'event' => $event,
                'record' => $record,
            ])
        </div>
    </div>
@endsection
