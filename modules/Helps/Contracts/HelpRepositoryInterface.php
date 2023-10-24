<?php

namespace Modules\Helps\Contracts;

use Modules\Kizuner\Models\Hangout;

interface HelpRepositoryInterface
{
    public function get(string $id);

    public function create(array $helpData);

    public function update(string $id, array $helpData);

    public function delete(string $id): bool;

    public function getByUser(string $userId, int $perPage);

    public function isHelpOwner(string $userId, string $helpId);
}
