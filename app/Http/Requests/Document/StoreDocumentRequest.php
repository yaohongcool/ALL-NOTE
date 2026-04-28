<?php

namespace App\Http\Requests\Document;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreDocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'category' => ['required', 'string', Rule::in(['身份证', '驾驶证', '护照', '其它'])],
            'due_date' => ['nullable', 'date'],
            'note' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => '请输入姓名。',
            'category.required' => '请选择分类。',
            'category.in' => '证照分类无效。',
            'due_date.date' => '到期日期格式不正确。',
        ];
    }
}