<?php

namespace Modules\KizunerApi\Http\Requests\Hangout;

use Illuminate\Foundation\Http\FormRequest;

class HangoutCreateRequest extends FormRequest
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
            'title' => 'required',
            'kizuna' => 'integer|min:0',
            'capacity' => 'integer|min:0',
            'payment_method' => 'required|in:credit,crypto,both',
            'amount' => 'required|numeric|min:0',
        ];
    }
}
