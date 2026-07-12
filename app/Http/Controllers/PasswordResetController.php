<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;

class PasswordResetController extends Controller
{
    public function showForgot(): View
    {
        return view('auth.forgot-password');
    }

    public function verify(ForgotPasswordRequest $request): RedirectResponse
    {
        $user = User::where('username', $request->username)->first();

        if (! $user || ! $user->master_password_hash) {
            return back()
                ->withInput($request->only('username'))
                ->withErrors([
                    'master_password' => '该用户未设置主密码，无法通过此方式重置。请登录后使用"更改密码"功能。',
                ]);
        }

        if (! Hash::check($request->master_password, $user->master_password_hash)) {
            return back()
                ->withInput($request->only('username'))
                ->withErrors([
                    'master_password' => '主密码错误，请重新输入。',
                ]);
        }

        session()->put('password_reset_user', $user->id);

        return redirect()->route('password.reset')
            ->with('success', '主密码验证成功，请设置新密码。');
    }

    public function showReset(): View
    {
        if (! session()->has('password_reset_user')) {
            return redirect()->route('password.forgot')
                ->with('warning', '请先验证身份。');
        }

        $user = User::find(session('password_reset_user'));

        if (! $user) {
            return redirect()->route('password.forgot')
                ->with('warning', '用户不存在。');
        }

        return view('auth.reset-password', [
            'username' => $user->username,
        ]);
    }

    public function reset(ResetPasswordRequest $request): RedirectResponse
    {
        $verifiedUserId = session('password_reset_user');

        if (! $verifiedUserId) {
            return redirect()->route('password.forgot')
                ->with('warning', '请先验证身份。');
        }

        $user = User::find($verifiedUserId);

        if (! $user || $user->username !== $request->username) {
            return redirect()->route('password.forgot')
                ->with('warning', '验证已过期，请重新操作。');
        }

        if (! $user->master_password_hash) {
            return redirect()->route('password.forgot')
                ->with('warning', '该用户未设置主密码。');
        }

        if (! Hash::check($request->master_password, $user->master_password_hash)) {
            return redirect()->route('password.forgot')
                ->with('warning', '主密码验证已过期，请重新验证。');
        }

        $user->update([
            'password' => $request->password,
        ]);

        session()->forget('password_reset_user');

        return redirect()->route('login')
            ->with('success', '密码重置成功，请使用新密码登录。');
    }
}
