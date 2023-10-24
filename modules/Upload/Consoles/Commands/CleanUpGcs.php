<?php

namespace Modules\Upload\Consoles\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CleanUpGcs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gcs:prune';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cleanup Google Storage';

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
        DB::table('upload_delete_queues')->chunkById(100, function($items) {
           $items->each(function($item) {
                \Storage::disk('gcs')->delete($item->path);
                DB::statement("delete from upload_delete_queues where id = '" . $item->id ."'");
           });
        });
    }
}
