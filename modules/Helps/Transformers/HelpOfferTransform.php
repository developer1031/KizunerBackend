<?php

namespace Modules\Helps\Transformers;

use Carbon\Carbon;
use League\Fractal\TransformerAbstract;
use Modules\Helps\Models\HelpOffer;
use Modules\KizunerApi\Transformers\SimpleUserTransform;
use Modules\Rating\Domains\Entities\RatingEntity;
use Modules\Rating\Domains\Rating;

class HelpOfferTransform extends TransformerAbstract
{
    protected $defaultIncludes = [
        'user'
    ];

    public function transform(HelpOffer $offer)
    {
        $available_status = $offer->help ? $offer->help->available_status : null;
        $status = array_flip(HelpOffer::$status)[$offer->status];

        $help = $offer->help;
        $short_address = $help ? ($help->location ? $help->location->short_address : '') : '';

        $transform = [
            'id'                => $offer->id,
            'help_id'           => $offer->help_id,
            'title'             => $offer->help_title,
            'kizuna'            => $offer->kizuna,
            'amount'            => $offer->amount,
            'start_time'        => ($available_status == 'no_time' || $available_status == 'combine') ? null : Carbon::create($offer->start),
            'end_time'          => ($available_status == 'no_time' || $available_status == 'combine') ? null : Carbon::create($offer->end),
            'address'           => $offer->address,
            'short_address'     => $short_address,
            'status'            => $status,
            'label'             => key_exists($status, HelpOffer::$label) ? HelpOffer::$label[$status] : $status,
            'created_at'        => $offer->created_at,
            'review'            => RatingEntity::where([
                'offer_id' => $offer->id,
                'user_id'  => auth()->user()->id
            ])->first(),
            'available_status'  => $available_status,
            'payment_method' => $help ? $help->payment_method : '',
            'amount' => $help ? $help->amount : 0,
            'payment_status' => $offer->payment_status,
            'invoice_url' => $offer->payment_status == HelpOffer::PAYMENT_STATUS_UNPAID ? $offer->invoice_url : null,
        ];

        if ($this->isSender($offer)) {
            $transform['show_cancel'] = ($offer->status == HelpOffer::$status['pending']
                || $offer->status == HelpOffer::$status['queuing']) ? true : false;
        }
        return $transform;
    }

    public function includeUser(HelpOffer $offer)
    {
        if ($this->isSender($offer)) {
            $user = $offer->receiver;
        } else {
            $user = $offer->sender;
        }

        if ($user) {
            return $this->item($user, new SimpleUserTransform());
        }
    }


    private function isSender(HelpOffer $offer)
    {
        if (app('request')->user()->id == $offer->sender_id) {
            return true;
        }
        return false;
    }
}
