<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class StrongPassword implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $password = (string) $value;

        if (mb_strlen($password) < 8) {
            $fail('密码长度不能少于 8 位。');
            return;
        }

        $types = 0;

        if (preg_match('/[A-Z]/', $password)) {
            $types++;
        }

        if (preg_match('/[a-z]/', $password)) {
            $types++;
        }

        if (preg_match('/[0-9]/', $password)) {
            $types++;
        }

        if (preg_match('/[^A-Za-z0-9]/', $password)) {
            $types++;
        }

        if ($types < 3) {
            $fail('密码必须至少包含大写字母、小写字母、数字、特殊字符中的 3 类。');
        }
    }
}