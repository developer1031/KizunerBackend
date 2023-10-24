<?php

namespace Modules\Search\Http\Controllers;

use Illuminate\Http\Response;
use Modules\Search\Domain\Actions\HangoutRecommendAction;
use Modules\Search\Http\Transformers\HangoutRecommendTransform;

class HangoutRecommendController
{
    public function index()
    {
        $user       = auth()->user();
        $perPage    = app('request')->input('per_page');

        /*return response()->json(
            fractal(
                (new HangoutRecommendAction())->execute($user->id, $perPage),
                new HangoutRecommendTransform()),
            Response::HTTP_OK
        );
        */

        return response()->json((new HangoutRecommendAction)->execute($user->id, $perPage), Response::HTTP_OK);
    }
}
