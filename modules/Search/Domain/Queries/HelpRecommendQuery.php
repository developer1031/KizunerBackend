<?php

namespace Modules\Search\Domain\Queries;

use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Modules\Helps\Models\Help;

class HelpRecommendQuery
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
                  helps.id as help_id,
                  helps.title as help_title,
                  helps.description as help_description,
                  helps.start as help_start,
                  helps.end as help_end,
                  helps.budget as help_kizuna,
                  helps.type as help_type,
                  helps.capacity as help_capacity,
                  helps.available as help_available,
                  helps.schedule as help_schedule,
                  helps.created_at as help_created_at,
                  helps.updated_at as help_updated_at,
                  users.id as user_id,
                  users.name as user_name,
                  user_uploads.path as user_avatar_origin,
                  user_uploads.thumb as user_avatar_thumb,
                  help_uploads.path as help_cover_origin,
                  help_uploads.thumb as help_cover_thumb,
                  users.social_avatar as social_avatar,
                  helps.is_fake as is_fake,
                  helps.cover_img as cover_img,
                  helps.is_range_price as is_range_price,
                  helps.min_amount as min_amount,
                  helps.max_amount as max_amount,
                  helps.amount as amount,
                ' . DB::raw('"help" AS type');

        return DB::table('help_helps as helps')
            ->selectRaw($select)
            ->join('users', 'users.id', '=', 'helps.user_id')
            ->join('skillables as sk', 'sk.skillable_id', '=', 'helps.id')
            ->leftJoin('uploads as help_uploads', 'help_uploads.uploadable_id', '=', 'helps.id')
            ->leftJoin('uploads as user_uploads', 'user_uploads.id', '=', 'users.avatar_id')
            ->where('helps.is_completed', 0)
            ->whereIn('sk.skill_id', $userSpecialities)
            ->whereNull('helps.deleted_at')
            ->whereNull('helps.room_id')
            ->where(function($query) {
                $query->where('helps.start', '>', Carbon::now());
                $query->orWhereNull('helps.start');
            })
            ->where('helps.user_id', '<>', auth()->user()->id)
            ->orderBy('helps.updated_at')
            //->groupBy('helps.id')
            ->groupBy('helps.title')
            ->take(50)->get();
            //->paginate($this->perPage);
    }
}
