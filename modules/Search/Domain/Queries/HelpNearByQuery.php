<?php

namespace Modules\Search\Domain\Queries;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Modules\Helps\Models\Help;

class HelpNearByQuery
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
                                locations.address as help_address,
                                locations.lat as help_lat,
                                locations.lng as help_lng,

                                helps.is_fake as is_fake,
                                helps.cover_img as cover_img,
                                helps.is_range_price as help_is_range_price,
                                helps.min_amount as help_min_amount,
                                helps.max_amount as help_max_amount,
                                helps.amount as help_amount,
                                "help" as type,

                                (6371 * acos (
                                              cos ( radians('  . $this->lat .  ') )
                                              * cos( radians( locations.lat ) )
                                              * cos( radians( locations.lng ) - radians('  . $this->long .  ') )
                                              + sin ( radians(' . $this->lat . ') )
                                              * sin( radians( locations.lat ) )
                                            )
                                ) AS distance
                    '))
                    ->join('locations', 'locations.locationable_id', '=', 'helps.id')
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
                    ->where('helps.user_id', '<>', auth()->user()->id)
                    ->where('is_completed', 0)
                    ->orderBy('distance')->take(50)->get();
                    //->paginate($this->perPage);


    }
}
