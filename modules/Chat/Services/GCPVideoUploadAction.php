<?php

namespace Modules\Chat\Services;

use FFMpeg\Coordinate\TimeCode;
use FFMpeg\FFMpeg;
use Illuminate\Support\Facades\Log;
use Modules\Upload\Contracts\UploadPath;

class GCPVideoUploadAction
{
    private $videos;

    public function __construct($videos)
    {
        $this->videos = collect($videos);
    }

    public function execute()
    {
        $uploadedVideos = collect([]);

        $this->videos->each(function($item) use ($uploadedVideos) {

            $uploadedVideos->push($this->upload($item));
        });
        return $uploadedVideos;
    }

    private function upload($video)
    {
        $disk = \Storage::disk('gcs');
        $name = time() . '_' . $video->getClientOriginalName();
        $saveOriginal   =  'chats/' . date('Y/m/d') . '/' . $name;
        $saveOriginal = str_replace('.' . $video->extension(), '', $saveOriginal) . '.' . $video->extension();

        /*
        $extension = $video->extension();
        if( in_array($extension, ['qt']) ) {
            $saveOriginal .= '.mp4';
        }
        */

        $remove_extensions = ['.qt', '.mov'];
        $saveOriginal = str_replace($remove_extensions, '.mp4', $saveOriginal);

        $originalRs = file_get_contents($video);
        $disk->put(
            $saveOriginal,
            $originalRs
        );

        //Thumb
        $out_path_thumb = storage_path('app/tmp-video-thumb') . '/' . str_replace('.' . $video->extension(), '', $name) . '.jpg';
        $saveOriginal_thumb = UploadPath::resolve() . '/' . date('Y/m/d') . '/' . str_replace('.' . $video->extension(), '', $name) . '.jpg';

        $ffmpeg = FFMpeg::create(array(
            'ffmpeg.binaries' => '/usr/bin/ffmpeg',
            'ffprobe.binaries' => '/usr/bin/ffprobe'
        ));
        $video = $ffmpeg->open($video);
        $video->frame(TimeCode::fromSeconds(1))->save($out_path_thumb);

        $originalRs_thumb = file_get_contents($out_path_thumb);
        $disk->put(
            $saveOriginal_thumb,
            $originalRs_thumb
        );

        return [
            'original' => $saveOriginal,
            'thumb'    => $saveOriginal_thumb
        ];
    }
}
