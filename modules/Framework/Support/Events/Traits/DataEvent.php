<?php


namespace Modules\Framework\Support\Events\Traits;


trait DataEvent
{
    private $data;

    public function getData()
    {
        return $this->data;
    }
}
