<?php

namespace App\Http\Requests\Fund;

use App\Enums\FundBudgetType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreFundBudgetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:50'],
            'type' => ['required', 'string', Rule::in(FundBudgetType::values())],
            'monthly_amount' => ['nullable', 'numeric', 'required_without:annual_amount'],
            'annual_amount' => ['nullable', 'numeric', 'required_without:monthly_amount'],
            'note' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => '请输入预算名称。',
            'name.max' => '预算名称不能超过50个字符。',
            'type.required' => '请选择预算类型。',
            'type.in' => '预算类型无效。',
            'monthly_amount.required_without' => '月金额和年金额至少填写一项。',
            'annual_amount.required_without' => '年金额和月金额至少填写一项。',
            'monthly_amount.numeric' => '月金额必须是数字。',
            'annual_amount.numeric' => '年金额必须是数字。',
        ];
    }
}
