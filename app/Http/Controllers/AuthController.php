<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\ChangePasswordRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use App\Services\UserBootstrapService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;

class AuthController extends Controller
{
    public function __construct(
        protected UserBootstrapService $userBootstrapService
    ) {
    }

    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function showChangePassword(): View
    {
        return view('auth.change-password');
    }

    public function login(LoginRequest $request): RedirectResponse
    {
        $key = $request->throttleKey();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);

            return back()
                ->withInput($request->only('username'))
                ->withErrors([
                    'username' => "登录失败次数过多，请在 {$seconds} 秒后重试。",
                ]);
        }

        $credentials = $request->validated();

        if (! Auth::attempt([
            'username' => $credentials['username'],
            'password' => $credentials['password'],
        ], $request->boolean('remember'))) {
            RateLimiter::hit($key, 300);

            return back()
                ->withInput($request->only('username'))
                ->withErrors([
                    'username' => '用户名或密码错误，请重新输入。',
                ]);
        }

        RateLimiter::clear($key);

        $request->session()->regenerate();

        return redirect()->route('dashboard')
            ->with('success', '登录成功，欢迎回来。');
    }

    public function changePassword(ChangePasswordRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $user = User::query()
            ->where('username', $data['username'])
            ->first();

        if (! $user || ! Hash::check($data['old_password'], $user->password)) {
            return back()
                ->withInput($request->only('username'))
                ->withErrors([
                    'old_password' => '用户名或旧密码错误，请重新输入。',
                ]);
        }

        if (Hash::check($data['password'], $user->password)) {
            return back()
                ->withInput($request->only('username'))
                ->withErrors([
                    'password' => '新密码不能与旧密码相同。',
                ]);
        }

        $user->update([
            'password' => $data['password'],
        ]);

        return redirect()->route('login')
            ->with('success', '密码修改成功，请使用新密码登录。');
    }

    public function showRegister(): View
    {
        return view('auth.register');
    }

    public function register(RegisterRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $user = DB::transaction(function () use ($data) {
            $user = \App\Models\User::create([
                'username' => $data['username'],
                'password' => Hash::make($data['password']),
            ]);

            $this->userBootstrapService->bootstrap($user);

            return $user;
        });

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('dashboard')
            ->with('success', '注册成功，已自动登录，并已为你初始化数据。');
    }

    public function logout(): RedirectResponse
    {
        Auth::logout();

        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', '你已安全退出登录。');
    }
}
