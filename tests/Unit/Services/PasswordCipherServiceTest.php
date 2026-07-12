<?php

namespace Tests\Unit\Services;

use App\Services\PasswordCipherService;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class PasswordCipherServiceTest extends TestCase
{
    private string $masterPassword = 'TestMaster@123';

    public function test_encrypt_and_decrypt_roundtrip(): void
    {
        $service = $this->app->make(PasswordCipherService::class);
        $plaintext = 'MySecret@123';

        $encrypted = $service->encrypt($plaintext, $this->masterPassword);
        $this->assertStringStartsWith('v2|', $encrypted);

        $decrypted = $service->decrypt($encrypted, $this->masterPassword);

        $this->assertSame($plaintext, $decrypted);
    }

    public function test_decrypt_empty_string_returns_empty(): void
    {
        $service = $this->app->make(PasswordCipherService::class);

        $result = $service->decrypt('', $this->masterPassword);

        $this->assertSame('', $result);
    }

    public function test_decrypt_with_wrong_master_password_fails(): void
    {
        $service = $this->app->make(PasswordCipherService::class);

        $encrypted = $service->encrypt('sensitive-data', $this->masterPassword);

        Log::spy();

        try {
            $service->decrypt($encrypted, 'WrongMaster@456');
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

        $encrypted = $service->encrypt('some-data', $this->masterPassword);

        Log::spy();

        try {
            $service->decrypt($encrypted, 'WrongMaster@456', $passwordId);
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

    public function test_encrypt_unique_per_call(): void
    {
        $service = $this->app->make(PasswordCipherService::class);
        $plaintext = 'same-data';

        $e1 = $service->encrypt($plaintext, $this->masterPassword);
        $e2 = $service->encrypt($plaintext, $this->masterPassword);

        $this->assertNotSame($e1, $e2);

        $this->assertSame($plaintext, $service->decrypt($e1, $this->masterPassword));
        $this->assertSame($plaintext, $service->decrypt($e2, $this->masterPassword));
    }

    public function test_session_roundtrip(): void
    {
        $service = $this->app->make(PasswordCipherService::class);

        $service->cacheMasterKeyInSession($this->masterPassword);

        $this->assertTrue($service->hasMasterKeyInSession());

        $retrieved = $service->getMasterPasswordFromSession();
        $this->assertSame($this->masterPassword, $retrieved);

        $service->clearMasterKeyFromSession();
        $this->assertFalse($service->hasMasterKeyInSession());
    }
}
