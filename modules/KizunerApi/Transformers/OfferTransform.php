<?php

namespace Modules\KizunerApi\Transformers;

use Carbon\Carbon;
use League\Fractal\TransformerAbstract;
use Modules\Kizuner\Models\Offer;
use Modules\Rating\Domains\Entities\RatingEntity;
use Modules\Rating\Domains\Rating;
use Modules\Wallet\Domains\Entities\CryptoWalletEntity;
use Illuminate\Support\Facades\Log;

class OfferTransform extends TransformerAbstract
{

    protected $defaultIncludes = [
        'user'
    ];

    public function transform(Offer $offer)
    {
        $available_status = $offer->hangout ? $offer->hangout->available_status : null;
        $status = array_flip(Offer::$status)[$offer->status];

        $hangout = $offer->hangout;
        $short_address = $hangout ? ($hangout->location ? $hangout->location->short_address : '') : '';

        $crypto_currency = null;
        if ($hangout->crypto_wallet_id) {
            $wallet = CryptoWalletEntity::where('id', $hangout->crypto_wallet_id)->first();
            $crypto_currency = $wallet ? $wallet->currency : null;
        }

        $transform = [
            'id'                => $offer->id,
            'hangout_id'        => $offer->hangout_id,
            'title'             => $offer->hangout_title,
            'kizuna'            => $offer->kizuna,
            'start_time'        => ($available_status == 'no_time' || $available_status == 'combine') ? null : Carbon::create($offer->start),
            'end_time'          => ($available_status == 'no_time' || $available_status == 'combine') ? null : Carbon::create($offer->end),
            'address'           => $offer->address,
            'short_address'     => $short_address,
            'status'            => $status,
            'label'             => key_exists($status, Offer::$label) ? Offer::$label[$status] : $status,
            'created_at'        => $offer->created_at,
            'review'            => RatingEntity::where([
                'offer_id' => $offer->id,
                'user_id'  => auth()->user()->id
            ])->first(),
            'available_status'  => ($offer->hangout) ? $offer->hangout->available_status : null,
            'payment_method' => $offer->payment_method,
            'amount' => $offer->amount,
            'is_range_price'   => $offer->is_range_price,
            'min_amount'       => $offer->min_amount,
            'max_amount'       => $offer->max_amount,
            'payment_status' => $offer->payment_status,
            // 'invoice_url' => $offer->payment_status == Offer::PAYMENT_STATUS_UNPAID ? $offer->invoice_url : null,
            'invoice_url' => $offer->invoice_url,
            'available_payment_method' => $hangout ? $hangout->payment_method : null,
            'crypto_currency' => $crypto_currency,
        ];

        if ($this->isSender($offer)) {
            $transform['show_cancel'] = ($offer->status == Offer::$status['pending']
                || $offer->status == Offer::$status['queuing']) ? true : false;
        }
        return $transform;
    }

    public function includeUser(Offer $offer)
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


    private function isSender(Offer $offer)
    {
        if (app('request')->user()->id == $offer->sender_id) {
            return true;
        }
        return false;
    }
}
