<?php

namespace Modules\Friend\Contracts\Data;

interface FollowInterface
{
    public function setUserId(string $userId): self;

    public function getUserId(): string;

    public function setFollowId(string $followId): self;

    public function getFollowId(): string;
}
