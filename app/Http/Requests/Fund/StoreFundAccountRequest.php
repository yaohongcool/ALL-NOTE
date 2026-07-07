<?php

namespace App\Http\Requests\Fund;

use Illuminate\Foundation\Http\FormRequest;

class StoreFundAccountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:50'],
            'balance' => ['required', 'numeric'],
            'note' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => '请输入账户名称。',
            'name.max' => '账户名称不能超过50个字符。',
            'balance.required' => '请输入金额。',
            'balance.numeric' => '金额必须是数字。',
        ];
    }
}
