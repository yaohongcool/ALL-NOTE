@extends('layouts.app', [
    'title' => '首页 - 全录笔记',
    'headerTitle' => '全录笔记',
    'headerTxt' => '一个简洁、安全，面向个人的多场景信息记录工具',
])

@section('content')
    @php
        $eventContentService = app(\App\Services\EventContentService::class);
    @endphp

    <div class="space-y-6">
        <section class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
            <x-stat-card
                title="密码总数"
                :value="$stats['passwords_count']"
                hint="当前登录用户的密码记录总数"
                :createUrl="route('passwords.create')"
                :manageUrl="route('passwords.index')"
            />

            <x-stat-card
                title="资产总数"
                :value="$stats['assets_count']"
                hint="当前登录用户的资产记录总数"
                :createUrl="route('assets.create')"
                :manageUrl="route('assets.index')"
            />

            <x-stat-card
                title="期限备忘总数"
                :value="$stats['documents_count']"
                hint="当前登录用户的期限备忘记录总数"
                :createUrl="route('documents.create')"
                :manageUrl="route('documents.index')"
            />

            <x-stat-card
                title="事件总数"
                :value="$stats['events_count']"
                hint="当前登录用户的事件记录总数"
                :createUrl="route('events.create')"
                :manageUrl="route('events.index')"
            />
        </section>

        

        <section class="grid grid-cols-1 items-start gap-6 xl:grid-cols-2">
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <div class="border-b border-slate-200 px-6 py-5 dark:border-slate-800">
                    <h2 class="text-lg font-semibold text-slate-900 dark:text-slate-100">IT资产</h2>
                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                        物理设备和数字资产一览
                    </p>
                </div>

                <div class="responsive-table-wrap overflow-x-auto">
                    <table class="responsive-table min-w-full divide-y divide-slate-200 dark:divide-slate-800">
                        <thead class="bg-slate-50 dark:bg-slate-800/60">
                            <tr>
                                <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                                    名称
                                </th>
                                <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                                    信息
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                            @forelse ($assetsOverview as $asset)
                                <tr
                                    class="cursor-pointer transition hover:bg-slate-50 dark:hover:bg-slate-800/40"
                                    onclick="window.location='{{ route('assets.index') }}'"
                                >
                                    <td data-label="名称" class="px-5 py-4 align-middle">
                                        <p class="text-sm font-semibold text-slate-900 dark:text-slate-100">
                                            {{ $asset->name }}
                                        </p>
                                        @if($asset->note)
                                            <p class="mt-1 max-w-xs truncate text-xs text-slate-500 dark:text-slate-400">
                                                {{ $asset->note }}
                                            </p>
                                        @endif
                                    </td>

                                    <td data-label="信息" class="px-5 py-4 align-middle">
                                        <span class="text-sm text-slate-600 dark:text-slate-300">
                                            {{ $asset->summary ?: '-' }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="px-5 py-12 text-center align-middle text-sm text-slate-500 dark:text-slate-400">
                                        暂无可展示内容。
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <div class="border-b border-slate-200 px-6 py-5 dark:border-slate-800">
                    <h2 class="text-lg font-semibold text-slate-900 dark:text-slate-100">期限备忘</h2>
                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                        证件、会员、物品和其它事项到期速览
                    </p>
                </div>

                <div class="responsive-table-wrap overflow-x-auto">
                    <table class="responsive-table min-w-full divide-y divide-slate-200 dark:divide-slate-800">
                        <thead class="bg-slate-50 dark:bg-slate-800/60">
                            <tr>
                                <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                                    名称
                                </th>
                                <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                                    分类
                                </th>
                                <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                                    状态
                                </th>
                                <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                                    距离到期
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                            @forelse ($reminders as $item)
                                @php
                                    $statusClass = match ($item['status']) {
                                        '正常' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300',
                                        '即将到期' => 'bg-amber-50 text-amber-700 dark:bg-amber-950/40 dark:text-amber-300',
                                        '已过期' => 'bg-red-50 text-red-700 dark:bg-red-950/40 dark:text-red-300',
                                        default => 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300',
                                    };

                                    $targetUrl = $item['type'] === 'document'
                                        ? route('documents.index')
                                        : route('assets.index');
                                @endphp

                                <tr
                                    class="cursor-pointer transition hover:bg-slate-50 dark:hover:bg-slate-800/40"
                                    onclick="window.location='{{ $targetUrl }}'"
                                >
                                    <td data-label="名称" class="px-5 py-4 align-middle">
                                        <p class="text-sm font-semibold text-slate-900 dark:text-slate-100">
                                            {{ $item['title'] }}
                                        </p>
                                        @if(!empty($item['note']))
                                            <p class="mt-1 max-w-xs truncate text-xs text-slate-500 dark:text-slate-400">
                                                {{ $item['note'] }}
                                            </p>
                                        @endif
                                    </td>

                                    <td data-label="分类" class="px-5 py-4 align-middle">
                                        <span class="text-sm text-slate-700 dark:text-slate-200">
                                            {{ $item['category'] }}
                                        </span>
                                    </td>

                                    <td data-label="状态" class="px-5 py-4 align-middle">
                                        <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $statusClass }}">
                                            {{ $item['status'] }}
                                        </span>
                                    </td>

                                    <td data-label="距离到期" class="px-5 py-4 align-middle">
                                        <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $statusClass }}">
                                            {{ $item['days_until_due_label'] }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-5 py-12 text-center align-middle text-sm text-slate-500 dark:text-slate-400">
                                        暂无可展示内容。
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <div class="flex flex-col gap-3 border-b border-slate-200 px-6 py-5 dark:border-slate-800 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-slate-900 dark:text-slate-100">事件一览</h2>
                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                        最近记录的事件
                    </p>
                </div>

                <a
                    href="{{ route('events.index') }}"
                    class="inline-flex items-center justify-center rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 transition hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200 dark:hover:bg-slate-800"
                >
                    查看全部
                </a>
            </div>

            <div class="responsive-table-wrap overflow-x-auto">
                <table class="responsive-table min-w-full divide-y divide-slate-200 dark:divide-slate-800">
                    <thead class="bg-slate-50 dark:bg-slate-800/60">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">标题</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">状态</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">来源/对象</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">发生日期</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">标签</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">记录数</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">内容</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">可见性（后期推出共享功能）</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                        @forelse ($recentEvents as $event)
                            @php
                                $statusClass = match ($event->status) {
                                    '已处理' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300',
                                    '处理中' => 'bg-blue-50 text-blue-700 dark:bg-blue-950/40 dark:text-blue-300',
                                    '待处理' => 'bg-amber-50 text-amber-700 dark:bg-amber-950/40 dark:text-amber-300',
                                    '无需处理' => 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300',
                                    default => 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300',
                                };

                                $visibilityClass = $event->visibility === 'public'
                                    ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-950/40 dark:text-indigo-300'
                                    : 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300';
                            @endphp
                            <tr
                                class="cursor-pointer transition hover:bg-slate-50 dark:hover:bg-slate-800/40"
                                onclick="window.location='{{ route('events.show', $event) }}'"
                            >
                                <td data-label="标题" class="px-4 py-4 align-middle">
                                    <a href="{{ route('events.show', $event) }}" class="text-sm font-semibold text-slate-900 transition hover:text-blue-700 dark:text-slate-100 dark:hover:text-blue-400">
                                        {{ $event->title }}
                                    </a>
                                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                                        创建于 {{ $event->created_at?->format('Y-m-d H:i') }}
                                    </p>
                                </td>

                                <td data-label="状态" class="px-4 py-4 align-middle">
                                    <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $statusClass }}">
                                        {{ $event->status }}
                                    </span>
                                </td>

                                <td data-label="来源/对象" class="px-4 py-4 align-middle">
                                    <span class="text-sm text-slate-600 dark:text-slate-300">
                                        {{ $event->subject ?: '-' }}
                                    </span>
                                </td>

                                <td data-label="发生日期" class="px-4 py-4 align-middle">
                                    <span class="text-sm text-slate-600 dark:text-slate-300">
                                        {{ $event->occurred_on?->format('Y-m-d') ?: '未记录' }}
                                    </span>
                                </td>

                                <td data-label="标签" class="px-4 py-4 align-middle">
                                    @if($event->tags->isNotEmpty())
                                        <div class="flex max-w-xs flex-wrap gap-1.5">
                                            @foreach ($event->tags->take(3) as $tag)
                                                <span class="rounded-full bg-slate-100 px-2 py-1 text-xs font-medium text-slate-600 dark:bg-slate-800 dark:text-slate-300">{{ $tag->name }}</span>
                                            @endforeach
                                            @if($event->tags->count() > 3)
                                                <span class="rounded-full bg-slate-100 px-2 py-1 text-xs font-medium text-slate-600 dark:bg-slate-800 dark:text-slate-300">+{{ $event->tags->count() - 3 }}</span>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-sm text-slate-400">-</span>
                                    @endif
                                </td>

                                <td data-label="记录数" class="px-4 py-4 align-middle">
                                    <span class="text-sm text-slate-600 dark:text-slate-300">
                                        {{ $event->records_count }}
                                    </span>
                                </td>

                                <td data-label="内容" class="px-4 py-4 align-middle">
                                    <span class="text-sm text-slate-600 dark:text-slate-300">
                                        {{ $eventContentService->textSummary($event->summaryRecord?->process) }}
                                    </span>
                                </td>

                                <td data-label="可见性" class="px-4 py-4 align-middle">
                                    <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $visibilityClass }}">
                                        {{ $event->visibility_label }}
                                    </span>
                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-4 py-10 text-center align-middle text-sm text-slate-500 dark:text-slate-400">
                                    暂无事件记录。
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
@endsection
