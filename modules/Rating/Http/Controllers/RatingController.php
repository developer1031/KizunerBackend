<?php

namespace Modules\Rating\Http\Controllers;

use Illuminate\Http\Response;
use Modules\Rating\Domains\Rating;
use Modules\Rating\Http\Requests\RatingStoreRequest;
use Modules\Rating\Http\Requests\RatingUpdateRequest;
use Modules\Rating\Http\Transformers\RatingTransformer;

class RatingController
{

    public function store(RatingStoreRequest $request)
    {
        return response()->json(
            fractal($request->save(), new RatingTransformer()),
            Response::HTTP_CREATED
        );
    }

    public function update(RatingUpdateRequest $request, string $id)
    {
        return response()->json(
            fractal($request->save($id), new RatingTransformer()),
            Response::HTTP_OK
        );
    }

    public function destroy(string $id)
    {
        return response()->json([
            'data' => [
                'status' => Rating::delete($id)
            ]
        ], Response::HTTP_OK);
    }
}
