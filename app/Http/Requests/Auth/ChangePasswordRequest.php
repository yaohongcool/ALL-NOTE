<?php

namespace App\Http\Requests\Auth;

use App\Rules\StrongPassword;
use Illuminate\Foundation\Http\FormRequest;

class ChangePasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'old_password' => ['required', 'string'],
            'password' => ['required', 'string', 'confirmed', new StrongPassword()],
        ];
    }

    public function messages(): array
    {
        return [
            'old_password.required' => '请输入旧密码。',
            'password.required' => '请输入新密码。',
            'password.confirmed' => '两次输入的新密码不一致。',
        ];
    }
}
