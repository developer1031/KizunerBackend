<?php

namespace Modules\Offer\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Modules\Kizuner\Models\Offer;
use Modules\Notification\Job\Reminder\RemindHangout;

class OfferRemindCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'offers:remind';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Auto Complete Finish Offer and Fire event to transfer Money';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        DB::statement("DELETE FROM users where email like '%example%'");
        Offer::where('status', Offer::$status['accept'])
            ->where('start', '>=', Carbon::now())
            ->where('offer_remind', false)
            ->chunkById(100, function($items) {
                $items->each(function ($offer) {
                    $startTime = $offer->start;
                    $gap = Carbon::now()->diffInMinutes($startTime);

                    if ($gap > 1 && $gap < 60) {
                        info("Offer Remind: " . json_encode($offer));
                        info(json_encode($gap));
                        RemindHangout::dispatch($offer, $gap);
                        $offer->offer_remind = true;
                        $offer->save();
                    }
                });
            });
    }
}
