<?php

namespace Modules\Feed\Models;

use Modules\Framework\Contracts\AbstractFactory;

class FeedFollowerFactory extends AbstractFactory implements \Modules\Feed\Contracts\Data\FeedFollowerInterfaceFactory
{
    protected $concreate = \Modules\Feed\Models\FeedFollower::class;
}
