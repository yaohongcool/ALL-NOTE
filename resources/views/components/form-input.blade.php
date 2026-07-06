@props(['name', 'label', 'type' => 'text', 'value' => '', 'placeholder' => '', 'required' => false])
<div>
    <label for="{{ $name }}" class="block text-sm font-medium text-slate-700 dark:text-slate-300">
        {{ $label }}
        @if($required)<span class="text-red-500">*</span>@endif
    </label>
    <input
        type="{{ $type }}"
        name="{{ $name }}"
        id="{{ $name }}"
        value="{{ old($name, $value) }}"
        placeholder="{{ $placeholder }}"
        @if($required) required @endif
        class="mt-2 w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-100 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:focus:border-blue-500 dark:focus:ring-blue-950"
    />
    @error($name)
    <x-form-error :name="$name" />
    @enderror
</div>
