<?php

namespace Modules\Chat\Http\Controllers;

use Illuminate\Http\Response;
use Modules\Chat\Domains\Actions\DeleteRoomAction;
use Modules\Chat\Domains\Queries\RoomsQuery;
use Modules\Chat\Domains\Queries\UserRoomsQuery;
use Modules\Chat\Http\Requests\RoomStoreRequest;
use Modules\Chat\Http\Requests\RoomUpdateRequest;
use Modules\Chat\Http\Transformers\RoomTransformer;
use Modules\Framework\Support\Requests\Pagination;

class RoomController
{

    public function show(string $id)
    {
        return response()
            ->json(fractal(
                ((new RoomsQuery($id))->execute()),
                new RoomTransformer()
            ), Response::HTTP_OK);
    }

    public function index()
    {
        $perPage = app('request')->input('per_page');
        $type = app('request')->input('type');
        $perPage = Pagination::normalize($perPage);
        return response()
                ->json(fractal(
                    ((new UserRoomsQuery(auth()->user()->id, $perPage, $type))->execute()),
                    new RoomTransformer()
                ), Response::HTTP_OK);
    }

    public function store(RoomStoreRequest $request)
    {
        return response()
                ->json(fractal($request->save(), new RoomTransformer()), Response::HTTP_CREATED);
    }

    public function update(string $id, RoomUpdateRequest $request)
    {
        return response()
                ->json($request->save($id), Response::HTTP_OK);
    }

    public function destroy(string $id)
    {
        (new DeleteRoomAction($id))->execute();
        return response()
                    ->json([
                        'data' => [
                            'status' => true
                        ]
                    ], Response::HTTP_OK);
    }
}
