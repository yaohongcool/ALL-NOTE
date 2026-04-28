<form method="POST" action="{{ $action }}" class="mt-6 space-y-5">
    @csrf
    @if ($method !== 'POST')
        @method($method)
    @endif

    <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
        <div>
            <label for="name" class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-200">
                姓名 <span class="text-red-500">*</span>
            </label>
            <input
                id="name"
                name="name"
                type="text"
                value="{{ old('name', $documentModel->name) }}"
                class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-100 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:focus:border-blue-500 dark:focus:ring-blue-950"
                placeholder="例如：张三"
            >
            @error('name')
                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="category" class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-200">
                分类 <span class="text-red-500">*</span>
            </label>
            <select
                id="category"
                name="category"
                class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:focus:border-blue-500 dark:focus:ring-blue-950"
            >
                @foreach ($categories as $item)
                    <option value="{{ $item }}" @selected(old('category', $documentModel->category) === $item)>{{ $item }}</option>
                @endforeach
            </select>
            @error('category')
                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div>
        <label for="due_date" class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-200">
            到期时间
        </label>
        <input
            id="due_date"
            name="due_date"
            type="date"
            value="{{ old('due_date', $documentModel->due_date?->format('Y-m-d')) }}"
            class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:focus:border-blue-500 dark:focus:ring-blue-950"
        >
        @error('due_date')
            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="note" class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-200">
            备注
        </label>
        <textarea
            id="note"
            name="note"
            rows="4"
            class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-100 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:focus:border-blue-500 dark:focus:ring-blue-950"
            placeholder="可填写补充说明"
        >{{ old('note', $documentModel->note) }}</textarea>
        @error('note')
            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
        @enderror
    </div>


    <div class="flex flex-wrap gap-3 pt-2">
        <button
            type="submit"
            class="inline-flex items-center justify-center rounded-2xl bg-blue-600 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700"
        >
            {{ $submitText }}
        </button>

        <a
            href="{{ route('documents.index') }}"
            class="inline-flex items-center justify-center rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-medium text-slate-700 transition hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200 dark:hover:bg-slate-800"
        >
            返回列表
        </a>
    </div>
</form>