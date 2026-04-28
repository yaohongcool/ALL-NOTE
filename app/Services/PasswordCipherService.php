<?php

namespace App\Services;

use Illuminate\Support\Facades\Crypt;

class PasswordCipherService
{
    public function encrypt(string $value): string
    {
        return Crypt::encryptString($value);
    }

    public function decrypt(?string $value): string
    {
        if (! $value) {
            return '';
        }

        try {
            return Crypt::decryptString($value);
        } catch (\Throwable $e) {
            return '[解密失败]';
        }
    }
}