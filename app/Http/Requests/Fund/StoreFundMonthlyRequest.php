<?php

namespace App\Http\Requests\Fund;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreFundMonthlyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'month' => ['required', 'date'],
            'income' => ['required', 'numeric', 'min:0'],
            'expense' => ['required', 'numeric', 'min:0'],
            'savings_target' => ['nullable', 'numeric', 'min:0'],
            'savings_actual' => ['nullable', 'numeric', 'min:0'],
            'savings_status' => ['nullable', 'string', Rule::in(['completed', 'uncompleted', 'na'])],
            'note' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'month.required' => '请选择月份。',
            'month.date' => '月份格式不正确。',
            'income.required' => '请输入收入。',
            'income.numeric' => '收入必须是数字。',
            'income.min' => '收入不能为负数。',
            'expense.required' => '请输入支出。',
            'expense.numeric' => '支出必须是数字。',
            'expense.min' => '支出不能为负数。',
            'savings_target.numeric' => '储蓄目标必须是数字。',
            'savings_target.min' => '储蓄目标不能为负数。',
            'savings_actual.numeric' => '实际储蓄必须是数字。',
            'savings_actual.min' => '实际储蓄不能为负数。',
            'savings_status.in' => '储蓄状态无效。',
        ];
    }
}
