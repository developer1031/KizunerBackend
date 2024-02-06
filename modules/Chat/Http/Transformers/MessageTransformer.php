<?php

namespace Modules\Chat\Http\Transformers;

use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use League\Fractal\TransformerAbstract;
use Modules\Helps\Models\Help;
use Modules\Helps\Models\HelpOffer;
use Modules\Kizuner\Models\Offer;

class MessageTransformer extends TransformerAbstract
{
    public function transform($message)
    {
        if ($message->hangout_id) {
            $canHangout = true;
            $isOffered = false;

            if (Offer::where([
                'hangout_id' => $message->hangout_id,
                'sender_id'  => auth()->user()->id
            ])->first()) {
                $isOffered = true;
            }

            if (
                Carbon::now()->gte(Carbon::create($message->hangout_start)) ||
                auth()->user()->id === $message->hangout_user_id ||
                $isOffered
            ) {
                $canHangout = false;
            }
        }

        $video = null;
        $image = null;

        if ($message->message_type == 'image') {
            $image = ($message->message_image_id && $message->message_image_thumb != '') ? [
                'id'        => $message->message_image_id,
                'original'  => \Storage::disk('gcs')->url($message->message_image_original),
                'thumb'     => \Storage::disk('gcs')->url($message->message_image_thumb)
            ] : null;
        }
        if ($message->message_type == 'video') {
            $video = ($message->message_image_id) ? [
                'id'        => $message->message_image_id,
                'original'  => \Storage::disk('gcs')->url($message->message_image_original),
                'thumb'     => \Storage::disk('gcs')->url($message->message_image_thumb)
            ] : null;
        }

        $help_data = null;
        if ($message->message_help_id) {
            $help = Help::find($message->message_help_id);
            $help_canHangout = true;
            if ($help) {
                $avatar = $help->user->medias()->where('type', 'user.avatar')->first();
                $help_offer = HelpOffer::where('sender_id', app('request')->user()->id)
                    ->where('help_id', $help->id)
                    ->where('status', '<>', HelpOffer::$status['cancel'])
                    ->first();
                $help_isOffered = ($help_offer == null) ? false : true;
                if (Carbon::now()->gte(Carbon::create($help->start)) || auth()->user()->id === $message->hangout_user_id || $isOffered) {
                    $help_canHangout = false;
                }

                $cover = $help->media && $help->media->count() > 0 ? $help->media->map(function ($media) use ($help) {
                    return \Storage::disk('gcs')->url($help->media->first()->thumb);
                }) : [];

                $help_data = [
                    'id'        => $help->id,
                    'type'      => $help->type,
                    'title'     => $help->title,
                    'cover'     => $cover,
                    'user'      => [
                        'id'        => $help->user_id,
                        'name'      => $help->user->name,
                        'avatar'    => ($avatar) ? \Storage::disk('gcs')->url($avatar->thumb) : null
                    ],
                    'address'     => $help->location->address,
                    'start'       => Carbon::create($help->start),
                    'end'         => Carbon::create($help->end),
                    'schedule'    => $help->schedule,
                    'kizuna'      => $help->budget,
                    'can_hangout' => $help_canHangout,
                    'is_offered'  => $help_isOffered,
                    'is_range_price'  => $help->is_range_price,
                    'min_amount'  => $help->min_amount,
                    'max_amount'  => $help->max_amount,
                    'amount'  => $help->amount,
                    'room_id'  => $help->room_id,
                ];
            }
        }

        $disk = \Storage::disk('gcs');
        $related_user_obj = (isset($message->related_user) && $message->related_user) ? User::find($message->related_user) : null;
        $related_user = null;
        if ($related_user_obj) {
            $related_user = [
                'id'        => $related_user_obj->id,
                'name'      => $related_user_obj->name,
                'about'     => $related_user_obj->about,
                'phone'     => $related_user_obj->phone,
                'email'     => $related_user_obj->email,
                'social'    => $related_user_obj->social == null ? [] : json_decode($related_user_obj->social),
                'birth_date' => $related_user_obj->birth_date,
                'gender'     => $related_user_obj->gender,
                'email_verified_at' => $related_user_obj->email_verified_at,
                'phone_verified_at' => $related_user_obj->phone_verified_at,
                //                'media'    => [
                //                    'social_avatar' => !$related_user_obj->is_fake ? $related_user_obj->social_avatar : $related_user_obj->fake_avatar,
                //                    'avatar' => [
                //                        'path'  => !$related_user_obj->is_fake ? ($avatar == null ? null : $disk->url($avatar->path)) : $related_user_obj->fake_avatar,
                //                        'thumb' => !$related_user_obj->is_fake ? ($avatar == null ? null : $disk->url($avatar->thumb)) : $related_user_obj->fake_avatar
                //                    ]
                //                ],
            ];
        }


        return [
            'id'        => $message->message_id,
            'text'      => $message->message_text,
            'image'     => ($message->message_type == 'image') ? ($message->message_image_id && $message->message_image_thumb != '') ? [
                'id'        => $message->message_image_id,
                'original'  => \Storage::disk('gcs')->url($message->message_image_original),
                'thumb'     => \Storage::disk('gcs')->url($message->message_image_thumb)
            ] : null : null,
            'video'     => ($message->message_type == 'video') ?  ($message->message_image_id) ? [
                'id'        => $message->message_image_id,
                'original'  => \Storage::disk('gcs')->url($message->message_image_original),
                'thumb'     => \Storage::disk('gcs')->url($message->message_image_thumb)
            ] : null : null,

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
                'kizuna'      => $message->hangout_kizuna,
                'can_hangout' => $canHangout,
                'is_offered'  => $isOffered,
                'is_range_price'  => $message->hangout_is_range_price,
                'min_amount'  => $message->hangout_min_amount,
                'max_amount'  => $message->hangout_max_amount,
                'amount'  => $message->hangout_amount,
                'room_id'  => $message->hangout_room_id,
            ] : null,
            'help'      => $help_data,
            'user'      => [
                'id'        => $message->user_id,
                'name'      => $message->user_name,
                'avatar'    => (isset($message->user_is_fake) && $message->user_is_fake) ? $message->user_fake_avatar : ($message->user_avatar ? \Storage::disk('gcs')->url($message->user_avatar) : null),
                'online'    => $message->online,
                'is_fake'   => $message->user_is_fake
            ],
            'created_at'    => Carbon::create($message->message_created_at),
            'room_id'       => $message->message_room_id,
            'related_user'  => $related_user
        ];
    }
}
