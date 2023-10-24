<?php

namespace Modules\Framework\Contracts;

use Illuminate\Support\Collection;

interface LoaderInterface
{
    /**
     * @return Collection
     */
    public function getCollection();
}
