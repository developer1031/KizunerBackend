<?php

namespace Modules\Rating\Http\Controllers;

use Illuminate\Http\Response;
use Modules\Framework\Support\Requests\Pagination;
use Modules\Rating\Domains\Queries\UserRatingQuery;

class UserController
{
    public function show($userId = null)
    {
        if (!$userId) {
            $userId = auth()->user()->id;
        }

        $perPage = app('request')->input('per_page');
        $perPage = Pagination::normalize($perPage);

        return response()->json(
            (new UserRatingQuery($userId, $perPage))->execute(),
            Response::HTTP_OK
        );
    }
}
