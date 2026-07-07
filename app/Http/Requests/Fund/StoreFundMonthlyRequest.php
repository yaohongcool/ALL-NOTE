<?php

namespace App\Http\Requests\Fund;

use Illuminate\Foundation\Http\FormRequest;

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
            'amount' => ['required', 'numeric', 'min:0'],
            'note' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'month.required' => '请选择月份。',
            'month.date' => '月份格式不正确。',
            'amount.required' => '请输入金额。',
            'amount.numeric' => '金额必须是数字。',
            'amount.min' => '金额不能为负数。',
        ];
    }
}
