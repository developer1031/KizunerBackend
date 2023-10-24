<?php

namespace Modules\Kizuner\Contracts;

interface ReactRepositoryInterface
{
    public function hangoutReact(string $userId, string $hangoutId);
    public function statusReact(string $userId, string $statusId);
    public function helpReact(string $userId, string $helpId);
}
