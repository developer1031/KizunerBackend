<?php

namespace Modules\Helps\Repositories;

use Modules\Helps\Contracts\HelpOfferRepositoryInterface;
use Modules\Helps\Models\HelpOffer;

class HelpOfferRepository implements HelpOfferRepositoryInterface
{
    public function create(array $data)
    {
        $offer = new HelpOffer($data);
        $offer->save();
        return $offer;
    }

    public function update(string $id, array $data)
    {
        $offer = HelpOffer::find($id);
        $offer->update($data);
        return $offer;
    }

    public function getOfferForUser(string $id, $perPage, int $status = null)
    {
        $offerQuery = null;

        if ($status) {
            $condition = [
                'receiver_id' => $id,
                'status'      => $status
            ];
            $offerQuery = HelpOffer::where($condition);
        } else {
            //$offerQuery = HelpOffer::whereIn('status', [3, 45])->where('receiver_id', $id);
            $offerQuery = HelpOffer::where('receiver_id', $id);
        }
        return $offerQuery->orderBy('updated_at', 'desc')
                            ->orderBy('position')
                            ->paginate($perPage);
    }

    public function getOfferByUser(string $id, $perPage, int $status = null)
    {
        $offerQuery = null;

        if ($status) {

            if ($status == HelpOffer::$status['queuing']) {
                $statuses = [HelpOffer::$status['pending'], HelpOffer::$status['queuing']];

                $offerQuery = HelpOffer::where('sender_id', $id);
                $offerQuery->whereIn('status', $statuses);
            } else {
                $condition = [
                    'sender_id'     => $id,
                    'status'        => $status
                ];
                $offerQuery = HelpOffer::where($condition);
            }

        } else {
            $offerQuery = HelpOffer::where('sender_id', $id);
        }

        return $offerQuery->orderBy('updated_at', 'desc')->paginate($perPage);
    }

    public function isCancelledHelp(string $senderId, string $helpId)
    {
        return HelpOffer::where('sender_id', $senderId)
                        ->where('help_id', $helpId)
                        ->where('status', HelpOffer::$status['cancel'])
                        ->first();
    }

    public function isWaiting(string $senderId, string $helpId)
    {
        return HelpOffer::where('sender_id', $senderId)
            ->where('help_id', $helpId)
            ->whereIn('status', [2, 3])
            ->first();
    }
}
