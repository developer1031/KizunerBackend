<?php

namespace Modules\KizunerApi\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Modules\Chat\Domains\Repositories\Contracts\MemberRepositoryInterface;
use Modules\Helps\Models\Help;

class AddedPointSocketEvent implements ShouldBroadcast
{
    use SerializesModels;

    private $object;
    private $channels;
    public $data;
    public $event = 'added_point';

    public function __construct($is_up=false, $user=null)
    {
        $this->registerChannels($user);
        $this->sendData($is_up);
    }

    /**
     * @inheritDoc
     */
    public function broadcastOn()
    {
        return $this->channels;
    }

    /**
     * @return string
     */
    public function broadcastAs()
    {
        return 'added_point';
    }

    private function registerChannels($user)
    {
        //$user = auth()->user();
        $user = $user ? $user : auth()->user();

        Log::info('-----AddedPointSocketEvent ---- ');
        Log::info('-----$this->user ---- ');
        Log::info($user);

        $this->channels[] = new PrivateChannel('user.' . $user->id);
    }

    private function sendData($is_up) {
        //$is_up = true;
        $this->data = [
            'is_up'      => $is_up,
        ];
    }
}
