<?php

namespace Modules\Search\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Modules\Framework\Support\Requests\Pagination;
use Modules\Search\Domain\Actions\HangoutNearByAction;
use Modules\Search\Http\Transformers\HangoutNearByTransform;

class HangoutNearByController
{
    public function index()
    {
        $lat        = app('request')->input('lat');
        $lng        = app('request')->input('lng');
        $radius     = app('request')->input('radius');
        $perPage    = app('request')->input('per_page');
        $perPage    = Pagination::normalize($perPage);

        //fix radius
        //$radius = 1000;

        auth()->user()->last_send_mail = null;
        auth()->user()->save();

        return response()->json((new HangoutNearByAction())->execute($lat, $lng, $radius, $perPage), Response::HTTP_OK);

        /*
        return response()
                    ->json(
                        fractal(
                            (new HangoutNearByAction())->execute($lat, $lng, $radius, $perPage),
                            new HangoutNearByTransform()
                        ),
                        Response::HTTP_OK
                    );
        */
    }
}
