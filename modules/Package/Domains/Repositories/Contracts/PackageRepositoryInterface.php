<?php

namespace Modules\Package\Domains\Repositories\Contracts;

use Illuminate\Support\Collection;

interface PackageRepositoryInterface
{

    /**
     * @return Collection
     */
    public function get(): Collection;
}
