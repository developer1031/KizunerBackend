<?php

namespace Modules\Chat\Domains\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class UpdateChat implements  ShouldBroadcast
{

    private $channel;

    public $event = 'chat.reload';

    public function __construct($userId)
    {
        $this->channel = new PrivateChannel('user.'.$userId);
    }

    /**
     * @inheritDoc
     */
    public function broadcastOn()
    {
        return $this->channel;
    }

    /**
     * @return string
     */
    public function broadcastAs()
    {
        return $this->event;
    }
}
