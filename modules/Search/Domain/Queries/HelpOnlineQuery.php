<?php

namespace Modules\Search\Domain\Queries;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Modules\Helps\Models\Help;
use Modules\Helps\Models\HelpOffer;

class HelpOnlineQuery
{

    private $perPage;

    public function __construct($perPage)
    {
        $this->perPage  = $perPage;
    }

    public function execute()
    {
                    return DB::table('help_helps as helps')
                    ->select(DB::raw('
                                helps.title as help_title,
                                helps.id as help_id,
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
                                users.social_avatar as social_avatar,
                                user_uploads.path as user_avatar_origin,
                                user_uploads.thumb as user_avatar_thumb,
                                help_uploads.path as help_cover_origin,
                                help_uploads.thumb as help_cover_thumb,
                                helps.is_fake as is_fake,
                                helps.cover_img as cover_img,
                                helps.is_range_price as help_is_range_price,
                                helps.min_amount as help_min_amount,
                                helps.max_amount as help_max_amount,
                                helps.amount as help_amount,
                                "help" as type
                    '))
                    ->join('users', 'users.id', '=', 'helps.user_id')
                    ->leftJoin('uploads as help_uploads', 'help_uploads.uploadable_id', '=', 'helps.id')
                    ->leftJoin('uploads as user_uploads', 'user_uploads.id', '=', 'users.avatar_id')
                    ->whereNull('helps.deleted_at')
                    ->whereNull('helps.room_id')
                    ->where('helps.is_completed', 0)
                    ->where(function($query) {
                        $query->where('helps.start', '>', Carbon::now());
                        $query->orWhereNull('helps.start');
                    })
                    ->groupBy('helps.title')
                    ->whereIn('helps.available_status', ["online", "combine"])
                    ->where('helps.user_id', '<>', auth()->user()->id)
                    ->where('is_completed', 0)
                    ->orderBy('help_created_at' , 'desc')->take(50)->get();
                    //->paginate($this->perPage);


    }
}
