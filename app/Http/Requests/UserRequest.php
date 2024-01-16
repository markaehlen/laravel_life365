<?php

namespace App\Http\Requests;

use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;


class UserRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $id = $this->id;

        switch ($this->method()) {
            case 'GET': {
                    return [];
                }
            case 'DELETE': {
                    return [];
                }
            case 'POST': {
                    return [
                        'first_name' => 'bail|required|string|max:50',
                        'last_name' => 'bail|required|string|max:50',
                        'email' => 'bail|required|email:rfc,dns|max:100|unique:users,email',
                        'password'     => 'bail|required|string|min:8|max:12|confirmed',
                        'password_confirmation' => 'required'
                    ];
                }
            case 'PUT':
            case 'PATCH':
            default:
                break;
        }
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
            if ($this->hasFile('photo')) {
                if ($this->file('photo')->getSize() > '5242880') {
                    $validator->errors()->add('photo', 'Photo shouldn\'t be greater than 5 MB. Please select another photo.');
                }
            }
        });
    }

    /**
     * Get the validation messages that apply to the request.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'photo.image' => 'The photo must be an image.',
            'password_confirmation.required' => 'The password confirmation field is required',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'photo' => 'photo'
        ];
    }
}
