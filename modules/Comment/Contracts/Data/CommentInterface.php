<?php

namespace Modules\Comment\Contracts\Data;

interface CommentInterface
{
    public function getId(): string;

    public function setUserId(string $userId): self;

    public function getUserId(): string;

    public function setBody(string $body): self;

    public function getBody(): string;

    public function getUpdatedAt(): \DateTime;

    public function user();
}
