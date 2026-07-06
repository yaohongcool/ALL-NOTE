    <form
    method="POST"
    action="{{ $action }}"
    class="mt-6 space-y-5"
    x-data="{
        type: @js(old('type', $budget->type ?? 'expense')),
        calcMode: 'none',
        monthly: @js(old('monthly_amount', $budget->monthly_amount ?? 0)),
        annual: @js(old('annual_amount', $budget->annual_amount ?? 0)),
        onMonthly(val) {
            this.monthly = parseFloat(val) || 0;
            this.annual = this.monthly * 12;
            this.calcMode = this.annual === 0 ? 'none' : 'monthly';
        },
        onAnnual(val) {
            this.annual = parseFloat(val) || 0;
            this.monthly = this.annual / 12;
            this.calcMode = this.monthly === 0 ? 'none' : 'annual';
        }
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
            value="{{ old('name', $budget->name) }}"
            class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-100 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:focus:border-blue-500 dark:focus:ring-blue-950"
            placeholder="例如：工资收入"
        >
        @error('name')
            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-200">
            类型 <span class="text-red-500">*</span>
        </label>
        <div class="flex gap-4">
            <label class="inline-flex cursor-pointer items-center gap-2">
                <input
                    type="radio"
                    name="type"
                    value="income"
                    x-model="type"
                    class="h-4 w-4 border-slate-300 text-blue-600 focus:ring-blue-500"
                >
                <span class="text-sm text-slate-700 dark:text-slate-200">收入</span>
            </label>
            <label class="inline-flex cursor-pointer items-center gap-2">
                <input
                    type="radio"
                    name="type"
                    value="expense"
                    x-model="type"
                    class="h-4 w-4 border-slate-300 text-blue-600 focus:ring-blue-500"
                >
                <span class="text-sm text-slate-700 dark:text-slate-200">支出</span>
            </label>
        </div>
        @error('type')
            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
        @enderror
    </div>

    <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
        <div>
            <label for="monthly_amount" class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-200">
                月金额 <span class="text-red-500">*</span>
            </label>
            <input
                id="monthly_amount"
                name="monthly_amount"
                type="number"
                step="0.01"
                x-model.number="monthly"
                @input="onMonthly($event.target.value)"
                :disabled="calcMode === 'annual'"
                :class="calcMode === 'annual' ? 'opacity-50 cursor-not-allowed bg-slate-100 dark:bg-slate-900' : ''"
                value="{{ old('monthly_amount', $budget->monthly_amount) }}"
                class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-100 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:focus:border-blue-500 dark:focus:ring-blue-950"
                placeholder="0.00"
            >
            @error('monthly_amount')
                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="annual_amount" class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-200">
                年金额 <span class="text-red-500">*</span>
            </label>
            <input
                id="annual_amount"
                name="annual_amount"
                type="number"
                step="0.01"
                x-model.number="annual"
                @input="onAnnual($event.target.value)"
                :disabled="calcMode === 'monthly'"
                :class="calcMode === 'monthly' ? 'opacity-50 cursor-not-allowed bg-slate-100 dark:bg-slate-900' : ''"
                value="{{ old('annual_amount', $budget->annual_amount) }}"
                class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-100 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:focus:border-blue-500 dark:focus:ring-blue-950"
                placeholder="0.00"
            >
            @error('annual_amount')
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
        >{{ old('note', $budget->note) }}</textarea>
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
            href="{{ route('funds.budgets.index') }}"
            class="inline-flex items-center justify-center rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-medium text-slate-700 transition hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200 dark:hover:bg-slate-800"
        >
            返回列表
        </a>
    </div>
</form>
