<?php

namespace Modules\KizunerApi\Transformers;

use App\User;
use League\Fractal\TransformerAbstract;
use Modules\Rating\Domains\Queries\UserRatingQuery;

class SimpleUserTransform extends TransformerAbstract
{

    public function transform(User $user)
    {
        $disk = \Storage::disk('gcs');
        $avatar = $user->medias()->where('type', 'user.avatar')->first();
        $rateInfo = (new UserRatingQuery($user->id, 1))->execute();
        return [
            'id'        => $user->id,
            'name'      => $user->name,
            'media'    => [
                'social_avatar' => $user->social_avatar,
                'avatar' => [
                    'path'  => $avatar == null ? null : $disk->url($avatar->path),
                    'thumb' => $avatar == null ? null : $disk->url($avatar->thumb)
                ]
            ],
            'rating' => [
                'rating' => $rateInfo['avg'],
                'count'  => $rateInfo['count']
            ],
        ];
    }
}
