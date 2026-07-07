@extends('layouts.app', [
    'title' => '累计收益 - 全录笔记',
    'headerTitle' => '累计收益',
    'headerTxt' => '内联编辑各期收益',
    'breadcrumb' => [
        ['label' => '首页', 'url' => route('dashboard')],
        ['label' => '资金记录', 'url' => route('funds.index')],
        ['label' => '累计收益'],
    ],
])

@section('content')
<div x-data="earningTable()" x-init="init()" class="space-y-6">
    <div x-show="loading" class="text-sm text-slate-400">加载中...</div>

    <div x-show="!loading" class="flex flex-wrap gap-4">
        <template x-for="skin in skins" :key="skin.id">
            <div class="w-56 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <p class="text-sm font-medium text-slate-500 dark:text-slate-400" x-text="skin.name + ' 租金累计收入'"></p>
                <p class="mt-3 text-3xl font-bold tracking-tight text-emerald-600 dark:text-emerald-400" x-text="'¥' + cumulative(skin.id).toFixed(2)"></p>
                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">去除手续实际累计收入</p>
            </div>
        </template>
    </div>

    <section x-show="!loading" class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
        <div class="responsive-table-wrap overflow-x-auto">
            <table class="responsive-table min-w-full divide-y divide-slate-200 dark:divide-slate-800">
                <thead class="bg-slate-50 dark:bg-slate-800/60">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">周期</th>
                        <template x-for="skin in skins" :key="skin.id">
                            <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400" x-text="skin.name"></th>
                        </template>
                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">操作</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                    <template x-for="(period, idx) in periods" :key="period.id">
                        <tr>
                            <td class="px-4 py-4 align-middle">
                                <span class="text-sm font-medium text-slate-700 dark:text-slate-200" x-text="period.label"></span>
                            </td>
                            <template x-for="skin in skins" :key="skin.id">
                                <td class="px-4 py-4 text-right align-middle">
                                    <template x-if="editingId !== period.id">
                                        <span>
                                            <span class="text-sm font-semibold text-emerald-600 dark:text-emerald-400">
                                                ¥<span x-text="(period.amounts[skin.id] || 0).toFixed(2)"></span>
                                            </span>
                                            <span x-show="(period.original_amounts[skin.id] || 0) > 0" class="text-xs text-slate-400 dark:text-slate-500">
                                                （¥<span x-text="(period.original_amounts[skin.id] || 0).toFixed(2)"></span>）
                                            </span>
                                        </span>
                                    </template>
                                    <template x-if="editingId === period.id">
                                        <input type="number" step="0.01"
                                            x-model="editForm.amounts[skin.id]"
                                            class="w-24 rounded-xl border border-slate-300 bg-white px-3 py-1.5 text-right text-sm text-slate-900 outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100"
                                            placeholder="0.00">
                                    </template>
                                </td>
                            </template>
                            <td class="px-4 py-4 text-right align-middle">
                                <template x-if="editingId !== period.id">
                                    <div class="flex justify-end gap-1">
                                        <button @click="startEdit(period)" class="rounded-xl border border-slate-200 px-3 py-1.5 text-xs font-medium text-slate-600 transition hover:bg-slate-100 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800">编辑</button>
                                        <button x-show="idx === periods.length - 1" @click="deletePeriod(period)" class="rounded-xl border border-red-200 px-3 py-1.5 text-xs font-medium text-red-600 transition hover:bg-red-50 dark:border-red-900 dark:text-red-400 dark:hover:bg-red-950/40">删除</button>
                                    </div>
                                </template>
                                <template x-if="editingId === period.id">
                                    <div class="flex justify-end gap-1">
                                        <button @click="saveEdit(period)" class="rounded-xl border border-emerald-200 bg-emerald-50 px-3 py-1.5 text-xs font-medium text-emerald-700 transition hover:bg-emerald-100 dark:border-emerald-900 dark:bg-emerald-950/40 dark:text-emerald-300">保存</button>
                                        <button @click="clearAndSave(period)" class="rounded-xl border border-slate-200 px-3 py-1.5 text-xs font-medium text-slate-600 transition hover:bg-slate-100 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800">清空</button>
                                    </div>
                                </template>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </section>

    <div class="flex items-center justify-between" x-show="!loading">
        <button @click="addPeriod()"
            class="inline-flex items-center justify-center rounded-2xl border-2 border-dashed border-slate-300 bg-white px-6 py-3 text-sm font-medium text-slate-500 transition hover:border-blue-400 hover:text-blue-600 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-400 dark:hover:border-blue-500 dark:hover:text-blue-400">
            + 添加<span x-text="'第' + (periods.length + 1) + '期'"></span>
        </button>

        <button x-show="periods.length > 0" @click="deleteAll()"
            class="inline-flex items-center justify-center rounded-2xl border border-red-200 bg-white px-6 py-3 text-sm font-medium text-red-600 transition hover:bg-red-50 dark:border-red-900 dark:bg-slate-900 dark:text-red-400 dark:hover:bg-red-950/40">
            全部删除
        </button>
    </div>
</div>

<script>
window.earningTable = function () {
    return {
        baseUrl: '{{ route('funds.historical-earnings.periods.store') }}',
        skins: [],
        periods: [],
        editingId: null,
        editForm: { amounts: {} },
        loading: true,

        init() {
            this.loadData();
        },

        async loadData() {
            this.loading = true;
            try {
                const res = await fetch('{{ route('funds.historical-earnings.data') }}');
                const data = await res.json();
                this.skins = data.skins;
                this.periods = data.periods;
                this.periods.forEach(function(p) {
                    p.original_amounts = p.original_amounts || {};
                    p.amounts = p.amounts || {};
                });
            } finally {
                this.loading = false;
            }
        },

        cumulative(skinId) {
            var total = 0;
            this.periods.forEach(function(p) { total += p.amounts[skinId] || 0; });
            return total;
        },

        buildEmptyAmounts() {
            var amounts = {};
            this.skins.forEach(function(s) { amounts[s.id] = 0; });
            return amounts;
        },

        async addPeriod() {
            // 先保存当前编辑中的期
            if (this.editingId !== null) {
                var editing = this.periods.find(function(p) { return p.id === this.editingId; }.bind(this));
                if (editing) await this.saveEdit(editing);
            }
            this.loading = true;
            try {
                const res = await fetch('{{ route('funds.historical-earnings.periods.store') }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                });
                const newPeriod = await res.json();
                newPeriod.amounts = {};
                newPeriod.original_amounts = {};
                this.skins.forEach(function(s) { newPeriod.amounts[s.id] = 0; newPeriod.original_amounts[s.id] = 0; });
                this.periods.push(newPeriod);
                this.startEdit(newPeriod);
            } finally {
                this.loading = false;
            }
        },

        startEdit(period) {
            this.editingId = period.id;
            this.editForm.amounts = {};
            this.skins.forEach(function(s) {
                this.editForm.amounts[s.id] = period.original_amounts[s.id] || 0;
            }.bind(this));
        },

        async saveEdit(period) {
            this.loading = true;
            try {
                await fetch(this.baseUrl + '/' + period.id, {
                    method: 'PUT',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({ amounts: this.editForm.amounts })
                });
                this.skins.forEach(function(s) {
                    var original = parseFloat(this.editForm.amounts[s.id]) || 0;
                    var revenue = Math.round(original * 0.8 * 100) / 100;
                    period.amounts[s.id] = revenue;
                    period.original_amounts[s.id] = original;
                }.bind(this));
                this.editingId = null;
            } finally {
                this.loading = false;
            }
        },

        async clearAndSave(period) {
            this.editForm.amounts = {};
            this.skins.forEach(function(s) { this.editForm.amounts[s.id] = 0; }.bind(this));
            await this.saveEdit(period);
        },

        async deletePeriod(period) {
            if (!confirm('确定删除 ' + period.label + ' 吗？')) return;
            this.loading = true;
            try {
                await fetch(this.baseUrl + '/' + period.id, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                });
                this.periods = this.periods.filter(function(p) { return p.id !== period.id; });
            } finally {
                this.loading = false;
            }
        },

        async deleteAll() {
            if (!confirm('确定删除所有期次吗？此操作不可撤销。')) return;
            if (!confirm('再次确认：删除全部 ' + this.periods.length + ' 期数据？')) return;
            this.loading = true;
            try {
                for (var i = 0; i < this.periods.length; i++) {
                    await fetch(this.baseUrl + '/' + this.periods[i].id, {
                        method: 'DELETE',
                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                    });
                }
                this.periods = [];
            } finally {
                this.loading = false;
            }
        }
    };
};
</script>
@endsection
