<?php

namespace Modules\Search\Http\Transformers;

use Carbon\Carbon;
use League\Fractal\TransformerAbstract;

class HangoutRecommendTransform extends TransformerAbstract
{
    public function transform($item)
    {
        $avatar_origin = getUserFakeAvatar($item->user_id);

        return [
            'id'            => $item->hangout_id,
            'title'         => $item->hangout_title,
            'description'   => $item->hangout_description,
            'start'         => Carbon::create($item->hangout_start),
            'end'           => Carbon::create($item->hangout_end),
            'kizuna'        => $item->hangout_kizuna,
            'type'          => $item->hangout_type,
            'capacity'      => $item->hangout_capacity,
            'available'     => $item->hangout_available,
            'is_range_price'     => $item->is_range_price,
            'min_amount'     => $item->min_amount,
            'max_amount'     => $item->max_amount,
            'amount'     => $item->amount,
            'schedule'      => $item->hangout_schedule ? $item->hangout_schedule : Carbon::create($item->hangout_created_at)->diffForHumans(),
            'created_at'    => Carbon::create($item->hangout_created_at),
            'updated_at'    => Carbon::create($item->hangout_updated_at),
            'post_type'          => 'hangout',
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
                'origin'    => $item->hangout_cover_origin ? \Storage::disk('gcs')->url($item->hangout_cover_origin) : \Storage::disk('gcs')->url($item->cover_img),
                'thumb'     => $item->hangout_cover_thumb ? \Storage::disk('gcs')->url($item->hangout_cover_thumb) : \Storage::disk('gcs')->url($item->cover_img)
            ]
        ];
    }
}
