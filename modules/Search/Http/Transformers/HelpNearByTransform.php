<?php

namespace Modules\Search\Http\Transformers;

use Carbon\Carbon;
use League\Fractal\TransformerAbstract;

class HelpNearByTransform extends TransformerAbstract
{
    public function transform($item)
    {
        $avatar_origin = getUserFakeAvatar($item->user_id);
        return [
            'id'            => $item->help_id,
            'title'         => $item->help_title,
            'description'   => $item->help_description,
            'start'         => Carbon::create($item->help_start),
            'end'           => Carbon::create($item->help_end),
            'kizuna'        => $item->help_kizuna,
            'type'          => $item->help_type,
            'post_type'     => 'help',
            'capacity'      => $item->help_capacity,
            'available'     => $item->help_available,
            'is_range_price'     => $item->help_is_range_price,
            'min_amount'     => $item->help_min_amount,
            'max_amount'     => $item->help_max_amount,
            'amount'     => $item->help_amount,
            'schedule'      => $item->help_schedule ? $item->help_schedule : Carbon::create($item->help_created_at)->diffForHumans(),
            'created_at'    => Carbon::create($item->help_created_at),
            'updated_at'    => Carbon::create($item->help_updated_at),
            'user'          => [
                'id'        => $item->user_id,
                'name'      => $item->user_name,
                'social_avatar' => $item->social_avatar,
                'avatar'    => [
                    'origin'    => $item->user_avatar_origin ? \Storage::disk('gcs')->url($item->user_avatar_origin) : $avatar_origin,
                    'thumb'     => $item->user_avatar_thumb ? \Storage::disk('gcs')->url($item->user_avatar_thumb) : $avatar_origin
                ]
            ],
            'cover' => [
                'origin'    => $item->help_cover_origin ? \Storage::disk('gcs')->url($item->help_cover_origin) : \Storage::disk('gcs')->url($item->cover_img),
                'thumb'     => $item->help_cover_thumb ? \Storage::disk('gcs')->url($item->help_cover_thumb) : \Storage::disk('gcs')->url($item->cover_img)
            ],
            'location'  => [
                'address' => $item->help_address,
                'lat'     => $item->help_lat,
                'lng'     => $item->help_lng
            ],
            'distance'    => $item->distance,
            'dymanic_link'  => dynamicUrl('help', $item->help_id),
        ];
    }
}
