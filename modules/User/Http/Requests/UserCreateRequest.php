<?php

namespace Modules\User\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserCreateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|string|min:2|max:50',
            'email' => 'required|string|email|unique:users,email',
            'phone' => 'required|string|regex:/(01)[0-9]{9}/|unique:users,phone',
            'password' => 'required|string|confirmed|min:6',
        ];
    }

    public function messages()
    {
        return [];
    }
}
