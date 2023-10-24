<?php

namespace Modules\Feed\Contracts\Data;

interface TimelineInterface
{

    public function getId(): string;

    public function setUserId(string $userId): self;

    public function getUserId(): string;

    public function setReferenceId(string $referenceId): self;

    public function getReferenceId(): string;

    public function setType(string $type): self;

    public function getType(): string;

    public function setStatus(string $status): self;

    public function getStatus(): string;

    public function isStatus(): bool;

    public function isHangout(): bool;

    public function setReferenceUserId(string $referenceUserId): self;

    public function getReferenceUserId(): string;
}
