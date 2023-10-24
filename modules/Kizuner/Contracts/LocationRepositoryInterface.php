<?php

namespace Modules\Kizuner\Contracts;

use Modules\Kizuner\Models\Location;

interface LocationRepositoryInterface
{
    public function create(array $data): Location;
}
