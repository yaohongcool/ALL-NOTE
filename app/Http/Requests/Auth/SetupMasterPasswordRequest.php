<?php

namespace App\Http\Requests\Auth;

use App\Rules\StrongPassword;
use Illuminate\Foundation\Http\FormRequest;

class SetupMasterPasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'master_password' => ['required', 'string', 'confirmed', new StrongPassword()],
        ];
    }

    public function messages(): array
    {
        return [
            'master_password.required' => '请输入主密码。',
            'master_password.confirmed' => '两次输入的主密码不一致。',
        ];
    }
}
