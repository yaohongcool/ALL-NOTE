@php
    $record = $record ?? new \App\Models\EventRecord();
    $value = old($name, $value ?? null);
    $editorId = $name . '_editor';
    $imagesId = $name . '_images';
    $placeholder = $placeholder ?? '';
    $contentService = app(\App\Services\EventContentService::class);
@endphp

<div>
    <label for="{{ $editorId }}" class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-200">
        {{ $label }}
    </label>

    <input id="{{ $name }}" name="{{ $name }}" type="hidden" x-ref="{{ $name }}Input" value="{{ $value }}">
    <input name="{{ $name }}_image_keys" type="hidden" x-ref="{{ $name }}ImageKeys" value="{{ old($name . '_image_keys', '[]') }}">

    <div
        id="{{ $editorId }}"
        x-ref="{{ $name }}Editor"
        contenteditable="true"
        role="textbox"
        aria-multiline="true"
        data-placeholder="{{ $placeholder }}"
        class="min-h-48 w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm leading-6 text-slate-900 outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:focus:border-blue-500 dark:focus:ring-blue-950"
        @paste="handlePaste($event, '{{ $name }}')"
        @input="refreshContent('{{ $name }}')"
        @focus="rememberSelection('{{ $name }}')"
        @mouseup="rememberSelection('{{ $name }}')"
        @keyup="rememberSelection('{{ $name }}')"
        @click="handleEditorClick($event, '{{ $name }}')"
    >{!! $contentService->renderEditor($value, $record, $name) !!}</div>

    @error($name)
        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
    @enderror

    <div class="mt-3">
        <label for="{{ $imagesId }}" class="inline-flex cursor-pointer items-center justify-center rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200 dark:hover:bg-slate-800">
            插入图片
        </label>
        <input
            id="{{ $imagesId }}"
            x-ref="{{ $name }}Images"
            @change="insertSelectedImages('{{ $name }}')"
            name="{{ $name }}_images[]"
            type="file"
            multiple
            accept="image/*"
            class="hidden"
        >

        @error($name . '_images')
            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
        @enderror
        @error($name . '_images.*')
            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
        @enderror
    </div>
</div>
