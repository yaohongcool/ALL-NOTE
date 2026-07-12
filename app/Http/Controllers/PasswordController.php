<?php

namespace App\Http\Controllers;

use App\Http\Requests\Password\StorePasswordRequest;
use App\Http\Requests\Password\UpdatePasswordRequest;
use App\Models\Password;
use App\Services\PasswordCipherService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PasswordController extends Controller
{
    public function __construct(
        protected PasswordCipherService $cipher
    ) {
        $this->middleware(function ($request, $next) {
            $method = $request->route()->getActionMethod();

            if (in_array($method, ['store', 'update', 'reveal'])) {
                if (! $this->cipher->hasMasterKeyInSession()) {
                    $user = auth()->user();

                    if ($user && $user->master_password_hash) {
                        return redirect()->route('master-password.verify')
                            ->with('warning', '请验证主密码后继续操作。');
                    }

                    return redirect()->route('master-password.setup')
                        ->with('warning', '请先设置主密码。');
                }
            }

            return $next($request);
        });
    }

    public function index(): View
    {
        $user = auth()->user();

        $passwords = $user->passwords()
            ->select(['id', 'user_id', 'name', 'account', 'phone', 'email', 'note', 'created_at', 'updated_at'])
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
            'encrypted_password' => $this->cipher->encryptWithSession($data['password']),
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
            $payload['encrypted_password'] = $this->cipher->encryptWithSession($data['password']);
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

    public function reveal(Password $password, Request $request): JsonResponse
    {
        $this->authorizePassword($password);

        $request->validate([
            'password' => ['required', 'string'],
        ]);

        if (! Hash::check($request->password, auth()->user()->password)) {
            return response()->json([
                'message' => '登录密码验证失败，请重新输入。',
            ], 422);
        }

        try {
            $plainPassword = $this->cipher->decryptWithSession(
                $password->encrypted_password,
                $password->id
            );

            return response()->json([
                'password' => $plainPassword,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => $e->getMessage() ?: '读取密码失败，请稍后重试。',
            ], 422);
        }
    }

    protected function authorizePassword(Password $password): void
    {
        abort_unless($password->user_id === auth()->id(), 403);
    }
}
