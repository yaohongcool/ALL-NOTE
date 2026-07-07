<form
    method="POST"
    action="{{ $action }}"
    class="mt-6 space-y-5"
    x-data="{
        uuFee: {{ ($skin->uu_fee_rate ?? 0.02) * 100 }},
        buffFee: {{ ($skin->buff_fee_rate ?? 0.025) * 100 }}
    }"
>
    @csrf
    @if ($method !== 'POST')
        @method($method)
    @endif

    <div>
        <label for="name" class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-200">
            名称 <span class="text-red-500">*</span>
        </label>
        <input
            id="name"
            name="name"
            type="text"
            value="{{ old('name', $skin->name) }}"
            class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-100 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:focus:border-blue-500 dark:focus:ring-blue-950"
            placeholder="例如：AK-47 | 表面淬火"
        >
        @error('name')
            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
        @enderror
    </div>

    <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
        <div>
            <label for="cost" class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-200">
                成本 <span class="text-red-500">*</span>
            </label>
            <input
                id="cost"
                name="cost"
                type="number"
                step="0.01"
                value="{{ old('cost', $skin->cost) }}"
                class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-100 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:focus:border-blue-500 dark:focus:ring-blue-950"
                placeholder="0.00"
            >
            @error('cost')
                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="daily_rental" class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-200">
                日租金
            </label>
            <input
                id="daily_rental"
                name="daily_rental"
                type="number"
                step="0.01"
                value="{{ old('daily_rental', $skin->daily_rental) }}"
                class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-100 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:focus:border-blue-500 dark:focus:ring-blue-950"
                placeholder="0.00"
            >
            @error('daily_rental')
                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="uu_price" class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-200">
                UU价格
            </label>
            <input
                id="uu_price"
                name="uu_price"
                type="number"
                step="0.01"
                value="{{ old('uu_price', $skin->uu_price) }}"
                class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-100 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:focus:border-blue-500 dark:focus:ring-blue-950"
                placeholder="0.00"
            >
            @error('uu_price')
                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="uu_fee_rate" class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-200">
                UU手续费率 (%)
            </label>
            <input type="hidden" name="uu_fee_rate" :value="uuFee / 100">
            <input
                id="uu_fee_rate_display"
                type="number"
                step="0.1"
                x-model.number="uuFee"
                class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-100 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:focus:border-blue-500 dark:focus:ring-blue-950"
                placeholder="2.0"
            >
            @error('uu_fee_rate')
                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="buff_price" class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-200">
                Buff价格
            </label>
            <input
                id="buff_price"
                name="buff_price"
                type="number"
                step="0.01"
                value="{{ old('buff_price', $skin->buff_price) }}"
                class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-100 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:focus:border-blue-500 dark:focus:ring-blue-950"
                placeholder="0.00"
            >
            @error('buff_price')
                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="buff_fee_rate" class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-200">
                Buff手续费率 (%)
            </label>
            <input type="hidden" name="buff_fee_rate" :value="buffFee / 100">
            <input
                id="buff_fee_rate_display"
                type="number"
                step="0.1"
                x-model.number="buffFee"
                class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-100 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:focus:border-blue-500 dark:focus:ring-blue-950"
                placeholder="2.5"
            >
            @error('buff_fee_rate')
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
        >{{ old('note', $skin->note) }}</textarea>
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
            href="{{ route('funds.skins.index') }}"
            class="inline-flex items-center justify-center rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-medium text-slate-700 transition hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200 dark:hover:bg-slate-800"
        >
            返回列表
        </a>
    </div>
</form>
