<?php

namespace App\Http\Requests\Admin;

use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;


class UnitRequest extends FormRequest
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
                        'name' => 'bail|required|string|max:100|unique:units,name',
                        'system_id' => 'numeric|required',
                    ];
                }
            case 'PUT':
            case 'PATCH': {
                    return [
                        'name' => 'bail|required|string|max:100|unique:units,name,' . $id . ',id',
                        'system_id' => 'numeric|required',
                    ];
                }
            default:
                break;
        }
    }
}
