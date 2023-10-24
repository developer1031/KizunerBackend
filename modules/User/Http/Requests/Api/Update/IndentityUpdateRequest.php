<?php

namespace Modules\User\Http\Requests\Api\Update;

use Illuminate\Foundation\Http\FormRequest;

class IndentityUpdateRequest extends FormRequest
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

        ];
    }

    public function messages()
    {
        return [
            'name'  => 'nullable',
            'username' => 'nullable|unique:users,username',
            'email' => 'nullable|unique:users,email',
            'phone' => 'nullable|unique:users,phone'
        ];
    }
}
