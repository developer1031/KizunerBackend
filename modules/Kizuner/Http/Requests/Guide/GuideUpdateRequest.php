<?php

namespace Modules\Kizuner\Http\Requests\Guide;

use Illuminate\Foundation\Http\FormRequest;

class GuideUpdateRequest extends FormRequest
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
            'url'   => 'required',
            'cover' => 'required',
            'position' => 'required',
            'text' => 'required',
        ];
    }
}
