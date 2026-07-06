<?php

namespace App\Http\Requests\Fund;

use App\Enums\FundRentalType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateFundRentalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'type' => ['required', 'string', Rule::in(FundRentalType::values())],
            'rate' => ['required', 'numeric'],
            'discount' => ['nullable', 'numeric'],
            'lease_days' => ['required', 'integer', 'min:1'],
            'offhand_days' => ['nullable', 'integer', 'min:0'],
            'fee_rate' => ['nullable', 'numeric'],
            'note' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'type.required' => '请选择租赁类型。',
            'type.in' => '租赁类型无效。',
            'rate.required' => '请输入租赁费率。',
            'rate.numeric' => '租赁费率必须是数字。',
            'lease_days.required' => '请输入租赁天数。',
            'lease_days.integer' => '租赁天数必须是整数。',
            'lease_days.min' => '租赁天数至少为1天。',
            'offhand_days.integer' => '暂缓天数必须是整数。',
            'offhand_days.min' => '暂缓天数不能为负数。',
            'discount.numeric' => '折扣必须是数字。',
            'fee_rate.numeric' => '手续费率必须是数字。',
        ];
    }
}
