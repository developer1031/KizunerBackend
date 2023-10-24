<?php

namespace Modules\KizunerApi\Events;

use Modules\Kizuner\Models\Hangout;
use Illuminate\Queue\SerializesModels;

class HangoutUpdateEvent
{
    use SerializesModels;

    private $object;

    public function __construct(Hangout $hangout)
    {
        $this->object = $hangout;
    }

    public function getObject()
    {
        return $this->object;
    }
}
