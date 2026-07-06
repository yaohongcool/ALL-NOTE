<?php

namespace App\Http\Requests\Fund;

use App\Enums\FundAccountType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
            'type' => ['required', 'string', Rule::in(FundAccountType::values())],
            'balance' => ['required', 'numeric', 'min:0'],
            'sort' => ['nullable', 'integer', 'min:0'],
            'note' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => '请输入账户名称。',
            'name.max' => '账户名称不能超过50个字符。',
            'type.required' => '请选择账户类型。',
            'type.in' => '账户类型无效。',
            'balance.required' => '请输入余额。',
            'balance.numeric' => '余额必须是数字。',
            'balance.min' => '余额不能为负数。',
            'sort.integer' => '排序值必须是整数。',
            'sort.min' => '排序值不能为负数。',
        ];
    }
}
