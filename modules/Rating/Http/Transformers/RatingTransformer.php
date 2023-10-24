<?php

namespace Modules\Rating\Http\Transformers;

use League\Fractal\TransformerAbstract;

class RatingTransformer extends TransformerAbstract
{
    public function transform($item)
    {
        return [
            'id'        => $item->rating_id,
            'rate'      => $item->rating_rate,
            'comment'   => $item->rating_comment,
            'user'      => [
                'id'        => $item->user_id,
                'name'      => $item->user_name,
                'avatar'    => $item->user_avatar == null ? null : \Storage::disk('gcs')->url($item->user_avatar)
            ],
            'created_at'    => $item->created_at,
            'updated_at'    => $item->updated_at
        ];
    }
}
