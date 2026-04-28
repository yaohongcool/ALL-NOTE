@php
    $eventModel = $eventModel ?? $event;
    $recordModel = $recordModel ?? ($record ?? null);
    $includeRecord = $includeRecord ?? false;
    $selectedEventTagIds = collect(old('event_tag_ids', $selectedEventTagIds ?? []))->map(fn ($id) => (int) $id)->all();
@endphp

<form method="POST" action="{{ $action }}" class="mt-6 space-y-6" enctype="multipart/form-data" x-data="eventRecordForm()" @submit="prepareSubmit()">
    @csrf
    @if ($method !== 'POST')
        @method($method)
    @endif

    <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
        <div class="md:col-span-2">
            <label for="title" class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-200">
                名称 <span class="text-red-500">*</span>
            </label>
            <input
                id="title"
                name="title"
                type="text"
                value="{{ old('title', $eventModel->title) }}"
                class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-100 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:focus:border-blue-500 dark:focus:ring-blue-950"
                placeholder="例如：服务器登录异常处理"
            >
            @error('title')
                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="status" class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-200">
                状态 <span class="text-red-500">*</span>
            </label>
            <select
                id="status"
                name="status"
                class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:focus:border-blue-500 dark:focus:ring-blue-950"
            >
                @foreach ($statuses as $item)
                    <option value="{{ $item }}" @selected(old('status', $eventModel->status) === $item)>{{ $item }}</option>
                @endforeach
            </select>
            @error('status')
                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="visibility" class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-200">
                可见性 <span class="text-red-500">*</span>
            </label>
            <select
                id="visibility"
                name="visibility"
                class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:focus:border-blue-500 dark:focus:ring-blue-950"
            >
                <option value="private" @selected(old('visibility', $eventModel->visibility) === 'private')>仅自己可见</option>
                <option value="public" @selected(old('visibility', $eventModel->visibility) === 'public')>公开</option>
            </select>
            @error('visibility')
                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="subject" class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-200">
                来源/对象
            </label>
            <input
                id="subject"
                name="subject"
                type="text"
                value="{{ old('subject', $eventModel->subject) }}"
                class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-100 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:focus:border-blue-500 dark:focus:ring-blue-950"
                placeholder="客户、系统、设备、账号、渠道、项目等"
            >
            @error('subject')
                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="occurred_on" class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-200">
                发生日期
            </label>
            <input
                id="occurred_on"
                name="occurred_on"
                type="date"
                value="{{ old('occurred_on', $eventModel->occurred_on?->format('Y-m-d')) }}"
                class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:focus:border-blue-500 dark:focus:ring-blue-950"
            >
            @error('occurred_on')
                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5 dark:border-slate-800 dark:bg-slate-950/60">
        <h3 class="text-sm font-semibold text-slate-900 dark:text-slate-100">事件标签</h3>

        @if($tags->isNotEmpty())
            <div class="mt-4 flex flex-wrap gap-2">
                @foreach ($tags as $tag)
                    <div data-event-tag-id="{{ $tag->id }}" class="inline-flex items-center overflow-hidden rounded-full border border-slate-200 bg-white text-sm text-slate-700 transition hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200 dark:hover:bg-slate-800">
                        <label class="inline-flex cursor-pointer items-center gap-2 px-3 py-2">
                            <input
                                type="checkbox"
                                name="event_tag_ids[]"
                                value="{{ $tag->id }}"
                                class="h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500"
                                @checked(in_array($tag->id, $selectedEventTagIds, true))
                            >
                            <span>{{ $tag->name }}</span>
                        </label>
                        <button
                            type="button"
                            class="border-l border-slate-200 px-2.5 py-2 text-base leading-none text-slate-400 transition hover:bg-slate-100 hover:text-slate-600 dark:border-slate-700 dark:text-slate-500 dark:hover:bg-slate-800 dark:hover:text-slate-300"
                            title="删除标签"
                            aria-label="删除标签 {{ $tag->name }}"
                            @click.prevent="deleteEventTag({{ (int) $tag->id }}, @js(route('event-tags.destroy', $tag)))"
                        >
                            ×
                        </button>
                    </div>
                @endforeach
            </div>
        @endif

        <div class="mt-4">
            <input
                id="new_event_tags"
                name="new_event_tags"
                type="text"
                value="{{ old('new_event_tags') }}"
                class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-100 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:focus:border-blue-500 dark:focus:ring-blue-950"
                placeholder="输入新标签，多个标签用逗号分隔"
            >
            @error('new_event_tags')
                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>
    </div>

    @if($includeRecord)
        <div class="space-y-5 rounded-2xl border border-slate-200 bg-white p-5 dark:border-slate-800 dark:bg-slate-900">
            <h3 class="text-sm font-semibold text-slate-900 dark:text-slate-100">首条处理记录</h3>

            @include('events.records._rich_editor', [
                'name' => 'process',
                'label' => '过程',
                'value' => $recordModel?->process,
                'record' => $recordModel ?? new \App\Models\EventRecord(),
                'placeholder' => '记录排查、处理、沟通或执行过程，可直接粘贴图片',
            ])

            @include('events.records._rich_editor', [
                'name' => 'result',
                'label' => '结果',
                'value' => $recordModel?->result,
                'record' => $recordModel ?? new \App\Models\EventRecord(),
                'placeholder' => '记录最终结论、影响范围或后续事项，可直接粘贴图片',
            ])

            <div>
                <label for="attachments" class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-200">
                    附件
                </label>
                <input
                    id="attachments"
                    name="attachments[]"
                    type="file"
                    multiple
                    class="block w-full text-sm text-slate-600 file:mr-4 file:rounded-xl file:border-0 file:bg-blue-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-blue-700 hover:file:bg-blue-100 dark:text-slate-300 dark:file:bg-slate-800 dark:file:text-blue-300"
                >
                @error('attachments')
                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
                @error('attachments.*')
                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

        </div>
    @endif

    <div class="flex flex-wrap gap-3 pt-2">
        <button
            type="submit"
            class="inline-flex items-center justify-center rounded-2xl bg-blue-600 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700"
        >
            {{ $submitText }}
        </button>

        <a
            href="{{ route('events.index') }}"
            class="inline-flex items-center justify-center rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-medium text-slate-700 transition hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200 dark:hover:bg-slate-800"
        >
            返回列表
        </a>
    </div>
</form>
