<form
    method="POST"
    action="{{ $action }}"
    class="mt-6 space-y-5"
    x-data="{ category: @js(old('category', $assetModel->category ?? '物理设备')) }"
>
    @csrf
    @if ($method !== 'POST')
        @method($method)
    @endif

    <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
        <div>
            <label for="category" class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-200">
                资产分类 <span class="text-red-500">*</span>
            </label>
            <select
                id="category"
                name="category"
                x-model="category"
                class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:focus:border-blue-500 dark:focus:ring-blue-950"
            >
                @foreach ($categories as $item)
                    <option value="{{ $item }}">{{ $item }}</option>
                @endforeach
            </select>
            @error('category')
                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="name" class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-200">
                名称 <span class="text-red-500">*</span>
            </label>
            <input
                id="name"
                name="name"
                type="text"
                value="{{ old('name', $assetModel->name) }}"
                class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-100 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:focus:border-blue-500 dark:focus:ring-blue-950"
                placeholder="例如：我的电脑"
            >
            @error('name')
                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div>
        <label for="due_date" class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-200">
            到期时间
            <span x-show="category === '云服务器' || category === '域名'" class="text-red-500" style="display: none;">*</span>
        </label>
        <input
            id="due_date"
            name="due_date"
            type="date"
            value="{{ old('due_date', $assetModel->due_date?->format('Y-m-d')) }}"
            class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:focus:border-blue-500 dark:focus:ring-blue-950"
        >
        @error('due_date')
            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
        @enderror
    </div>

    <div x-show="category === '物理设备'" x-cloak>
        <div class="rounded-2xl bg-slate-50 p-5 dark:bg-slate-800/60">
            <h3 class="text-sm font-semibold text-slate-900 dark:text-slate-100">配置信息</h3>
            <div class="mt-4 grid grid-cols-1 gap-5 md:grid-cols-2">
                <div>
                    <label for="cpu_model" class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-200">CPU 型号</label>
                    <input id="cpu_model" name="cpu_model" type="text" value="{{ old('cpu_model', $assetModel->getDetail('cpu_model')) }}" placeholder="例如：i5-10400F" class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:focus:border-blue-500 dark:focus:ring-blue-950">
                    @error('cpu_model')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="gpu_model" class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-200">显卡型号</label>
                    <input id="gpu_model" name="gpu_model" type="text" value="{{ old('gpu_model', $assetModel->getDetail('gpu_model')) }}" placeholder="例如：RTX 4060" class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:focus:border-blue-500 dark:focus:ring-blue-950">
                    @error('gpu_model')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="memory" class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-200">内存（GB）</label>
                    <input id="memory" name="memory" type="text" value="{{ old('memory', $assetModel->getDetail('memory')) }}" placeholder="例如：16" class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:focus:border-blue-500 dark:focus:ring-blue-950">
                    @error('memory')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="storage_1" class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-200">硬盘1（TB）</label>
                    <input id="storage_1" name="storage_1" type="text" value="{{ old('storage_1', $assetModel->getDetail('storage_1')) }}" placeholder="例如：1" class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:focus:border-blue-500 dark:focus:ring-blue-950">
                    @error('storage_1')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="storage_2" class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-200">硬盘2（TB）</label>
                    <input id="storage_2" name="storage_2" type="text" value="{{ old('storage_2', $assetModel->getDetail('storage_2')) }}" placeholder="例如：1" class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:focus:border-blue-500 dark:focus:ring-blue-950">
                    @error('storage_2')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="storage_3" class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-200">硬盘3（TB）</label>
                    <input id="storage_3" name="storage_3" type="text" value="{{ old('storage_3', $assetModel->getDetail('storage_3')) }}" placeholder="例如：2" class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:focus:border-blue-500 dark:focus:ring-blue-950">
                    @error('storage_3')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>
    </div>

    <div x-show="category === '云服务器'" x-cloak>
        <div class="rounded-2xl bg-slate-50 p-5 dark:bg-slate-800/60">
            <h3 class="text-sm font-semibold text-slate-900 dark:text-slate-100">云服务器信息</h3>
            <div class="mt-4 grid grid-cols-1 gap-5 md:grid-cols-2">
                <div>
                    <label for="cpu_cores" class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-200">CPU 核心数</label>
                    <select id="cpu_cores" name="cpu_cores" class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:focus:border-blue-500 dark:focus:ring-blue-950">
                        <option value="">请选择</option>
                        @foreach (['2', '4', '8', '16'] as $item)
                            <option value="{{ $item }}" @selected(old('cpu_cores', $assetModel->getDetail('cpu_cores')) === $item)>{{ $item }}</option>
                        @endforeach
                    </select>
                    @error('cpu_cores')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="memory_size" class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-200">内存大小</label>
                    <select id="memory_size" name="memory_size" class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:focus:border-blue-500 dark:focus:ring-blue-950">
                        <option value="">请选择</option>
                        @foreach (['2GB', '4GB', '8GB', '16GB'] as $item)
                            <option value="{{ $item }}" @selected(old('memory_size', $assetModel->getDetail('memory_size')) === $item)>{{ $item }}</option>
                        @endforeach
                    </select>
                    @error('memory_size')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="ip_address" class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-200">IP 地址</label>
                    <input id="ip_address" name="ip_address" type="text" value="{{ old('ip_address', $assetModel->getDetail('ip_address')) }}" class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:focus:border-blue-500 dark:focus:ring-blue-950">
                    @error('ip_address')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="operating_system" class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-200">操作系统</label>
                    <input id="operating_system" name="operating_system" type="text" value="{{ old('operating_system', $assetModel->getDetail('operating_system')) }}" class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:focus:border-blue-500 dark:focus:ring-blue-950">
                    @error('operating_system')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="provider" class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-200">厂商 / 提供商</label>
                    <input id="provider" name="provider" type="text" value="{{ old('provider', $assetModel->getDetail('provider')) }}" class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:focus:border-blue-500 dark:focus:ring-blue-950">
                    @error('provider')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>
    </div>

    <div x-show="category === '域名'" x-cloak>
        <div class="rounded-2xl bg-slate-50 p-5 dark:bg-slate-800/60">
            <h3 class="text-sm font-semibold text-slate-900 dark:text-slate-100">域名信息</h3>
            <div class="mt-4">
                <label for="domain_address" class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-200">
                    域名地址 <span class="text-red-500">*</span>
                </label>
                <input
                    id="domain_address"
                    name="domain_address"
                    type="text"
                    value="{{ old('domain_address', $assetModel->getDetail('domain_address')) }}"
                    placeholder="例如：example.com"
                    class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:focus:border-blue-500 dark:focus:ring-blue-950"
                >
                @error('domain_address')
                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>
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
        >{{ old('note', $assetModel->note) }}</textarea>
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
            href="{{ route('assets.index') }}"
            class="inline-flex items-center justify-center rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-medium text-slate-700 transition hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200 dark:hover:bg-slate-800"
        >
            返回列表
        </a>
    </div>
</form>