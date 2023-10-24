<?php

namespace Modules\Search\Domain\Queries;

use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class HangoutRecommendQuery
{

    private $userId;


    private $perPage;


    public function __construct($userId, $perPage)
    {
        $this->userId   = $userId;
        $this->perPage  = $perPage;
    }

    public function execute()
    {
        $userSpecialities = auth()->user()->skills->pluck('id')->toArray();

        $select = '
                  hangouts.id as hangout_id,
                  hangouts.title as hangout_title,
                  hangouts.description as hangout_description,
                  hangouts.start as hangout_start,
                  hangouts.end as hangout_end,
                  hangouts.kizuna as hangout_kizuna,
                  hangouts.type as hangout_type,
                  hangouts.capacity as hangout_capacity,
                  hangouts.available as hangout_available,
                  hangouts.schedule as hangout_schedule,
                  hangouts.created_at as hangout_created_at,
                  hangouts.updated_at as hangout_updated_at,
                  users.id as user_id,
                  users.name as user_name,
                  user_uploads.path as user_avatar_origin,
                  user_uploads.thumb as user_avatar_thumb,
                  hangout_uploads.path as hangout_cover_origin,
                  hangout_uploads.thumb as hangout_cover_thumb,
                  users.social_avatar as social_avatar,
                  hangouts.is_fake as is_fake,
                  hangouts.cover_img as cover_img,
                  hangouts.is_range_price as is_range_price,
                  hangouts.min_amount as min_amount,
                  hangouts.max_amount as max_amount,
                  hangouts.amount as amount,
                ' . DB::raw('"hangout" AS type');

        return DB::table('hangout_hangouts as hangouts')
            ->selectRaw($select)
            ->join('users', 'users.id', '=', 'hangouts.user_id')
            ->join('skillables as sk', 'sk.skillable_id', '=', 'hangouts.id')
            ->leftJoin('uploads as hangout_uploads', 'hangout_uploads.uploadable_id', '=', 'hangouts.id')
            ->leftJoin('uploads as user_uploads', 'user_uploads.id', '=', 'users.avatar_id')
            ->where('hangouts.is_completed', 0)
            ->whereIn('sk.skill_id', $userSpecialities)
            ->whereNull('hangouts.deleted_at')
            ->whereNull('hangouts.room_id')
            ->where(function($query) {
                $query->where('hangouts.start', '>', Carbon::now());
                $query->orWhereNull('hangouts.start');
            })
            ->where('hangouts.user_id', '<>', auth()->user()->id)
            ->orderBy('hangouts.updated_at')
            //->groupBy('hangouts.id')
            ->groupBy('hangouts.title')
            ->take(50)->get();
            //->paginate($this->perPage);
    }
}
