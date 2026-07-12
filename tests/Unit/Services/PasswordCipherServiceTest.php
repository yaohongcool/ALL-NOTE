<?php

namespace Tests\Unit\Services;

use App\Services\PasswordCipherService;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class PasswordCipherServiceTest extends TestCase
{
    public function test_encrypt_and_decrypt_roundtrip(): void
    {
        $service = $this->app->make(PasswordCipherService::class);
        $plaintext = 'MySecret@123';

        $encrypted = $service->encrypt($plaintext);
        $decrypted = $service->decrypt($encrypted);

        $this->assertSame($plaintext, $decrypted);
    }

    public function test_decrypt_empty_string_returns_empty(): void
    {
        $service = $this->app->make(PasswordCipherService::class);

        $result = $service->decrypt('');

        $this->assertSame('', $result);
    }

    public function test_decrypt_deleted_app_key_throws_and_logs(): void
    {
        $service = $this->app->make(PasswordCipherService::class);

        $cipher = config('app.cipher');
        $keyLength = ($cipher === 'AES-128-CBC' || $cipher === 'AES-128-GCM') ? 16 : 32;
        $otherEncrypter = new \Illuminate\Encryption\Encrypter(random_bytes($keyLength), $cipher);
        $ciphertext = $otherEncrypter->encryptString('sensitive-data');

        Log::spy();

        try {
            $service->decrypt($ciphertext);
            $this->fail('Expected exception was not thrown.');
        } catch (\Throwable $e) {
            Log::shouldHaveReceived('warning')
                ->once()
                ->withArgs(function (string $message, array $context) {
                    return $message === '密码解密失败'
                        && ($context['password_id'] ?? null) === -1;
                });
        }
    }

    public function test_decrypt_logs_password_id(): void
    {
        $service = $this->app->make(PasswordCipherService::class);
        $passwordId = 42;

        $cipher = config('app.cipher');
        $keyLength = ($cipher === 'AES-128-CBC' || $cipher === 'AES-128-GCM') ? 16 : 32;
        $otherEncrypter = new \Illuminate\Encryption\Encrypter(random_bytes($keyLength), $cipher);
        $ciphertext = $otherEncrypter->encryptString('some-data');

        Log::spy();

        try {
            $service->decrypt($ciphertext, $passwordId);
            $this->fail('Expected exception was not thrown.');
        } catch (\Throwable $e) {
            Log::shouldHaveReceived('warning')
                ->once()
                ->withArgs(function (string $message, array $context) use ($passwordId) {
                    return $message === '密码解密失败'
                        && ($context['password_id'] ?? null) === $passwordId;
                });
        }
    }
}
