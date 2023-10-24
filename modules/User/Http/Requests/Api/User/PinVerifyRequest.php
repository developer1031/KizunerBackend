<?php

namespace Modules\User\Http\Requests\Api\User;

use Illuminate\Foundation\Http\FormRequest;

class PinVerifyRequest extends FormRequest
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
            'email'     => 'required',
            'pin'       => 'required'
        ];
    }
}
