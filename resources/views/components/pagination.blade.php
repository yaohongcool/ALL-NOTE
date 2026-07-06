@props(['paginator'])
@if ($paginator->hasPages())
<div class="border-t border-slate-200 px-4 py-4 dark:border-slate-800">
    {{ $paginator->links() }}
</div>
@endif
