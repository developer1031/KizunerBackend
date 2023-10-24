<?php

namespace Modules\Offer\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Modules\Kizuner\Models\Offer;

class OfferUpdateStatusCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'offers:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update Status Offer';

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
        Offer::whereIn('status', [
            Offer::$status['pending'],
            Offer::$status['queuing']
        ])
            ->where('start', '<=', $currentTime)
            ->chunkById(100, function ($items) {
                $items->each(function ($offer) {
                    $offer->status = Offer::$status['reject'];
                    $offer->save();
                });
            });
    }
}
