<?php

namespace Modules\Search\Http\Transformers;

use League\Fractal\TransformerAbstract;
use Modules\Kizuner\Contracts\RelationshipRepositoryInterface;

class UserTransform extends TransformerAbstract
{
    public function transform($item)
    {
        /** @var RelationshipRepositoryInterface $relationRepository */
        $relationRepository = resolve(RelationshipRepositoryInterface::class);
        $friendCheck        = $relationRepository->isFriendShipExist(auth()->user()->id, $item->user_id);

        return [
            'id'        => $item->user_id,
            'name'      => $item->user_name,
            'is_friend' => $friendCheck ? true : false,
            'location'  => [
                'address' => $item->user_location_address,
                'lat'     => $item->user_location_lat,
                'lng'     => $item->user_location_lng
            ],
            'birth_date'  => $item->user_birth_date,
            'gender'      => $item->user_gender,
            'social_avatar' => $item->social_avatar,
            'avatar'    => $item->user_thumb ? \Storage::disk('gcs')->url($item->user_thumb) : null,
            'original'    => [
                'thumb' => $item->user_thumb ? \Storage::disk('gcs')->url($item->user_thumb) : null,
                'path'  => $item->user_path  ? \Storage::disk('gcs')->url($item->user_path) : null
            ],
        ];
    }
}
