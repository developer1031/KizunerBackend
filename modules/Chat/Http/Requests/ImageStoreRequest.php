<?php

namespace Modules\Chat\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Chat\Domains\Actions\BatchCreateImageAction;
use Modules\Chat\Image\GCPImageUploadAction;

class ImageStoreRequest extends FormRequest
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
            'images' => 'required'
        ];
    }

    public function save()
    {
        $images = (new GCPImageUploadAction($this->file('images')))->execute();
        return (new BatchCreateImageAction($images))->execute();
    }
}
