<?php

namespace App\Http\Requests\Web;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class ProfileRequest extends FormRequest
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
        $id = Auth::guard('user')->id();
        return [
            'first_name' => 'bail|required|string|max:50',
            'last_name' => 'bail|required|string|max:50',
            'email' => 'bail|required|email|max:100|unique:users,email,' . $id . ',id',
            // 'phone' => 'bail|required|regex:/[0-9]{7,15}/|unique:users,phone,' . $id . ',id',           
        ];
    }
}
