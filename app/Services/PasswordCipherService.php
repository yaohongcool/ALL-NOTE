<?php

namespace App\Services;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;

class PasswordCipherService
{
    public function encrypt(string $value): string
    {
        return Crypt::encryptString($value);
    }

    public function decrypt(string $value, int $passwordId = -1): string
    {
        if ($value === '') {
            return '';
        }

        try {
            return Crypt::decryptString($value);
        } catch (\Throwable $e) {
            Log::warning('密码解密失败', [
                'password_id' => $passwordId,
                'exception_class' => get_class($e),
            ]);

            throw $e;
        }
    }
}