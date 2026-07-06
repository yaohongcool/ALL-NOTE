@extends('layouts.app', [
    'title' => '统计图表 - 全录笔记',
    'headerTitle' => '统计图表',
    'headerTxt' => '资金收支与储蓄数据分析',
    'breadcrumb' => [
        ['label' => '首页', 'url' => route('dashboard')],
        ['label' => '资金记录', 'url' => route('funds.index')],
        ['label' => '统计图表'],
    ],
])

@section('content')
<div x-data="fundChart()" x-init="init()" class="space-y-6">
    <div class="flex items-center gap-4">
        <label for="year-select" class="text-sm font-medium text-slate-700 dark:text-slate-200">选择年份</label>
        <select
            id="year-select"
            x-model="year"
            @change="loadChartData()"
            class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm text-slate-900 outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:focus:border-blue-500 dark:focus:ring-blue-950"
        >
            @php $currentYear = now()->year; @endphp
            @for($y = $currentYear; $y >= $currentYear - 5; $y--)
                <option value="{{ $y }}">{{ $y }} 年</option>
            @endfor
        </select>
        <span x-show="loading" class="text-sm text-slate-400">加载中...</span>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <h3 class="mb-4 text-sm font-semibold text-slate-900 dark:text-slate-100">收支趋势</h3>
            <div class="relative w-full" style="max-height: 400px;">
                <canvas id="income-expense-chart"></canvas>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <h3 class="mb-4 text-sm font-semibold text-slate-900 dark:text-slate-100">存款进度</h3>
            <div class="relative w-full" style="max-height: 400px;">
                <canvas id="savings-chart"></canvas>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
window.fundChart = function () {
    return {
        year: '{{ now()->year }}',
        loading: false,
        incomeExpenseChart: null,
        savingsChart: null,

        init() {
            this.loadChartData();
        },

        async loadChartData() {
            this.loading = true;
            try {
                const res = await fetch(`{{ route('funds.chart-data') }}?year=${this.year}`);
                const data = await res.json();
                this.renderIncomeExpense(data);
                this.renderSavings(data);
            } finally {
                this.loading = false;
            }
        },

        renderIncomeExpense(data) {
            const labels = data.map(d => {
                const m = new Date(d.month).getMonth() + 1;
                return m + '月';
            });
            const incomes = data.map(d => parseFloat(d.income) || 0);
            const expenses = data.map(d => parseFloat(d.expense) || 0);
            const nets = data.map(d => (parseFloat(d.income) || 0) - (parseFloat(d.expense) || 0));

            const ctx = document.getElementById('income-expense-chart').getContext('2d');
            if (this.incomeExpenseChart) this.incomeExpenseChart.destroy();

            this.incomeExpenseChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: '收入',
                            data: incomes,
                            backgroundColor: 'rgba(16, 185, 129, 0.7)',
                            borderColor: 'rgba(16, 185, 129, 1)',
                            borderWidth: 1,
                            borderRadius: 4,
                        },
                        {
                            label: '支出',
                            data: expenses,
                            backgroundColor: 'rgba(239, 68, 68, 0.7)',
                            borderColor: 'rgba(239, 68, 68, 1)',
                            borderWidth: 1,
                            borderRadius: 4,
                        },
                        {
                            label: '净额',
                            data: nets,
                            type: 'line',
                            borderColor: 'rgba(59, 130, 246, 1)',
                            backgroundColor: 'rgba(59, 130, 246, 0.1)',
                            borderWidth: 2,
                            pointRadius: 4,
                            pointBackgroundColor: 'rgba(59, 130, 246, 1)',
                            tension: 0.3,
                            fill: false,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: { usePointStyle: true, padding: 20 }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { callback: v => '¥' + v.toLocaleString() }
                        }
                    }
                }
            });
        },

        renderSavings(data) {
            const valid = data.filter(d => d.savings_target !== null && d.savings_target > 0);
            const labels = valid.map(d => {
                const m = new Date(d.month).getMonth() + 1;
                return m + '月';
            });
            const targets = valid.map(d => parseFloat(d.savings_target) || 0);
            const actuals = valid.map(d => parseFloat(d.savings_actual) || 0);

            const ctx = document.getElementById('savings-chart').getContext('2d');
            if (this.savingsChart) this.savingsChart.destroy();

            this.savingsChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: '目标',
                            data: targets,
                            borderColor: 'rgba(148, 163, 184, 1)',
                            borderDash: [6, 4],
                            borderWidth: 2,
                            pointRadius: 3,
                            pointBackgroundColor: 'rgba(148, 163, 184, 1)',
                            fill: false,
                        },
                        {
                            label: '实际',
                            data: actuals,
                            borderColor: 'rgba(16, 185, 129, 1)',
                            backgroundColor: 'rgba(16, 185, 129, 0.1)',
                            borderWidth: 2,
                            pointRadius: 4,
                            pointBackgroundColor: 'rgba(16, 185, 129, 1)',
                            tension: 0.3,
                            fill: true,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: { usePointStyle: true, padding: 20 }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { callback: v => '¥' + v.toLocaleString() }
                        }
                    }
                }
            });
        }
    };
};
</script>
@endpush
