<?php

namespace Modules\Comment\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Support\Facades\DB;

class NewCommentEvent implements ShouldBroadcast
{
    private $channels;

    public $data;

    public $message;

    public $event;

    public function __construct(string $commentId, string $type)
    {
        $objectType = null;
        if ($type == 'hangout') {
            $objectType = 'hangout_hangouts';
        }

        if ($type === 'status') {
            $objectType = 'statuses';
        }

        if ($type === 'help') {
            $objectType = 'help_helps';
        }

        $comment = DB::table('comment_comments')
                       ->select(
                           'users.id as user_id',
                           'users.name as user_name',
                           'uploads.thumb as avatar',
                           'comment_comments.id as comment_id',
                           'comment_comments.body as comment_body',
                           'comment_comments.commentable_id as commentable_id'
                       )
                       ->join('users', 'users.id', '=', 'comment_comments.user_id')
                       ->leftJoin('uploads', 'users.avatar_id', '=', 'uploads.id')
                       ->where('comment_comments.id', $commentId)
                       ->first();


        $object = DB::table($objectType)
            ->where('id', $comment->commentable_id)
            ->first();
        $this->event = 'comment.'. $object->id;

        if ($comment->user_id != $object->user_id) {
            $this->channels[] = new PrivateChannel('user.' . $object->user_id);
        }

        $userIds = DB::table('comment_comments')
                    ->select('user_id')
                    ->where('commentable_id', $object->id)
                    ->where('user_id', '<>', $object->user_id)
                    ->groupBy('user_id')
                    ->get()
                    ->pluck('user_id')
                    ->toArray();

        collect($userIds)->filter(function($item) use ($comment){
            return $item != $comment->user_id;
        })->each(function ($item) {
            $this->channels[] = new PrivateChannel('user.' . $item);
        });

        $this->data = [
            'reference_id'  => $comment->commentable_id,
            'type'          => $type,
            'user'          => [
                'id'        => $comment->user_id,
                'name'      => $comment->user_name,
                'avatar'    => $comment->avatar == null ? $comment->avatar : \Storage::disk('gcs')->url($comment->avatar)
            ],
            'comment' => [
                'id'    => $comment->comment_id,
                'body'  => $comment->comment_body
            ]
        ];
        $this->message = 'You have new Comment';
    }

    /**
     * @inheritDoc
     */
    public function broadcastOn()
    {
        return $this->channels;
    }

    public function broadcastAs()
    {
        return $this->event;
    }
}
