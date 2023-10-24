<?php

namespace Modules\KizunerApi\Transformers;

use Illuminate\Support\Facades\DB;
use League\Fractal\TransformerAbstract;
use Modules\Kizuner\Models\React;
use Modules\Kizuner\Models\Status;
use Modules\User\Domains\User;

class StatusTransform extends TransformerAbstract
{
    protected $defaultIncludes = [
        'media',
        'user'
    ];

    public function transform(Status $status)
    {
        $commentCount = DB::table('comment_comments')
            ->where('comment_comments.commentable_id', $status->id)
            ->count();

        $statusReactCount = React::where([
            'reactable_id'     => $status->id,
            'reactable_type'   => Status::class,
            'react_type'    => 'like'
        ])->count();

        $likeCheck = React::where([
            'user_id'           => app('request')->user()->id,
            'reactable_id'      => $status->id,
            'reactable_type'    => Status::class
        ])->count();

        $friends = null;
        if($status->friends) {
            $friends_ids = json_decode($status->friends);
            $users_friends = \App\User::whereIn('id', $friends_ids)->get();
            $friends = fractal($users_friends, new UserTransform());
        }

        return [
            'id'            => $status->id,
            'status'        => $status->status,
            'created_at'    => $status->created_at,
            'updated_at'    => $status->updated_at,
            'commentCount'  => $commentCount,
            'liked'         => $likeCheck == 0 ? false : true,
            'like_count'    => $statusReactCount,
            'friends'       => $friends,
            'dymanic_link'  => dynamicUrl('status', $status->id),
        ];
    }

    public function includeMedia(Status $status)
    {
        $media = $status->media;
        if ($media) {
            return $this->collection($media, new MediaTransform());
        }
    }

    public function includeUser(Status $status)
    {
        $user = $status->user;
        if ($user) {
            return $this->item($user, new UserTransform());
        }
    }
}
