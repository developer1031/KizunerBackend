<?php

namespace Modules\Notification\Http\Transformers;

use League\Fractal\TransformerAbstract;

class NotificationTransformer extends TransformerAbstract
{
    public function transform($item)
    {
        $image = $item->image == null ? null : \Storage::disk('gcs')->url($item->image);
        $payload = (array)(json_decode($item->payload));
        $payload['image'] = $image;
        return [
            'id'            => $item->id,
            'title'         => $item->title,
            'body'          => $item->body,
            'payload'       => $payload,
            'created_at'    => $item->created_at,
            'status'        => $item->status == 0 ? false : true
        ];
    }
}
