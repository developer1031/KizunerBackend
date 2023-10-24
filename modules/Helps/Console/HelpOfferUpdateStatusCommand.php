<?php

namespace Modules\Helps\Console;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Modules\Helps\Models\Help;
use Modules\Helps\Models\HelpOffer;

class HelpOfferUpdateStatusCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'help_offers:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update Status Help Offer';

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
        $currentTime = Carbon::now()->subMinutes(5);
        HelpOffer::whereIn('status', [
            HelpOffer::$status['pending'],
            HelpOffer::$status['queuing']
        ])
            ->where('start', '<=', $currentTime)
            ->chunkById(100, function ($items) {
                $items->each(function ($offer) {
                    $offer->status = HelpOffer::$status['reject'];
                    $offer->save();
                });
            });

        dump('Processing ...');

        Help::whereNotNull('end')->where('end', '<=', $currentTime)
            ->where('available_status', Help::STATUS_ONLINE)
            ->chunkById(100, function ($items) {
                $items->each(function ($help) {
                    $help->available_status = Help::STATUS_NO_TIME;
                    $help->save();

                    dump('Processing ...');
                });
            });
    }
}
