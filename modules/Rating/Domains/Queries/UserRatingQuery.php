<?php

namespace Modules\Rating\Domains\Queries;

use Illuminate\Support\Facades\DB;
use Modules\Rating\Http\Transformers\RatingTransformer;

class UserRatingQuery
{
    public $userId;

    public $perPage;

    public function __construct($userId, $perPage)
    {
        $this->userId = $userId;
        $this->perPage = $perPage;
    }

    public function execute()
    {
        return $this->getUserRatings();
    }

    private function getUserRatings()
    {
        $rates = DB::table('rating_ratings as r')
                    ->select(
                        'u.id as user_id',
                        'u.name as user_name',
                        'p.thumb as user_avatar',
                        'r.id as rating_id',
                        'r.rate as rating_rate',
                        'r.comment as rating_comment',
                        'r.created_at as created_at',
                        'r.updated_at as updated_at'
                    )
                    ->join('users as u', 'u.id', 'r.user_id')
                    ->leftJoin('uploads as p', 'u.avatar_id', '=', 'p.id')
                    ->where('r.ratted_user_id', $this->userId)
                    ->orderBy('r.created_at', 'desc')
                    ->groupBy('r.offer_id')
                    ->paginate($this->perPage);

        $rateAvg = DB::table('rating_ratings as r')
                    ->select('r.rate as rating_rate')
                    ->where("r.ratted_user_id", $this->userId)
                    ->get();

        return [
            'user'      => $this->getUserInfo(),
            'count'     => $rateAvg->count(),
            'avg'       => $rateAvg->avg(function ($item) {return $item->rating_rate;}),
            'rating'    => fractal($rates, new RatingTransformer())
        ];

    }

    private function getUserInfo()
    {
        $data = DB::table('users as u')
                    ->select(
                        'u.id as id',
                        'u.name as name',
                        'p.thumb as avatar'
                    )
                    ->leftJoin('uploads as p', 'p.uploadable_id', '=', 'u.id')
                    ->where('u.id', $this->userId)
                    ->first();
        $data->avatar = $data->avatar != null ? \Storage::disk('gcs')->url($data->avatar) : null;
        return $data;
    }
}
