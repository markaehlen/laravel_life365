<?php

namespace App\Http\Requests\Admin;

use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;


class SettingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Auth::guard('admin')->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'app_name' => 'bail|required|string|max:100',
            'business_email' => 'bail|nullable|string|email|max:100',
            'business_phone' => ['required','regex:^(\([0-9]{3}\) |[0-9]{3}-)[0-9]{3}-[0-9]{4}$^'],
            'business_address' => 'bail|nullable|string|max:100',
            'from_email' => 'bail|required|email|max:100',
            'email_from_name' => 'bail|required|string|max:100',
            'facebook_url' => 'bail|nullable|url|max:100',
            'twitter_url' => 'bail|nullable|url|max:100',
            'linkedin_url' => 'bail|nullable|url|max:100',
            'youtube_url' => 'bail|nullable|url|max:100',
            'logo' => 'bail|nullable|image',
            'favicon' => 'bail|nullable|image',
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
            if ($this->hasFile('logo')) {
                if ($this->file('logo')->getSize() > '3145728') {
                    $validator->errors()->add('logo', 'Logo shouldn\'t be greater than 3 MB. Please select another photo.');
                }
            }

            if ($this->hasFile('favicon')) {
                if ($this->file('favicon')->getSize() > '1048576') {
                    $validator->errors()->add('favicon', 'Favicon shouldn\'t be greater than 1 MB. Please select another photo.');
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
            'logo.image' => 'The logo must be an image.',
            'favicon.image' => 'The favicon must be an image.',
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
            'logo' => 'logo',
            'favicon' => 'favicon'
        ];
    }
}
