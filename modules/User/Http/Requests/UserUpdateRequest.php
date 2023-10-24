<?php

namespace Modules\User\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserUpdateRequest extends FormRequest
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
            'email' => 'required|string|email|unique:users,email,' . app('request')->get('id'),
            'phone' => 'required|string|regex:/(01)[0-9]{9}/|unique:users,phone,' . app('request')->get('id'),
            'password' => "nullable|min:6|same:password_confirm|different:password_current",
            'password_confirm' => "nullable|required_with:password|min:6|same:password",
            'password_current' => "nullable|required_with:password"
        ];
    }

    public function messages()
    {
        return [];
    }
}
