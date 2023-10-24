<?php

namespace Modules\Admin\Http\Requests\ChatPublicGroup;

use Illuminate\Foundation\Http\FormRequest;
use Intervention\Image\Facades\Image;
use Modules\Chat\Domains\Entities\RoomEntity;
use Modules\Chat\Models\ChatIntent;
use Modules\Framework\Support\Facades\EntityManager;
use Modules\Upload\Contracts\UploadPath;

class UpdateRequest extends FormRequest
{
    public function rules()
    {
        return [
            'name' => 'required',
        ];
    }

    public function save($id)
    {
        $file = $this->file('avatar');
        $saveOriginal = null;
        if($file) {
            $disk = \Storage::disk('gcs');
            $original = Image::make($file)->encode('jpg', 90);
            $fileName = pathinfo($file->hashName(), PATHINFO_FILENAME);
            $saveOriginal = UploadPath::resolve() . '/' . date('Y/m/d') . '/' . $fileName . '.jpg';
            $originalRs = $original->stream();
            $disk->put(
                $saveOriginal,
                $originalRs
            );
        }
        $roomManager = EntityManager::getManager(RoomEntity::class);
        $room = $roomManager->find($id);
        $room->name = $this->name;
        $room->avatar = $saveOriginal;
        return $room->save();
    }
}
