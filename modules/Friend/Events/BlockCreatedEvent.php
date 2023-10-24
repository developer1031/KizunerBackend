<?php

namespace Modules\Friend\Events;

use Modules\Kizuner\Models\User\Block;

class BlockCreatedEvent
{
    private $object;

    public function __construct(Block $block)
    {
        $this->object = $block;
    }

    public function getObject()
    {
        return $this->object;
    }
}
