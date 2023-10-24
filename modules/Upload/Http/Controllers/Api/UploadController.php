<?php

namespace Modules\Upload\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Modules\Upload\Http\Requests\MultipleFilesUploadRequest;
use Modules\Upload\Http\Requests\SingleFileUploadRequest;
use Modules\Upload\Services\UploadManager;
use Symfony\Component\HttpFoundation\Response;

class UploadController
{
    public function uploadSingleFile(UploadManager $uploadManager, SingleFileUploadRequest $request)
    {
        $file_size = intval($request->file('file')->getSize())/1048576;
        if($file_size > config('kizuner.limit_file_size.others')) {
            return new JsonResponse([
                'errors' => [
                    'message' => 'Could not send file over ' . config('kizuner.limit_file_size.others') . 'MB',
                ]
            ], Response::HTTP_BAD_REQUEST);
        }

        //Log::info($request->file('file'));
        //dd($request->file('file'));

        if ($request->validated()) {
            $response = $uploadManager->uploadSingleFile($request);
            return new JsonResponse($response, Response::HTTP_CREATED);
        }
    }

    public function uploadMultipleFiles(UploadManager $uploadManager, MultipleFilesUploadRequest $request)
    {
        $response = $uploadManager->uploadMultipleFiles($request);
        return new JsonResponse($response, Response::HTTP_CREATED);
    }
}
