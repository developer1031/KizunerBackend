<?php

namespace Modules\Feed\Contracts\Data;

interface FeedFollowerInterface
{
    public function setUserId(string $userId): self;

    public function getUserId(): string;

    public function setChannelId(string $channelId): self;

    public function getChannelId(): string;

    public function setStatus(string $status): self;

    public function getStatus(): string;

    public function setScope(string $scope): self;

    public function getScope(): string;

    public function isInactive(): bool ;
}
