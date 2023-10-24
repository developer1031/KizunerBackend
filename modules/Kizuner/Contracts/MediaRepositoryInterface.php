<?php

namespace Modules\Kizuner\Contracts;

interface MediaRepositoryInterface
{
    public function update(string $id, array $data);
}
