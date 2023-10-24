<?php

namespace Modules\Search\Http\Transformers;

use Carbon\Carbon;
use League\Fractal\TransformerAbstract;

class HelpRecommendTransform extends TransformerAbstract
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
            'capacity'      => $item->help_capacity,
            'available'     => $item->help_available,
            'is_range_price'     => $item->is_range_price,
            'min_amount'     => $item->min_amount,
            'max_amount'     => $item->max_amount,
            'amount'     => $item->amount,
            'schedule'      => $item->help_schedule ? $item->help_schedule : Carbon::create($item->help_created_at)->diffForHumans(),
            'created_at'    => Carbon::create($item->help_created_at),
            'updated_at'    => Carbon::create($item->help_updated_at),
            'post_type'     => 'help',
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
            'user_id' => $item->user_id
        ];
    }
}
