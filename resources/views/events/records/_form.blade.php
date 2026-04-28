@php
    $attachments = $record->exists
        ? $record->files->where('usage', \App\Models\EventFile::USAGE_ATTACHMENT)
        : collect();
@endphp

<form method="POST" action="{{ $action }}" class="mt-6 space-y-6" enctype="multipart/form-data" x-data="eventRecordForm()" @submit="prepareSubmit()">
    @csrf
    @if ($method !== 'POST')
        @method($method)
    @endif

    <div class="space-y-5 rounded-2xl border border-slate-200 bg-white p-5 dark:border-slate-800 dark:bg-slate-900">
        @include('events.records._rich_editor', [
            'name' => 'process',
            'label' => '过程',
            'value' => $record->process,
            'record' => $record,
            'placeholder' => '记录排查、处理、沟通或执行过程，可直接粘贴图片',
        ])

        @include('events.records._rich_editor', [
            'name' => 'result',
            'label' => '结果',
            'value' => $record->result,
            'record' => $record,
            'placeholder' => '记录最终结论、影响范围或后续事项，可直接粘贴图片',
        ])

        <div>
            <label for="attachments" class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-200">
                附件
            </label>

            @if($attachments->isNotEmpty())
                <div class="mb-4 grid grid-cols-1 gap-2 md:grid-cols-2">
                    @foreach ($attachments as $file)
                        <div data-event-attachment-id="{{ $file->id }}" class="flex items-center justify-between gap-3 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 dark:border-slate-800 dark:bg-slate-950">
                            <span class="min-w-0 truncate text-sm text-slate-700 dark:text-slate-200">{{ $file->original_name ?: basename($file->path) }}</span>
                            <button
                                type="button"
                                class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full text-lg leading-none text-slate-400 transition hover:bg-slate-200 hover:text-slate-600 dark:text-slate-500 dark:hover:bg-slate-800 dark:hover:text-slate-300"
                                title="删除附件"
                                aria-label="删除附件 {{ $file->original_name ?: basename($file->path) }}"
                                @click.prevent="deleteAttachment({{ (int) $file->id }}, @js(route('event-files.destroy', $file)))"
                            >
                                ×
                            </button>
                        </div>
                    @endforeach
                </div>
            @endif

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

    <div class="flex flex-wrap gap-3 pt-2">
        <button
            type="submit"
            class="inline-flex items-center justify-center rounded-2xl bg-blue-600 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700"
        >
            {{ $submitText }}
        </button>

        <a
            href="{{ route('events.show', $event) }}"
            class="inline-flex items-center justify-center rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-medium text-slate-700 transition hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200 dark:hover:bg-slate-800"
        >
            返回事件
        </a>
    </div>
</form>
