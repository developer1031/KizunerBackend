<?php

namespace Modules\KizunerApi\Transformers;

use App\User;
use Illuminate\Support\Facades\Route;
use League\Fractal\TransformerAbstract;
use Modules\Kizuner\Contracts\Data\RelationInterface;
use Modules\Kizuner\Models\User\Block;
use Modules\Kizuner\Models\User\Follow;
use Modules\Kizuner\Models\User\Friend;

class FollowerTransform extends TransformerAbstract
{
    public function transform(RelationInterface $relation)
    {

        // if (Route::current()->parameters('id') != null) {
        //   $currentUser = Route::current()->parameters('id')['id'];
        // } else {
        //   $currentUser = app('request')->user()->id;
        // }

        // if (app('request')->exists('user_id')) {
        //   $currentUser = app('request')->get('user_id');
        // }

        // $type = 'friend_id';

        // if ($relation instanceof Follow) {
        //   $type = 'follow_id';
        // }

        // if ($relation instanceof Block) {
        //   $type = 'block_id';
        // }

        // $relationUserId = ($currentUser != $relation->user_id) ? $relation->user_id : $relation->$type;
        $user = User::find($relation->follow_id);

        if ($user) {
            $media = $user->medias()->where('type', 'user.avatar')->first();

            return [
                'id'            => $relation->id,
                'created_at'    => $relation->created_at,
                'user'          => [
                    'id'        => $user->id,
                    'name'      => $user->name,
                    'avatar'    => $media == null ? null : \Storage::disk('gcs')->url($media->thumb),
                    'social_avatar' => $user->social_avatar,
                ]
            ];
        }
    }
}
