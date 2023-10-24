<?php

namespace Modules\KizunerApi\Http\Requests\Ratting;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRattingRequest extends FormRequest
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
            'comment'   => 'required',
            'rate'      => 'required|digits_between:1,5'
        ];
    }
}
