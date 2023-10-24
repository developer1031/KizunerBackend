<?php

namespace Modules\Kizuner\Models;

use Modules\Framework\Contracts\AbstractFactory;

class HangoutFactory extends AbstractFactory implements \Modules\Kizuner\Contracts\Data\HangoutInterfaceFactory
{
    protected $concreate = \Modules\Kizuner\Models\Hangout::class;
}
