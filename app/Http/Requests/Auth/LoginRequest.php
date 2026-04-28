<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'username' => ['required', 'string', 'max:50'],
            'password' => ['required', 'string'],
            'remember' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'username.required' => '请输入用户名。',
            'password.required' => '请输入密码。',
        ];
    }

    public function throttleKey(): string
    {
        return strtolower((string) $this->input('username')) . '|' . $this->ip();
    }
}