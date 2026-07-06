<?php

namespace App\Http\Requests\Asset;

use App\Enums\AssetCategory;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAssetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        $categories = AssetCategory::values();
        $category = $this->input('category');

        $rules = [
            'category' => ['required', 'string', Rule::in($categories)],
            'name' => ['required', 'string', 'max:150'],
            'due_date' => ['nullable', 'date'],
            'note' => ['nullable', 'string'],
        ];

        if ($category === AssetCategory::Physical->value) {
            $rules['cpu_model'] = ['nullable', 'string', 'max:100'];
            $rules['gpu_model'] = ['nullable', 'string', 'max:100'];
            $rules['memory'] = ['nullable', 'string', 'max:100'];
            $rules['storage_1'] = ['nullable', 'string', 'max:100'];
            $rules['storage_2'] = ['nullable', 'string', 'max:100'];
            $rules['storage_3'] = ['nullable', 'string', 'max:100'];
        }

        if ($category === AssetCategory::Server->value) {
            $rules['due_date'] = ['required', 'date'];
            $rules['cpu_cores'] = ['nullable', 'string', Rule::in(['2', '4', '8', '16'])];
            $rules['memory_size'] = ['nullable', 'string', Rule::in(['2GB', '4GB', '8GB', '16GB'])];
            $rules['ip_address'] = ['nullable', 'string', 'max:100'];
            $rules['operating_system'] = ['nullable', 'string', 'max:100'];
            $rules['provider'] = ['nullable', 'string', 'max:100'];
        }

        if ($category === AssetCategory::Domain->value) {
            $rules['due_date'] = ['required', 'date'];
            $rules['domain_address'] = ['required', 'string', 'max:150'];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'category.required' => '请选择资产分类。',
            'category.in' => '资产分类无效。',
            'name.required' => '请输入资产名称。',
            'due_date.required' => '该分类必须填写到期日期。',
            'due_date.date' => '到期日期格式不正确。',
            'domain_address.required' => '请输入域名地址。',
            'cpu_cores.in' => 'CPU 核心数无效。',
            'memory_size.in' => '内存大小无效。',
        ];
    }
}