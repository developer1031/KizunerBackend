<?php

namespace Modules\Chat\Domains\Actions;

use Illuminate\Support\Facades\Log;
use Modules\Chat\Domains\Dto\MessageDto;
use Modules\Chat\Domains\Events\MessageCreatedEvent;
use Modules\Chat\Domains\Member;
use Modules\Chat\Domains\Message;
use Modules\Notification\Job\Chat\FindRealUserJob;
use Modules\Notification\Job\Chat\NewMessageJob;
use Modules\User\Domains\User;

class  CreateMessageAction
{
    private $messageDto;
    private $fromCronJob;
    private $relateUser;

    public function __construct(MessageDto $messageDto, $fromCronJob=false, $relateUser=null)
    {
        $this->messageDto = $messageDto;
        $this->fromCronJob = $fromCronJob;
        $this->relateUser = $relateUser;
    }

    public function execute()
    {
        $message =  $this->messageDto->images ?
                    $this->createMessageWithImages():
                    $this->createMessage();

        if(!$this->fromCronJob) {
            event(new MessageCreatedEvent($message));
        }

        $user = User::find($message->user_id);
        $member = Member::findFakeMemberByRoomId($message->room_id, $message->user_id);

        Log::info('Noti busy');
        Log::info($member);

        if($member) {
            $member = User::find($member->user_id);

            if(!$member->is_fake) {
                //if($user && !$user->is_fake)
                NewMessageJob::dispatch($message, $this->relateUser);
            }
        }
        return $message;
    }

    private function createMessageWithImages()
    {
        $message = $this->createMessage();
        (new BatchUpdateImageAction($message->id, $this->messageDto->images))->execute();
        return $message;
    }

    private function createMessage()
    {
        return Message::create($this->messageDto);
    }
}
