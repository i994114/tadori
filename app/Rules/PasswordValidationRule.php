<?php

namespace App\Rules;

use Illuminate\Support\Facades\Hash;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class PasswordValidationRule implements Rule
{
    protected $customMessage;
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($message = null)
    {
        $this->customMessage = $message;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $user = Auth::guard()->user();

        return Hash::check($value, $user->password);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return  $this->customMessage ?? '古いパスワードが正しくありません';
    }
}
