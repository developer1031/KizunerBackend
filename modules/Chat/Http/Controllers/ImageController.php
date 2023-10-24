<?php

namespace Modules\Chat\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Modules\Chat\Http\Requests\ImageStoreRequest;

class ImageController
{
    public function store(ImageStoreRequest $request)
    {
        $total_file_size = 0;
        foreach ($request->file('images') as $image) {
            $total_file_size += intval($image->getSize())/1048576;
        }
        if($total_file_size > config('kizuner.limit_file_size.chat')) {
            return new JsonResponse([
                'errors' => [
                    'message' => 'Could not send file over ' . config('kizuner.limit_file_size.chat') . 'MB',
                ]
            ], \Symfony\Component\HttpFoundation\Response::HTTP_BAD_REQUEST);
        }
        return response()
                ->json([
                    'data' => $request->save()
                ], Response::HTTP_CREATED);
    }
}
