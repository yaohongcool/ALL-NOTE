<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\SetupMasterPasswordRequest;
use App\Http\Requests\Auth\VerifyMasterPasswordRequest;
use App\Services\PasswordCipherService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;

class MasterPasswordController extends Controller
{
    public function __construct(
        protected PasswordCipherService $cipher
    ) {
    }

    public function showVerify(): View
    {
        return view('auth.verify-master');
    }

    public function verify(VerifyMasterPasswordRequest $request): RedirectResponse
    {
        $user = auth()->user();

        if (! $user || ! Hash::check($request->master_password, $user->master_password_hash)) {
            return back()->withErrors([
                'master_password' => '主密码错误，请重新输入。',
            ]);
        }

        $this->cipher->cacheMasterKeyInSession($request->master_password);

        return redirect()->intended(route('passwords.index'))
            ->with('success', '主密码验证成功。');
    }

    public function showSetup(): View
    {
        return view('auth.setup-master');
    }

    public function setup(SetupMasterPasswordRequest $request): RedirectResponse
    {
        $user = auth()->user();

        $user->master_password_hash = Hash::make($request->master_password);
        $user->master_password_set_at = now();
        $user->save();

        $count = $this->cipher->migrateUserToV2($user, $request->master_password);

        $this->cipher->cacheMasterKeyInSession($request->master_password);

        $message = '主密码设置成功。';
        if ($count > 0) {
            $message .= " 已迁移 {$count} 条密码记录。";
        }

        return redirect()->route('passwords.index')
            ->with('success', $message);
    }
}
