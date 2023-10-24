<?php

namespace Modules\Chat\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Chat\Domains\Queries\RoomSearchQuery;
use Modules\Chat\Http\Transformers\RoomTransformer;
use Modules\Framework\Support\Requests\Pagination;

class SearchController
{
    public function index(Request $request)
    {
        $query      = $request->input('query');
        $perPage    = $request->input('per_page');
        $type       = $request->input('type');

        $perPage    = Pagination::normalize($perPage);
        $user       = auth()->user();

        $searchData = (new RoomSearchQuery($user->id, $query, $perPage, $type))->execute();

        return response()->json(
            fractal($searchData, new RoomTransformer()),
            Response::HTTP_OK
        );
    }
}
