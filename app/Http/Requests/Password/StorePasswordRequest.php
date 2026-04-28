<?php

namespace App\Http\Requests\Password;

use Illuminate\Foundation\Http\FormRequest;

class StorePasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:150'],
            'account' => ['required', 'string', 'max:150'],
            'password' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:150'],
            'note' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => '请输入名称。',
            'account.required' => '请输入账号。',
            'password.required' => '请输入密码。',
            'email.email' => '绑定邮箱格式不正确。',
        ];
    }
}