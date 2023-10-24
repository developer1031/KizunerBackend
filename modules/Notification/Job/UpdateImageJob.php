<?php

namespace Modules\Notification\Job;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class UpdateImageJob implements ShouldQueue
{

    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $userId;

    private $newImage;

    public function __construct($userId, $newImageId)
    {
        $this->userId = $userId;
        $this->newImage = $newImageId;
    }

    public function handle()
    {
        DB::statement("UPDATE notification_notifications SET uploadable_id = '" . $this->newImage . "' WHERE user_id = '".$this->userId . "'");
    }
}
