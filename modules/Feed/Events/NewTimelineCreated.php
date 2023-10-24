<?php

namespace Modules\Feed\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Support\Facades\DB;
use Modules\Feed\Contracts\Data\TimelineInterface;

class NewTimelineCreated implements ShouldBroadcast
{

    private $channels;

    private $followers;

    public $message;

    public function __construct(TimelineInterface $timeline)
    {
        $this->followers = DB::table('feed_followers')
            ->where('channel_id', $timeline->getUserId())
            ->get();

        $this->followers->each(function($item) use ($timeline) {
            if ($item->user_id !== $timeline->getUserId()) {
                $this->channels[] = new PrivateChannel('user.' . $item->user_id);
            }
        });

        $this->message = 'You have new Feed';
    }

    /**
     * @inheritDoc
     */
    public function broadcastOn()
    {
        return $this->channels;
    }

    public function broadcastAs()
    {
        return 'timeline';
    }

    /**
     * Determine if this event should broadcast.
     *
     * @return bool
     */
    public function broadcastWhen()
    {
        return $this->followers->count() > 0;
    }
}
