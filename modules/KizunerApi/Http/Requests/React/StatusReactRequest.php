<?php

namespace Modules\KizunerApi\Http\Requests\React;

use Illuminate\Foundation\Http\FormRequest;

class StatusReactRequest extends FormRequest
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
            'status_id' => 'required'
        ];
    }
}
