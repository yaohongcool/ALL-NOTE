<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class ForgotPasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'username' => ['required', 'string', 'exists:users,username'],
            'master_password' => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'username.required' => '请输入用户名。',
            'username.exists' => '该用户名不存在。',
            'master_password.required' => '请输入主密码。',
        ];
    }
}
