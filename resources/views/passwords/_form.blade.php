<form method="POST" action="{{ $action }}" class="mt-6 space-y-5">
    @csrf
    @if ($method !== 'POST')
        @method($method)
    @endif

    <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
        <div>
            <label for="name" class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-200">
                名称 <span class="text-red-500">*</span>
            </label>
            <input
                id="name"
                name="name"
                type="text"
                value="{{ old('name', $passwordModel->name) }}"
                class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-100 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:focus:border-blue-500 dark:focus:ring-blue-950"
                placeholder="例如：Steam"
            >
            @error('name')
                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="account" class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-200">
                账号 <span class="text-red-500">*</span>
            </label>
            <input
                id="account"
                name="account"
                type="text"
                value="{{ old('account', $passwordModel->account) }}"
                class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-100 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:focus:border-blue-500 dark:focus:ring-blue-950"
                placeholder="请输入账号"
            >
            @error('account')
                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div x-data="passwordEditorField(@js($revealUrl ?? null))">
        <label for="password" class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-200">
            密码
            @if ($passwordRequired ?? false)
                <span class="text-red-500">*</span>
            @else
                <span class="text-slate-400">(选填)</span>
            @endif
        </label>
        <div class="relative">
            <input
                id="password"
                name="password"
                type="password"
                x-ref="passwordInput"
                autocomplete="new-password"
                class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 pr-16 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-100 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:focus:border-blue-500 dark:focus:ring-blue-950"
                placeholder="{{ ($passwordRequired ?? false) ? '请输入密码' : '留空则不修改当前密码' }}"
            >
            <button
                type="button"
                @click="toggleVisibility()"
                class="absolute right-3 top-1/2 -translate-y-1/2 text-xs font-medium text-slate-500 transition hover:text-slate-900 dark:text-slate-400 dark:hover:text-slate-100"
                x-text="inputVisible ? '隐藏' : '显示'"
            ></button>
        </div>

        @if (!empty($revealUrl))
            <div class="mt-2 flex items-center gap-3">
                <button
                    type="button"
                    @click="revealCurrentPassword()"
                    :disabled="loading || countdown > 0"
                    class="rounded-lg border border-slate-200 px-3 py-1 text-xs font-medium text-slate-600 transition hover:bg-slate-100 disabled:cursor-not-allowed disabled:opacity-60 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800"
                >
                    <span x-text="loading ? '读取中...' : (countdown > 0 ? `已显示 ${countdown}s` : '查看当前密码（15秒）')"></span>
                </button>
            </div>
        @endif

        @error('password')
            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
        @enderror
    </div>

    <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
        <div>
            <label for="phone" class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-200">
                绑定手机
            </label>
            <input
                id="phone"
                name="phone"
                type="text"
                value="{{ old('phone', $passwordModel->phone) }}"
                class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-100 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:focus:border-blue-500 dark:focus:ring-blue-950"
                placeholder="选填"
            >
            @error('phone')
                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="email" class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-200">
                绑定邮箱
            </label>
            <input
                id="email"
                name="email"
                type="text"
                value="{{ old('email', $passwordModel->email) }}"
                class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-100 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:focus:border-blue-500 dark:focus:ring-blue-950"
                placeholder="选填"
            >
            @error('email')
                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>
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
        >{{ old('note', $passwordModel->note) }}</textarea>
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
            href="{{ route('passwords.index') }}"
            class="inline-flex items-center justify-center rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-medium text-slate-700 transition hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200 dark:hover:bg-slate-800"
        >
            返回列表
        </a>
    </div>
</form>
