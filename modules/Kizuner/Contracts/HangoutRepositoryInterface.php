<?php

namespace Modules\Kizuner\Contracts;

use Modules\Kizuner\Models\Hangout;

interface HangoutRepositoryInterface
{
    public function get(string $id);

    public function create(array $hangoutData);

    public function update(string $id, array $hangoutData);

    public function delete(string $id): bool;

    public function getByUser(string $userId, int $perPage);

    public function isHangoutOwner(string $userId, string $hangoutId);
}
