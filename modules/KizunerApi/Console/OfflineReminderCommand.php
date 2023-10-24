<?php

namespace Modules\KizunerApi\Console;

use App\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Modules\Config\Config;
use Modules\Notification\Notification\Mails\MailTag;

class OfflineReminderCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'offline_user:remind';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remind user offline after some days';

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
        // $config_data = new Config();
        // $offline_remain_setting =  json_decode($config_data->getConfig('offline_remain'), true);
        // $days_offline_remain = isset($offline_remain_setting['day_num']) ? $offline_remain_setting['day_num'] : 0;

        // $currentTime = Carbon::now();
        // //$yesterday = Carbon::now()->subDays(1);
        // $yesterday = Carbon::now()->subMinutes(5);

        // $users_offline = User::where('email_notification', 1)
        //     //->whereDate('last_send_mail', $yesterday->format('Y-m-d'))
        //     ->whereNull('last_send_mail')
        //     ->whereRaw("DATE_ADD(last_login, INTERVAL ". $days_offline_remain ." MINUTE) <= '" . $currentTime . "'")
        //     ->get();

        // // dump($users_offline);

        // foreach ($users_offline as $user) {
        //     $user->last_send_mail = $currentTime;
        //     $user->save();
        //     $user->notify(new MailTag('offline_remain'));
        // }
    }
}
