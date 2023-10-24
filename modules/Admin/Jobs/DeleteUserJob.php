<?php

namespace Modules\Admin\Jobs;

use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;


class DeleteUserJob implements ShouldQueue
{

    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $userId;

    public function __construct($userId)
    {
        $this->userId = $userId;
    }

    public function handle()
    {
        $currentTime = Carbon::now();
        DB::statement("UPDATE hangout_hangouts SET deleted_at = '".$currentTime."' WHERE user_id = '".$this->userId."'");
        DB::statement("UPDATE statuses SET deleted_at = '".$currentTime."' WHERE user_id = '".$this->userId."'");
        DB::statement("UPDATE feed_timelines SET deleted_at = '".$currentTime."' WHERE reference_user_id = '".$this->userId."'");
    }
}
