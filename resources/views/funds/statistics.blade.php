@extends('layouts.app', [
    'title' => '统计图表 - 全录笔记',
    'headerTitle' => '统计图表',
    'headerTxt' => '',
    'breadcrumb' => [
        ['label' => '首页', 'url' => route('dashboard')],
        ['label' => '资金记录', 'url' => route('funds.index')],
        ['label' => '统计图表'],
    ],
])

@section('content')
<div class="space-y-6" id="chart-app">
    <div class="flex items-center gap-4">
        <span class="text-sm font-medium text-slate-700 dark:text-slate-200">选择年份</span>
        <select id="year-select"
            class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm text-slate-900 outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:focus:border-blue-500 dark:focus:ring-blue-950"
        >
            @foreach($years as $y)
                <option value="{{ $y }}">{{ $y }} 年</option>
            @endforeach
        </select>
        <span id="loading-text" class="text-sm text-slate-400" style="display:none">加载中...</span>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
        <h3 class="mb-4 text-sm font-semibold text-slate-900 dark:text-slate-100">存款总数趋势</h3>
        <div class="relative w-full" style="height: 350px;">
            <canvas id="cumulative-chart"></canvas>
        </div>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
        <h3 class="mb-4 text-sm font-semibold text-slate-900 dark:text-slate-100">每月环比增长数</h3>
        <div class="relative w-full" style="height: 350px;">
            <canvas id="growth-chart"></canvas>
        </div>
    </div>
</div>

<script>
(function() {
    var chart1 = null;
    var chart2 = null;

    function renderCumulative(data) {
        var labels = data.map(function(d) { return new Date(d.month).getMonth() + 1 + '月'; });
        var amounts = data.map(function(d) { return d.income; });
        var ctx = document.getElementById('cumulative-chart').getContext('2d');
        if (chart1) { chart1.destroy(); }
        chart1 = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: '存款累计',
                    data: amounts,
                    borderColor: 'rgba(16, 185, 129, 1)',
                    backgroundColor: 'rgba(16, 185, 129, 0.15)',
                    borderWidth: 2,
                    pointRadius: 4,
                    pointBackgroundColor: 'rgba(16, 185, 129, 1)',
                    tension: 0.3,
                    fill: true,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, ticks: { stepSize: 5000, callback: function(v) { return '¥' + v.toLocaleString(); } } }
                }
            }
        });
    }

    function renderGrowth(data) {
        var labels = [];
        var values = [];
        var colors = [];
        data.forEach(function(d) {
            if (d.growth === null) return;
            labels.push(new Date(d.month).getMonth() + 1 + '月');
            values.push(d.growth);
            colors.push(d.growth >= 0 ? 'rgba(16, 185, 129, 0.7)' : 'rgba(239, 68, 68, 0.7)');
        });
        var ctx = document.getElementById('growth-chart').getContext('2d');
        if (chart2) { chart2.destroy(); }
        chart2 = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: '环比增长',
                    data: values,
                    backgroundColor: colors,
                    borderColor: colors,
                    borderWidth: 1,
                    borderRadius: 4,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, ticks: { stepSize: 1000, callback: function(v) { return '¥' + v.toLocaleString(); } } }
                }
            }
        });
    }

    function loadData(year) {
        var loading = document.getElementById('loading-text');
        loading.style.display = '';
        fetch('{{ route('funds.chart-data') }}?year=' + year)
            .then(function(r) { return r.json(); })
            .then(function(data) {
                renderCumulative(data);
                renderGrowth(data);
            })
            .finally(function() {
                loading.style.display = 'none';
            });
    }

    document.addEventListener('DOMContentLoaded', function() {
        var sel = document.getElementById('year-select');
        if (sel) {
            loadData(sel.value);
            sel.addEventListener('change', function() { loadData(this.value); });
        }
    });
})();
</script>
@endsection
