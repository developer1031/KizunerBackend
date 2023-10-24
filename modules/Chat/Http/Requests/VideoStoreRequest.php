<?php

namespace Modules\Chat\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Chat\Domains\Actions\BatchCreateImageAction;
use Modules\Chat\Image\GCPImageUploadAction;
use Modules\Chat\Services\GCPVideoUploadAction;

class VideoStoreRequest extends FormRequest
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
            'videos' => 'required', //50Mb
        ];
    }

    public function save()
    {
        $videos = (new GCPVideoUploadAction($this->file('videos')))->execute();
        return (new BatchCreateImageAction($videos, 'video'))->execute();

    }
}
