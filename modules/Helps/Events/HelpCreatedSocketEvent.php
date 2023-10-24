<?php

namespace Modules\Helps\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;
use Modules\Chat\Domains\Repositories\Contracts\MemberRepositoryInterface;
use Modules\Helps\Models\Help;

class HelpCreatedSocketEvent implements ShouldBroadcast
{
    use SerializesModels;

    private $object;
    private $channels;
    public $data;
    public $event = 'reward';

    public function __construct(Help $help)
    {
        $this->object = $help;
        $this->registerChannels();
        $this->sendData();
    }

    /**
     * @inheritDoc
     */
    public function broadcastOn()
    {
        return $this->channels;
        return new Channel('user.' . $user->id);
    }

    /**
     * @return string
     */
    public function broadcastAs()
    {
        return 'reward';
    }

    private function registerChannels()
    {
        $user = auth()->user();
        $this->channels[] = new PrivateChannel('user.' . $user->id);
    }

    private function sendData() {
        $this->data = [
            'text'      => 'Test msg',
        ];
    }
}
