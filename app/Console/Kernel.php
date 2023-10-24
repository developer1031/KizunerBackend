<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Modules\Helps\Console\HelpOfferAutoCompleteCommand;
use Modules\Helps\Console\HelpOfferRemindCommand;
use Modules\Helps\Console\HelpOfferUpdateStatusCommand;
use Modules\Helps\Console\HelpUpdateStatusCommand;
use Modules\KizunerApi\Console\OfflineReminderCommand;
use Modules\KizunerApi\Console\SendNotiToRelatedUsers;
use Modules\Notification\Job\Hangout\HangoutFitUsersJob;
use Modules\Offer\Console\Commands\OfferAutoCompleteCommand;
use Modules\Offer\Console\Commands\OfferRemindCommand;
use Modules\Offer\Console\Commands\OfferUpdateStatusCommand;
use Modules\Upload\Consoles\Commands\CleanUpGcs;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        OfferAutoCompleteCommand::class,
        OfferUpdateStatusCommand::class,
        OfferRemindCommand::class,
        CleanUpGcs::class,

        HelpOfferAutoCompleteCommand::class,
        HelpOfferUpdateStatusCommand::class,
        HelpOfferRemindCommand::class,
        HelpUpdateStatusCommand::class,

        OfflineReminderCommand::class,

        //Send noti to Related users
        //HangoutFitUsersJob::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('telescope:prune')->daily();
        $schedule->command(OfferAutoCompleteCommand::class)
                 ->description('Autocomplete Offer')
                 ->everyMinute()
                 ->withoutOverlapping();

        $schedule->command(OfferUpdateStatusCommand::class)
            ->description('Update Offer Status to Reject')
            ->everyMinute()
            ->withoutOverlapping();

        $schedule->command(OfferRemindCommand::class)
            ->description('Reminder Offer')
            ->everyMinute()
            ->withoutOverlapping();

        $schedule->command('backup:clean')->daily();
        $schedule->command('backup:run')->daily();
        $schedule->command('gcs:prune')->daily();

        //Helps
        $schedule->command(HelpOfferAutoCompleteCommand::class)
            ->description('Autocomplete Offer')
            ->everyMinute()
            ->withoutOverlapping();
        $schedule->command(HelpOfferUpdateStatusCommand::class)
            ->description('Update Help Offer Status to Reject')
            ->everyMinute()
            ->withoutOverlapping();
        $schedule->command(HelpOfferRemindCommand::class)
            ->description('Help Reminder Offer')
            ->everyMinute()
            ->withoutOverlapping();
        $schedule->command(HelpUpdateStatusCommand::class)
            ->description('Update Help Status')
            ->everyMinute()
            ->withoutOverlapping();

        //Offline remain
        $schedule->command(OfflineReminderCommand::class)
            ->description('Offline Reminder')
            ->everyMinute()
            ->withoutOverlapping();

        //Notification for User by each 15 minutes
        /*
        $schedule->command(SendNotiToRelatedUsers::class)
            ->description('Notificate to related user')
            ->everyFifteenMinutes()
            ->withoutOverlapping();
        */
        $schedule->command('findRealUser')->everyThreeMinutes()->withoutOverlapping();

        //Complete/Expired post
        $schedule->command('updateCompletePost')->everyThreeMinutes()->withoutOverlapping();

        $schedule->command('user:generateAge')->daily();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
