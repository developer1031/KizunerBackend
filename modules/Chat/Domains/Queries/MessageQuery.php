<?php

namespace Modules\Chat\Domains\Queries;

use Illuminate\Support\Facades\DB;

class MessageQuery
{

    private $messageId;


    public function __construct(string $messageId)
    {
        $this->messageId        = $messageId;
    }

    public function execute()
    {
        return $this->queryMessage();
    }

    private function queryMessage()
    {
        return DB::table('chat_messages')
                    ->select(
                        'chat_messages.id as message_id',
                        'chat_messages.room_id as message_room_id',
                        'chat_messages.text as message_text',
                        'chat_messages.hangout as message_hangout_id',
                        'chat_messages.help as message_help_id',
                        'chat_messages.created_at as message_created_at',
                        'chat_messages.related_user as related_user',
                        'chat_message_images.id as message_image_id',
                        'chat_message_images.original as message_image_original',
                        'chat_message_images.thumb as message_image_thumb',
                        'chat_message_images.type as message_type',
                        'users.id as user_id',
                        'users.name as user_name',
                        'users.online as online',
                        'users.is_fake as user_is_fake',
                        'users.fake_avatar as user_fake_avatar',
                        'uploads.thumb as user_avatar',
                        'hangouts.id as hangout_id',
                        'hangouts.type as hangout_type',
                        'hangouts.title as hangout_title',
                        'hu.thumb as hangout_cover',
                        'uh.id as hangout_user_id',
                        'uh.name as hangout_user_name',
                        'uhp.thumb as hangout_user_thumb',
                        'lc.address as hangout_address',
                        'hangouts.kizuna as hangout_kizuna',
                        'hangouts.start as hangout_start',
                        'hangouts.end as hangout_end',
                        'hangouts.schedule as hangout_schedule',
                        'hangouts.is_range_price as hangout_is_range_price',
                        'hangouts.min_amount as hangout_min_amount',
                        'hangouts.max_amount as hangout_max_amount',
                        'hangouts.amount as hangout_amount'
                    )
                    ->join('users', 'users.id', '=', 'chat_messages.user_id')
                    ->leftJoin('hangout_hangouts as hangouts', 'hangouts.id', '=', 'chat_messages.hangout')
                    ->leftJoin('users as uh', 'uh.id', '=', 'hangouts.user_id')
                    ->leftJoin('uploads as uhp', 'uhp.id', '=', 'uh.avatar_id')
                    ->leftJoin('uploads as hu', 'hu.uploadable_id', '=', 'hangouts.id')
                    ->leftJoin('locations as lc', 'lc.locationable_id', '=', 'hangouts.id')
                    ->leftJoin('uploads', 'uploads.id', '=', 'users.avatar_id')
                    ->leftJoin('chat_message_images', 'chat_message_images.message_id', '=', 'chat_messages.id')
                    ->where('chat_messages.id', $this->messageId)
                    ->first();
    }
}
