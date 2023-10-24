<?php

namespace Modules\React\Http\Controllers;

use Modules\Framework\Support\Requests\Pagination;
use Modules\Kizuner\Models\React;

class ReactController
{
    public function show(string $id)
    {
        $perPage = app('request')->input('per_page');

        $perPage = Pagination::normalize($perPage);

        $result = React::select(
                    'users.id as id',
                    'users.name as name',
                    'uploads.thumb as avatar'
                    )->join('users', 'users.id', '=', 'reacts.user_id')
                    ->leftJoin('uploads', 'uploads.id', '=', 'users.avatar_id')
                    ->where('reacts.reactable_id', $id)
                    ->groupBy('reacts.user_id')
                    ->paginate($perPage);
        return fractal($result, new ReactTransform());
    }
}
