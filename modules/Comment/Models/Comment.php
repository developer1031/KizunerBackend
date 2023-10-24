<?php

namespace Modules\Comment\Models;

use App\User;
use GoldSpecDigital\LaravelEloquentUUID\Database\Eloquent\Model;
use Modules\Comment\Contracts\Data\CommentInterface;

class Comment extends Model implements CommentInterface
{
    protected $table = 'comment_comments';

    protected $fillable = [
        'id',
        'user_id',
        'body',
        'commented_user_id'
    ];

    public function getId(): string
    {
        return $this->id;
    }

    public function setUserId(string $userId): CommentInterface
    {
        $this->user_id = $userId;
        return $this;
    }

    public function getUserId(): string
    {
        return $this->user_id;
    }

    public function setBody(string $body): CommentInterface
    {
        $this->body = $body;
        return $this;
    }

    public function getBody(): string
    {
        return $this->body;
    }

    public function getUpdatedAt(): \DateTime
    {
        return $this->updated_at;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function commentable()
    {
        return $this->morphTo();
    }
}
