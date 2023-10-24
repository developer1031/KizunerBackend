<?php

namespace Modules\Rating\Domains\Queries;

use Illuminate\Support\Facades\DB;

class RatingQuery
{

    public $id;

    public function __construct($id)
    {
        $this->id = $id;
    }

    public function execute()
    {
        return DB::table('rating_ratings as r')
                ->select(
                    'r.id as rating_id',
                    'r.rate as rating_rate',
                    'r.comment as rating_comment',
                    'r.created_at as created_at',
                    'r.updated_at as updated_at',
                    'r.ratted_user_id as ratted_user_id',
                    'u.id as user_id',
                    'u.name as user_name',
                    'p.thumb as user_avatar'
                )
                ->join('users as u', 'u.id', '=', 'r.user_id')
                ->leftJoin('uploads as p', 'p.id', '=', 'u.avatar_id')
                ->where('r.id', $this->id)
                ->first();
    }
}
