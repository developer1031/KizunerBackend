<?php

namespace Modules\Helps\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class HelpUpdateRequest extends FormRequest
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
            'budget' => 'integer|min:1',
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|in:credit,crypto',
        ];
    }
}
