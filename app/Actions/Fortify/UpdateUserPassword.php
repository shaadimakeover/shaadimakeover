<?php

namespace App\Actions\Fortify;

use App\Rules\NewOldPasswordNotSame;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\UpdatesUserPasswords;

class UpdateUserPassword implements UpdatesUserPasswords
{
    use PasswordValidationRules;

    /**
     * Validate and update the user's password.
     *
     * @param  mixed  $user
     * @param  array  $input
     * @return void
     */
    public function update($user, array $input)
    {

        Validator::make($input, [
            'current_password' => ['required', 'string'],
            'password' => ['required', 'min:8', new NewOldPasswordNotSame($input['current_password'])],
            'password_confirmation' => ['required', 'same:password', 'min:8']
        ])->after(function ($validator) use ($user, $input) {
            if (!isset($input['current_password']) || !Hash::check($input['current_password'], $user->password)) {
                $validator->errors()->add('current_password', __('The provided password does not match your current password.'));
            }
        })->validateWithBag('updatePassword');

        $user->forceFill([
            'password' => $input['password']
        ])->save();
    }
}
