<?php

namespace App\Http\Requests\Admin;

use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;


class EcoparameterRequest extends FormRequest
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
                        'name' => 'bail|required|string|max:100|unique:ecoparameters,name',
                    ];
                }
            case 'PUT':
            case 'PATCH': {
                    return [
                        'name' => 'bail|required|string|max:100|unique:ecoparameters,name,' . $id . ',id',
                    ];
                }
            default:
                break;
        }
    }
}
