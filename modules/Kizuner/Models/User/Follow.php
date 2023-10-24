<?php

namespace Modules\Kizuner\Models\User;

use App\User;
use GoldSpecDigital\LaravelEloquentUUID\Database\Eloquent\Model;
use Modules\Friend\Contracts\Data\FollowInterface;
use Modules\Kizuner\Contracts\Data\RelationInterface;

class Follow extends Model implements FollowInterface, RelationInterface
{

    protected $table = 'friend_follows';

    protected $fillable = [
        'user_id',
        'follow_id'
    ];

    /**
     * Get the user that has the follow.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function setUserId(string $userId): FollowInterface
    {
        $this->user_id = $userId;
        return $this;
    }

    public function getUserId(): string
    {
        return $this->user_id;
    }

    public function setFollowId(string $followId): FollowInterface
    {
        $this->follow_id = $followId;
        return $this;
    }

    public function getFollowId(): string
    {
        return $this->follow_id;
    }
}
