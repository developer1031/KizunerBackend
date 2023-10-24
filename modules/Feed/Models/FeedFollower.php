<?php

namespace Modules\Feed\Models;

use GoldSpecDigital\LaravelEloquentUUID\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Feed\Contracts\Data\FeedFollowerInterface;

class FeedFollower extends Model implements FeedFollowerInterface
{

    protected $table = 'feed_followers';

    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'channel_id',
        'status',
        'scope'//default, notification, flat
    ];

    public function setUserId(string $userId): FeedFollowerInterface
    {
        $this->user_id = $userId;
        return $this;
    }

    public function getUserId(): string
    {
        return $this->user_id;
    }

    public function setChannelId(string $channelId): FeedFollowerInterface
    {
        $this->channel_id = $channelId;
        return $this;
    }

    public function getChannelId(): string
    {
        return $this->channel_id;
    }

    public function setStatus(string $status): FeedFollowerInterface
    {
        $this->status = $status;
        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setScope(string $scope): FeedFollowerInterface
    {
        $this->scope = $scope;
        return $this;
    }

    public function getScope(): string
    {
        return $this->scope;
    }

    public function isInactive(): bool
    {
        return $this->status == 'inactive' ? true : false;
    }
}
