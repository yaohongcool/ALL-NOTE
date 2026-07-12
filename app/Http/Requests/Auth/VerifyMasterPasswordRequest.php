<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class VerifyMasterPasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'master_password' => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'master_password.required' => '请输入主密码。',
        ];
    }
}
