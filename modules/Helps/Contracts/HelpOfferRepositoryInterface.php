<?php

namespace Modules\Helps\Contracts;

interface HelpOfferRepositoryInterface
{
    public function create(array $data);

    public function update(string $id, array $data);

    public function getOfferForUser(string $id, $perPage, int $status = null);

    public function getOfferByUser(string $id, $perPage, int $status = null);

    public function isCancelledHelp(string $senderId, string $helpId);

    public function isWaiting(string $senderId, string $helpId);
}
