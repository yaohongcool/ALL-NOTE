<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;

class PasswordCipherService
{
    const V2_PREFIX = 'v2|';

    public function encrypt(string $value, string $masterPassword): string
    {
        $key = $this->deriveKey($masterPassword);
        $iv = random_bytes(16);
        $encrypted = openssl_encrypt($value, 'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv);

        return self::V2_PREFIX . base64_encode($iv . $encrypted);
    }

    public function decrypt(string $value, string $masterPassword, int $passwordId = -1): string
    {
        if ($value === '') {
            return '';
        }

        try {
            if (str_starts_with($value, self::V2_PREFIX)) {
                $key = $this->deriveKey($masterPassword);
                $data = base64_decode(substr($value, 3));
                $iv = substr($data, 0, 16);
                $encrypted = substr($data, 16);

                $result = openssl_decrypt($encrypted, 'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv);

                if ($result === false) {
                    throw new \Exception('主密码解密失败');
                }

                return $result;
            }

            return Crypt::decryptString($value);
        } catch (\Throwable $e) {
            Log::warning('密码解密失败', [
                'password_id' => $passwordId,
                'exception_class' => get_class($e),
            ]);

            throw $e;
        }
    }

    public function decryptWithSession(string $value, int $passwordId = -1): string
    {
        $masterPassword = $this->getMasterPasswordFromSession();
        if ($masterPassword === null) {
            throw new \Exception('请先验证主密码。');
        }

        return $this->decrypt($value, $masterPassword, $passwordId);
    }

    public function encryptWithSession(string $value): string
    {
        $masterPassword = $this->getMasterPasswordFromSession();
        if ($masterPassword === null) {
            throw new \Exception('请先验证主密码。');
        }

        return $this->encrypt($value, $masterPassword);
    }

    private function deriveKey(string $masterPassword): string
    {
        return hash_hmac('sha256', $masterPassword, config('app.key'), true);
    }

    public function cacheMasterKeyInSession(string $masterPassword): void
    {
        $encrypted = Crypt::encryptString($masterPassword);
        session()->put('master_password', $encrypted);
    }

    public function getMasterPasswordFromSession(): ?string
    {
        $encrypted = session('master_password');
        if ($encrypted === null) {
            return null;
        }

        try {
            return Crypt::decryptString($encrypted);
        } catch (\Throwable $e) {
            return null;
        }
    }

    public function clearMasterKeyFromSession(): void
    {
        session()->forget('master_password');
    }

    public function hasMasterKeyInSession(): bool
    {
        return session()->has('master_password');
    }

    public function migrateUserToV2(User $user, string $masterPassword): int
    {
        $count = 0;
        $passwords = $user->passwords()
            ->where('encrypted_password', 'not like', self::V2_PREFIX . '%')
            ->get();

        foreach ($passwords as $password) {
            try {
                $plaintext = Crypt::decryptString($password->encrypted_password);
                $password->update([
                    'encrypted_password' => $this->encrypt($plaintext, $masterPassword),
                ]);
                $count++;
            } catch (\Throwable $e) {
                Log::warning('密码迁移失败', [
                    'password_id' => $password->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return $count;
    }
}
