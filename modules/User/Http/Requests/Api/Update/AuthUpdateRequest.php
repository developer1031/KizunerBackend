<?php

namespace Modules\User\Http\Requests\Api\Update;

use Illuminate\Foundation\Http\FormRequest;

class AuthUpdateRequest extends FormRequest
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
            'password' => "required|min:6|same:password_confirm|different:password_current",
            'password_confirm' => "required|min:6|same:password",
            // 'password_current' => "required"
        ];
    }

    public function messages()
    {
        return [];
    }
}
