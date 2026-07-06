<?php

namespace App\Http\Requests\Fund;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFundAccountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:50'],
            'balance' => ['required', 'numeric', 'min:0'],
            'note' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => '请输入账户名称。',
            'name.max' => '账户名称不能超过50个字符。',
            'balance.required' => '请输入余额。',
            'balance.numeric' => '余额必须是数字。',
            'balance.min' => '余额不能为负数。',
        ];
    }
}
