<?php

namespace Modules\Kizuner\Repositories;

use Illuminate\Support\Facades\Log;
use Modules\Kizuner\Contracts\OfferRepositoryInterface;
use Modules\Kizuner\Models\Offer;

class OfferRepository implements OfferRepositoryInterface
{
    public function create(array $data)
    {
        $offer = new Offer($data);
        $offer->save();
        return $offer;
    }

    public function update(string $id, array $data)
    {
        $offer = Offer::find($id);
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
            $offerQuery = Offer::where($condition);
        } else {
            $offerQuery = Offer::where('receiver_id', $id);
            
        }

        return $offerQuery->orderBy('updated_at', 'desc')
                            ->orderBy('position')
                            ->paginate($perPage);
    }

    public function getOfferByUser(string $id, $perPage, int $status = null)
    {
        $offerQuery = null;

        if ($status) {

            if ($status == Offer::$status['queuing']) {
                $statuses = [Offer::$status['pending'], Offer::$status['queuing']];

                $offerQuery = Offer::where('sender_id', $id);
                $offerQuery->whereIn('status', $statuses);
            } else {
                $condition = [
                    'sender_id'     => $id,
                    'status'        => $status
                ];
                $offerQuery = Offer::where($condition);
            }
        } else {
            $offerQuery = Offer::where('sender_id', $id);
        }

        return $offerQuery->orderBy('updated_at', 'desc')->paginate($perPage);
    }

    public function isCancelledHangout(string $senderId, string $hangoutId)
    {
        return Offer::where('sender_id', $senderId)
                        ->where('hangout_id', $hangoutId)
                        ->where('status', Offer::$status['cancel'])
                        ->first();
    }

    public function isWaiting(string $senderId, string $hangoutId)
    {
        return Offer::where('sender_id', $senderId)
            ->where('hangout_id', $hangoutId)
            ->whereIn('status', [2, 3])
            ->first();
    }
}
