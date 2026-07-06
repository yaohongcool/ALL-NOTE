@props(['editRoute', 'deleteRoute', 'deleteConfirm' => '确定删除吗？'])
<div class="flex justify-end gap-2">
    <a href="{{ $editRoute }}" class="rounded-xl border border-slate-200 px-3 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-100 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800">
        编辑
    </a>
    <form method="POST" action="{{ $deleteRoute }}" onsubmit="return confirm('{{ $deleteConfirm }}');">
        @csrf
        @method('DELETE')
        <button type="submit" class="rounded-xl border border-red-200 px-3 py-2 text-sm font-medium text-red-600 transition hover:bg-red-50 dark:border-red-900 dark:text-red-400 dark:hover:bg-red-950/40">
            删除
        </button>
    </form>
</div>
