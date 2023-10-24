<?php

namespace Modules\Framework\Contracts;

class AbstractFactory
{
    protected $concreate;

    public function create(array $data = [])
    {
        if (!empty($data)) {
            return new $this->concreate($data);
        }
        return new $this->concreate;
    }
}
