<?php

namespace Modules\Upload\Services;

use FFMpeg\Coordinate\TimeCode;
use FFMpeg\FFMpeg;
use Illuminate\Support\Facades\Log;
use Intervention\Image\Facades\Image;
use Modules\Upload\Contracts\UploadPath;
use Modules\Upload\Contracts\UploadRepositoryInterface;
use Modules\Upload\Event\UploadEvent;
use Modules\Upload\Http\Requests\MultipleFilesUploadRequest;
use Modules\Upload\Http\Requests\SingleFileUploadRequest;
use Modules\Upload\Transformers\UploadTransform as MediaTransform;
use Thumbnail;

class UploadManager
{
    private $uploadRepository;

    public function __construct(UploadRepositoryInterface $uploadRepository) {
        $this->uploadRepository = $uploadRepository;
    }

    public function uploadFileResponseUploadObject($request) {
        $mediaPath = $this->uploadFile($request->file('file'));
        $type = $request->get('type');
        $upload = $this->uploadRepository->create([
            'path' => $mediaPath['original'],
            'thumb' => $mediaPath['thumb'],
            'type' => $type
        ]);
        return $upload;
    }

    //public function uploadSingleFile(SingleFileUploadRequest $request)
    public function uploadSingleFile($request)
    {
        $mediaPath = $this->uploadFile($request->file('file'));
        
        \Log::debug($mediaPath);

        $type = $request->get('type');
        $upload = $this->uploadRepository->create([
                                    'path' => $mediaPath['original'],
                                    'thumb' => $mediaPath['thumb'],
                                    'type' => $type
                                ]);

        event(new UploadEvent($upload));
        return fractal($upload, new MediaTransform());
    }

    public function uploadMultipleFiles(MultipleFilesUploadRequest $request)
    {
        $requestFile = collect($request->file('file'));
        $uploadCollection = collect();
        $type = $request->get('type');
        $requestFile->each(function ($file, $key) use ($uploadCollection, $type) {
            $mediaPath = $this->uploadFile($file);
            $uploadCollection->push($this->uploadRepository->create([
                'path' => $mediaPath['original'],
                'thumb' => $mediaPath['thumb'],
                'type' => $type
            ]));
        });

        return fractal($uploadCollection, new MediaTransform());
    }

    private function uploadFile($file)
    {
        if( is_image_file_uploaded($file) )
            return $this->uploadImageFile($file);
        else
            return $this->uploadVideoFile($file);
    }

    private function uploadImageFile($file) {
        $disk = \Storage::disk('gcs');
        $original = Image::make($file)->encode('jpg', 90);
        $fileName = pathinfo($file->hashName(), PATHINFO_FILENAME);
        $saveOriginal = UploadPath::resolve() . '/' . date('Y/m/d') . '/' . $fileName . '.jpg';
        $saveThumb = UploadPath::resolve() . '/' . date('Y/m/d') . '/' .  $fileName . '_thumb.jpg';

        $originalRs = $original->stream();
        $disk->put(
            $saveOriginal,
            $originalRs
        );

        $original->resize(400, null, function ($constraint) {
            $constraint->aspectRatio();
        });
        $thumbRs = $original->stream()->detach();
        $disk->put(
            $saveThumb,
            $thumbRs
        );
        return [
            'original' => $saveOriginal,
            'thumb'    => $saveThumb
        ];
    }

    private function uploadVideoFile($file)
    {
        $disk = \Storage::disk('gcs');
        $name = time() . '_' . $file->getClientOriginalName();
        $saveOriginal = UploadPath::resolve() . '/' . date('Y/m/d') . '/' . $name;
        $saveOriginal = str_replace('.' . $file->extension(), '', $saveOriginal) . '.' . $file->extension();

        $remove_extensions = ['.qt', '.mov'];
        $saveOriginal = str_replace($remove_extensions, '.mp4', $saveOriginal);

        Log::debug("________________1");

        //Force rename to .mp4
        //$saveOriginal = $saveOriginal . '.mp4';

        $originalRs = file_get_contents($file);
        $disk->put(
            $saveOriginal,
            $originalRs
        );

        Log::debug("________________2");

        //generate Thumbnail
        $ffmpeg = FFMpeg::create(array(
            //'ffmpeg.binaries' => '/opt/ffmpeg/ffmpeg',
            //'ffprobe.binaries' => '/opt/ffmpeg/ffprobe'
            'ffmpeg.binaries' => '/usr/bin/ffmpeg',
            'ffprobe.binaries' => '/usr/bin/ffprobe'
        ));
        $video = $ffmpeg->open($file);

        Log::debug("________________3");

        $out_path_thumb = storage_path('app/tmp-video-thumb') . '/' . str_replace('.' . $file->extension(), '', $name) . '.jpg';

        Log::debug("_______" . $out_path_thumb);
        $video->frame(TimeCode::fromSeconds(1))->save($out_path_thumb);


        Log::debug("________________4");

        $saveOriginal_thumb = UploadPath::resolve() . '/' . date('Y/m/d') . '/' . str_replace('.' . $file->extension(), '', $name) . '.jpg';
        $originalRs_thumb = file_get_contents($out_path_thumb);
        $disk->put(
            $saveOriginal_thumb,
            $originalRs_thumb
        );

        Log::debug("________________5");


        return [
            'original' => $saveOriginal,
            'thumb'    => $saveOriginal_thumb
        ];
    }

    private function uploadCustomFile() {}

}
