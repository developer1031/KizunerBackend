<?php

namespace Modules\Helps\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class HelpCancelRequest extends FormRequest
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
            'is_able_contact' => 'required',
            'status' => 'required'
        ];
    }
}
