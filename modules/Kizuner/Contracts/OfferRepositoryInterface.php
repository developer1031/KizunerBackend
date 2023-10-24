<?php

namespace Modules\Kizuner\Contracts;

interface OfferRepositoryInterface
{
    public function create(array $data);

    public function update(string $id, array $data);

    public function getOfferForUser(string $id, $perPage, int $status = null);

    public function getOfferByUser(string $id, $perPage, int $status = null);

    public function isCancelledHangout(string $senderId, string $hangoutId);

    public function isWaiting(string $senderId, string $hangoutId);
}
