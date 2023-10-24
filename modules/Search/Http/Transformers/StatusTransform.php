<?php

namespace Modules\Search\Http\Transformers;

use Carbon\Carbon;
use League\Fractal\TransformerAbstract;

class StatusTransform extends TransformerAbstract
{
    public function transform($item)
    {
        return [
            'id'            => $item->status_id,
            'status'        => $item->status_status,
            'cover'         => [
                'thumb'     => $item->status_thumb ? \Storage::disk('gcs')->url($item->status_thumb): null,
                'path'      => $item->status_path ? \Storage::disk('gcs')->url($item->status_path): null,
            ],
            'created_at'    => Carbon::create($item->status_created_at),
            'updated_at'    => Carbon::create($item->status_updated_at),
            'user'          => [
                'id'        => $item->user_id,
                'name'      => $item->user_name,
                'avatar'    => $item->user_avatar ? \Storage::disk('gcs')->url($item->user_avatar): null,
                'social_avatar' => $item->social_avatar,
            ]
        ];
    }
}
