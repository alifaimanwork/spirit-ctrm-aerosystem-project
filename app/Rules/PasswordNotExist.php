<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use App\Models\User;

class PasswordNotExist implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {

        $user = User::where('staff_id', $value)->first();
        if ($user && !is_null($user->password) && $user->password !== '') {
            $fail("The {$value} already had login.");
        }
    }
}
