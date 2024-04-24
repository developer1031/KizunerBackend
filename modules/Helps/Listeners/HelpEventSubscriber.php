<?php

namespace Modules\Helps\Listeners;

use App\User;
use Modules\Config\Config;
use Modules\Feed\Listeners\AbstractEventSubscriber;
use Modules\Helps\Events\HelpCreatedEvent;
use Modules\Kizuner\Models\LeaderBoard;
use Modules\Notification\Job\Help\HelpTagJob;

class HelpEventSubscriber extends AbstractEventSubscriber
{

  /**
   * Register the listeners for the subscriber.
   *
   * @param  \Illuminate\Events\Dispatcher $events
   */
  public function subscribe($events)
  {
    $events->listen(
      HelpCreatedEvent::class,
      'Modules\Helps\Listeners\HelpEventSubscriber@handleHelpCreated'
    );
  }

  public function handleHelpCreated(HelpCreatedEvent $event)
  {
    $help = $event->getObject();
    $request_help = $event->getRequest();

    /** @var User $user */
    $user = app('request')->user();

    //Add 30kz if First add
    $config_data = new Config();
    $kz = $config_data->getConfigValWithDefault('kizuner_first_add_post');
    addKizuna($user, $kz);

    $this->feedTimelineRepository->create(
      $user->id,
      $help->id,
      'help',
      'new',
      $help->user_id
    );

    //Generate Casts
    generateFakeCast($help, 4);

    //Generate Hangout
    generateFakeHangouts($help, 4, $request_help, $this->feedTimelineRepository);
  }
}
