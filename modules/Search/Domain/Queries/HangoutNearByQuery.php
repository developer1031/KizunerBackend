<?php

namespace Modules\Search\Domain\Queries;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class HangoutNearByQuery
{

    private $lat;
    private $long;
    private $radius;
    private $perPage;

    public function __construct($lat, $long, $radius, $perPage)
    {
        $this->lat      = $lat;
        $this->long     = $long;
        $this->radius   = $radius;
        $this->perPage  = $perPage;
    }

    public function execute()
    {
        return DB::table('hangout_hangouts as hangouts')
                    ->select(DB::raw('
                                hangouts.title as hangout_title,
                                hangouts.id as hangout_id,
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
                                users.social_avatar as social_avatar,
                                user_uploads.path as user_avatar_origin,
                                user_uploads.thumb as user_avatar_thumb,
                                hangout_uploads.path as hangout_cover_origin,
                                hangout_uploads.thumb as hangout_cover_thumb,
                                locations.address as hangout_address,
                                locations.lat as hangout_lat,
                                locations.lng as hangout_lng,
                                hangouts.is_fake as is_fake,
                                hangouts.cover_img as cover_img,
                                hangouts.is_range_price as hangout_is_range_price,
                                hangouts.min_amount as hangout_min_amount,
                                hangouts.max_amount as hangout_max_amount,
                                hangouts.amount as hangout_amount,
                                "hangout" as type,

                                (6371 * acos (
                                              cos ( radians('  . $this->lat .  ') )
                                              * cos( radians( locations.lat ) )
                                              * cos( radians( locations.lng ) - radians('  . $this->long .  ') )
                                              + sin ( radians(' . $this->lat . ') )
                                              * sin( radians( locations.lat ) )
                                            )
                                ) AS distance
                    '))
                    ->join('locations', 'locations.locationable_id', '=', 'hangouts.id')
                    ->join('users', 'users.id', '=', 'hangouts.user_id')
                    ->leftJoin('uploads as hangout_uploads', 'hangout_uploads.uploadable_id', '=', 'hangouts.id')
                    ->leftJoin('uploads as user_uploads', 'user_uploads.id', '=', 'users.avatar_id')
                    ->whereNull('hangouts.deleted_at')
                    ->whereNull('hangouts.room_id')
                    ->where('hangouts.is_completed', 0)
                    //->groupBy('hangouts.id')
                    ->groupBy('hangouts.title')
                    ->whereRaw('
                            (6371 * acos (
                                              cos ( radians('  . $this->lat .  ') )
                                              * cos( radians( locations.lat ) )
                                              * cos( radians( locations.lng ) - radians('  . $this->long .  ') )
                                              + sin ( radians(' . $this->lat . ') )
                                              * sin( radians( locations.lat ) )
                                            )
                            ) < ' .$this->radius. '

                    ')
                    ->where('hangouts.user_id', '<>', auth()->user()->id)
                    ->orderBy('distance')->take(50)->get();
                    //->paginate($this->perPage);
    }
}
