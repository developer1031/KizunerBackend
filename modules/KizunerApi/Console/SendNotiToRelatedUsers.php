<?php

namespace Modules\KizunerApi\Console;

use App\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Modules\Config\Config;
use Modules\Helps\Models\Help;
use Modules\Notification\Notification\Mails\MailTag;

class SendNotiToRelatedUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notificate_to_related_user:remind';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Notificate to related user';

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
        $today = \Carbon\Carbon::now();

        /*
         * Hangouts
         */
        $hangouts = \Modules\Kizuner\Models\Hangout::doesntHave('offers')
            ->where('is_sent_to_users', 0)
            ->orderBy('created_at', 'desc')
            ->whereDate('created_at', $today->format('Y-m-d'))
            ->get();

        foreach ($hangouts as $hangout) {
            $specialities = $hangout->skills->pluck('id')->toArray();
            $skill_users = DB::table('skillables')
                ->select('users.id as user_id', 'users.email as email', 'users.fcm_token', 'users.name')
                ->leftJoin('users', 'skillables.skillable_id', '=', 'users.id')
                ->whereIn('skill_id', $specialities)
                ->where('skillable_type', \App\User::class)
                ->where('is_fake', 0)
                ->groupBy('skillable_id')->get();

            foreach ($skill_users as $skill_user) {
                $user_hangout_related = User::find($skill_user->user_id);
                \Modules\Notification\Job\Hangout\HangoutFitUsersJob::dispatch($hangout, $user_hangout_related);
            }
        }

        /*
         * Helps
         */
        $helps = Help::doesntHave('offers')
            ->where('is_sent_to_users', 0)
            ->orderBy('created_at', 'desc')
            ->whereDate('created_at', $today->format('Y-m-d'))
            ->get();

        foreach ($helps as $help) {
            $specialities = $help->skills->pluck('id')->toArray();
            $skill_users = DB::table('skillables')
                ->select('users.id as user_id', 'users.email as email', 'users.fcm_token', 'users.name')
                ->leftJoin('users', 'skillables.skillable_id', '=', 'users.id')
                ->whereIn('skill_id', $specialities)
                ->where('skillable_type', \App\User::class)
                ->where('is_fake', 0)
                ->groupBy('skillable_id')->get();

            foreach ($skill_users as $skill_user) {
                $user_help_related = User::find($skill_user->user_id);
                \Modules\Notification\Job\Hangout\HelpFitUsersJob::dispatch($help, $user_help_related);
            }
        }
    }
}
