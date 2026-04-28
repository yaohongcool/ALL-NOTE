<?php

namespace App\Http\Requests\Password;

class UpdatePasswordRequest extends StorePasswordRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:150'],
            'account' => ['required', 'string', 'max:150'],
            'password' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:150'],
            'note' => ['nullable', 'string'],
        ];
    }
}
