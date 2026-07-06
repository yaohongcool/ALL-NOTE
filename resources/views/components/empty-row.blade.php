@props(['colspan' => 1, 'message' => '暂无数据。'])
<tr>
    <td colspan="{{ $colspan }}" class="px-4 py-10 text-center align-middle text-sm text-slate-500 dark:text-slate-400">
        {{ $message }}
    </td>
</tr>
