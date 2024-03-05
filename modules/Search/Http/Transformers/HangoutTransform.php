<?php

namespace Modules\Search\Http\Transformers;

use Carbon\Carbon;
use League\Fractal\TransformerAbstract;

class HangoutTransform extends TransformerAbstract
{
    public function transform($item)
    {
        if( in_array($item->available_status, ['no_time', 'combine']) ) {
            $hangout_start = null;
            $hangout_end = null;
        }
        else {
            $hangout_start = Carbon::create($item->hangout_start);
            $hangout_end = Carbon::create($item->hangout_end);
        }

        $thumb = null;
        $path = null;
        if($item->is_fake) {
            $thumb = $item->cover_img ? \Storage::disk('gcs')->url($item->cover_img) : null;
            $path = $thumb;
        }
        else {
            $thumb = $item->hangout_cover_thumb ? \Storage::disk('gcs')->url($item->hangout_cover_thumb) : null;
            $path = $item->hangout_cover_path  ? \Storage::disk('gcs')->url($item->hangout_cover_path) : null;
        }

        return [
            'id'            => $item->hangout_id,
            'type'          => $item->hangout_type,
            'title'         => $item->hangout_title,
            'description'   => $item->hangout_description,
            'start'         => $hangout_start,
            'end'           => $hangout_end,
            'capacity'      => $item->hangout_capacity,
            'available'     => $item->hangout_available,
            'is_range_price'     => $item->hangout_is_range_price,
            'min_amount'     => $item->hangout_min_amount,
            'max_amount'     => $item->hangout_max_amount,
            'amount'     => $item->hangout_amount,
            'created_at'    => Carbon::create($item->hangout_created_at),
            'updated_at'    => Carbon::create($item->hangout_updated_at),
            'cover'         => [
                //'thumb'     => $item->hangout_cover_thumb ? \Storage::disk('gcs')->url($item->hangout_cover_thumb) : null,
                //'path'     => $item->hangout_cover_path  ? \Storage::disk('gcs')->url($item->hangout_cover_path) : null
                'thumb'     => $thumb,
                'path'     => $path,
            ],
            'user'          => [
                'id'        => $item->user_id,
                'name'      => $item->user_name,
                'avatar'    => $item->user_avatar ? \Storage::disk('gcs')->url($item->user_avatar) : null,
                'social_avatar' => $item->social_avatar,
            ],
            'payment_method'     => $item->payment_method,
            'available_status'     => $item->available_status,
        ];
    }
}

