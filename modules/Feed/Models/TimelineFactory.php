<?php

namespace Modules\Feed\Models;

use Modules\Framework\Contracts\AbstractFactory;

class TimelineFactory extends AbstractFactory implements \Modules\Feed\Contracts\Data\TimelineInterfaceFactory
{
    protected $concreate = \Modules\Feed\Models\Timeline::class;
}
