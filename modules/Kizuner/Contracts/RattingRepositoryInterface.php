<?php

namespace Modules\Kizuner\Contracts;

interface RattingRepositoryInterface
{
    public function create(array $data);

    public function update(string $id, array $data);

    public function delete(string $id);

    public function isRatted(string $userId, string $targetUser);
}
