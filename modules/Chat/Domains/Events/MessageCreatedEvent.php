<?php


namespace Modules\Chat\Domains\Events;

use Carbon\Carbon;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Modules\Chat\Domains\Actions\CreateMessageAction;
use Modules\Chat\Domains\Dto\MessageDto;
use Modules\Chat\Domains\Entities\MemberEntity;
use Modules\Chat\Domains\Entities\MessageEntity;
use Modules\Chat\Domains\Member;
use Modules\Chat\Domains\Queries\MessageQuery;
use Modules\Chat\Domains\Repositories\Contracts\MemberRepositoryInterface;
use Modules\Chat\Domains\Room;
use Modules\Chat\Http\Transformers\MessageTransformer;
use Modules\Chat\Models\ChatIntent;
use Modules\Notification\Job\Chat\NewMessageJob;
use Modules\User\Domains\User;

class MessageCreatedEvent implements ShouldBroadcast
{
    use SerializesModels;

    private $channels;

    public $data;

    public $event = 'chat';

    public function __construct(MessageEntity $message, $sendFakeMsg=true)
    {
        $this->registerChannels($message->room_id);
        $this->sendData($message);

        //Fake member send message
        if($sendFakeMsg)
            $this->fakeMemberSendMsg($message);
    }

    /**
     * @inheritDoc
     */
    public function broadcastOn()
    {
        return $this->channels;
    }

    /**
     * @return string
     */
    public function broadcastAs()
    {
        return 'chat';
    }

    private function registerChannels($roomId)
    {
        $memberRepository = resolve(MemberRepositoryInterface::class);

        $memberRepository->getByRoomId($roomId)->each(function ($item) {

            //$this->channels[] = new PrivateChannel('user.' . $item->user_id);
            if (auth()->user()) {
                if($item->user_id !== auth()->user()->id) {
                    $this->channels[] = new PrivateChannel('user.' . $item->user_id);
                }
            }
            else {
                $this->channels[] = new PrivateChannel('user.' . $item->user_id);
            }
        });
    }

    private function sendData($message)
    {
        $message    = (new MessageQuery($message->id))->execute();
        $this->data = [
            'id'        => $message->message_id,
            'text'      => $message->message_text,
            'image'     => $message->message_image_id ? [
                'id'        => $message->message_image_id,
                'original'  => \Storage::disk('gcs')->url($message->message_image_original),
                'thumb'     => \Storage::disk('gcs')->url($message->message_image_thumb)
            ] : null,
            'hangout'   => $message->hangout_id ? [
                'id'        => $message->hangout_id,
                'type'      => $message->hangout_type,
                'title'     => $message->hangout_title,
                'cover'     => $message->hangout_cover ? \Storage::disk('gcs')->url($message->hangout_cover) : null,
                'user'      => [
                    'id'        => $message->hangout_user_id,
                    'name'      => $message->hangout_user_name,
                    'avatar'    => $message->hangout_user_thumb ? \Storage::disk('gcs')->url($message->hangout_user_thumb) : null
                ],
                'address'     => $message->hangout_address,
                'start'       => Carbon::create($message->hangout_start),
                'end'         => Carbon::create($message->hangout_end),
                'schedule'    => $message->hangout_schedule,
                'can_hangout' => true
            ] : null,
            'user'      => [
                'id'        => $message->user_id,
                'name'      => $message->user_name,
                'avatar'    => $message->user_avatar ? \Storage::disk('gcs')->url($message->user_avatar) : null
            ],
            'created_at'    => $message->message_created_at,
            'room_id'       => $message->message_room_id
        ];

    }

    private function fakeMemberSendMsg($message) {
        $member = Member::findFakeMemberByRoomId($message->room_id, $message->user_id);
        if(!$member) return false;

        $member = User::find($member->user_id);

        if($member && $member->is_fake) {

            //delay 2 seconds before reply
            sleep(2);

            //Filter message to Reply
            $text = 'Hello, there!';
            $chat_intent = ChatIntent::where('intent', 'like', '%'. strtolower($message->text) .'%')->first();
            if($chat_intent) {
                $text = $chat_intent->reply;
            }
            else {
                $text = 'Well, lets me see!';
            }

            $user = $member;
            $messageDto = new MessageDto(
                $user->id,
                $message->room_id,
                $text,
                null,
                null,
                null,
                1
            );

            $chatRoom = Room::find($message->room_id);
            $chatRoom->updated_at = Carbon::now();
            $chatRoom->save();

            $chatMembers = MemberEntity::where([
                'room_id' => $message->room_id,
                'user_id' => $user->id
            ])->first();
            $chatMembers->seen_at = Carbon::now();
            $chatMembers->save();

            $message = (new CreateMessageAction($messageDto, true))->execute();

            //Artisan::call('findRealUser');
        }
    }
}
