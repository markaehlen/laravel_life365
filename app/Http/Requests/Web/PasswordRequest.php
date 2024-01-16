<?php

namespace App\Http\Requests\Web;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;


class PasswordRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Auth::guard('user')->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'old_password'     => 'bail|required|string|min:8|max:12',
            'new_password'     => 'bail|required|string|min:8|max:12',
            'confirm_password' => 'bail|required|string|same:new_password',
        ];
    }
    /**
     * Configure the validator instance.
     *
     * @param \Illuminate\Validation\Validator $validator
     *
     * @return void
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if (strcmp($this->old_password, $this->new_password) == 0) {
                $validator->errors()->add('new_password', 'New Password cannot be same as your old password. Please choose a different password.');
            }
            $user = Auth::guard('user')->user();
            if (!Hash::check($this->old_password, $user->password)) {
                $validator->errors()->add('old_password', 'The old password is incorrect.');
            }
        });
    }
}
