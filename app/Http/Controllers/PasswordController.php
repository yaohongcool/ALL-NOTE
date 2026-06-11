<?php

namespace App\Http\Controllers;

use App\Http\Requests\Password\StorePasswordRequest;
use App\Http\Requests\Password\UpdatePasswordRequest;
use App\Models\Password;
use App\Services\PasswordCipherService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class PasswordController extends Controller
{
    public function __construct(
        protected PasswordCipherService $cipher
    ) {
    }

    public function index(): View
    {
        $user = auth()->user();

        $passwords = $user->passwords()
            ->orderByDesc('updated_at')
            ->orderByDesc('id')
            ->paginate(10);

        return view('passwords.index', [
            'passwords' => $passwords,
        ]);
    }

    public function create(): View
    {
        return view('passwords.create', [
            'password' => new Password(),
        ]);
    }

    public function store(StorePasswordRequest $request): RedirectResponse
    {
        $data = $request->validated();

        auth()->user()->passwords()->create([
            'name' => $data['name'],
            'account' => $data['account'],
            'encrypted_password' => $this->cipher->encrypt($data['password']),
            'phone' => $data['phone'] ?? null,
            'email' => $data['email'] ?? null,
            'note' => $data['note'] ?? null,
        ]);

        return redirect()->route('passwords.index')
            ->with('success', '密码记录已创建。');
    }

    public function edit(Password $password): View
    {
        $this->authorizePassword($password);

        return view('passwords.edit', [
            'password' => $password,
        ]);
    }

    public function update(UpdatePasswordRequest $request, Password $password): RedirectResponse
    {
        $this->authorizePassword($password);

        $data = $request->validated();

        $payload = [
            'name' => $data['name'],
            'account' => $data['account'],
            'phone' => $data['phone'] ?? null,
            'email' => $data['email'] ?? null,
            'note' => $data['note'] ?? null,
        ];

        if (filled($data['password'] ?? null)) {
            $payload['encrypted_password'] = $this->cipher->encrypt($data['password']);
        }

        $password->update($payload);

        return redirect()->route('passwords.index')
            ->with('success', '密码记录已更新。');
    }

    public function destroy(Password $password): RedirectResponse
    {
        $this->authorizePassword($password);

        $password->delete();

        return redirect()->route('passwords.index')
            ->with('success', '密码记录已删除。');
    }

    public function reveal(Password $password): JsonResponse
    {
        $this->authorizePassword($password);

        try {
            $plainPassword = $this->cipher->decrypt(
                $password->encrypted_password,
                $password->id
            );

            return response()->json([
                'password' => $plainPassword,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => '读取密码失败，请稍后重试。',
            ], 422);
        }
    }

    protected function authorizePassword(Password $password): void
    {
        abort_unless($password->user_id === auth()->id(), 403);
    }
}
