<?php

namespace App\Http\Requests\Auth;

use App\Rules\StrongPassword;
use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'username' => ['required', 'string', 'min:2', 'max:50', 'regex:/^[\p{L}\p{N}_-]+$/u', 'unique:users,username'],
            'password' => ['required', 'string', 'confirmed', new StrongPassword()],
        ];
    }

    public function messages(): array
    {
        return [
            'username.required' => '请输入用户名。',
            'username.min' => '用户名长度不能少于 2 位。',
            'username.max' => '用户名长度不能超过 50 位。',
            'username.regex' => '用户名只能包含中文、字母、数字、横线和下划线。',
            'username.unique' => '该用户名已被占用，请更换。',
            'password.required' => '请输入密码。',
            'password.confirmed' => '两次输入的密码不一致。',
        ];
    }
}
