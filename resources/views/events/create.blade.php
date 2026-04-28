@extends('layouts.app', [
    'title' => '添加事件 - 全录笔记',
    'headerTitle' => '添加事件',
    'headerTxt' => '记录问题处理过程和结果',
    'breadcrumb' => [
        ['label' => '首页', 'url' => route('dashboard')],
        ['label' => '事件记录', 'url' => route('events.index')],
        ['label' => '添加事件'],
    ],
])

@section('content')
    <div class="max-w-6xl">
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <h2 class="text-lg font-semibold text-slate-900 dark:text-slate-100">创建事件</h2>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                默认创建事件并保存第一条处理记录。
            </p>

            @include('events._form', [
                'action' => route('events.store'),
                'method' => 'POST',
                'submitText' => '保存事件',
                'eventModel' => $event,
                'recordModel' => $record,
                'statuses' => $statuses,
                'tags' => $tags,
                'selectedEventTagIds' => $selectedEventTagIds,
                'includeRecord' => true,
            ])
        </div>
    </div>
@endsection
