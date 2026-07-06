<?php

namespace App\Http\Requests\Fund;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFundSkinRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'cost' => ['required', 'numeric', 'min:0'],
            'uu_price' => ['nullable', 'numeric'],
            'buff_price' => ['nullable', 'numeric'],
            'daily_rental' => ['nullable', 'numeric'],
            'note' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => '请输入虚拟资产名称。',
            'name.max' => '虚拟资产名称不能超过100个字符。',
            'cost.required' => '请输入虚拟资产成本。',
            'cost.numeric' => '成本必须是数字。',
            'cost.min' => '成本不能为负数。',
            'uu_price.numeric' => 'UU价格必须是数字。',
            'buff_price.numeric' => 'Buff价格必须是数字。',
            'daily_rental.numeric' => '日租金必须是数字。',
        ];
    }
}
