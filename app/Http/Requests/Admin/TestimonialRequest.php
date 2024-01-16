<?php

namespace App\Http\Requests\Admin;

use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;


class TestimonialRequest extends FormRequest
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
                        'title' => 'bail|required|string|max:100|unique:testimonials,title',
                        'name' => 'bail|required|string|max:100',
                        'description' => 'bail|required|string|max:1000',
                        'photo' => 'bail|nullable|image'
                    ];
                }
            case 'PUT':
            case 'PATCH': {
                    return [
                        'title' => 'bail|required|string|max:100|unique:testimonials,title,' . $id . ',id',
                        'name' => 'bail|required|string|max:100',
                        'description' => 'bail|required|string|max:1000',
                        'photo' => 'bail|nullable|image'
                    ];
                }
            default:
                break;
        }
    }
}
