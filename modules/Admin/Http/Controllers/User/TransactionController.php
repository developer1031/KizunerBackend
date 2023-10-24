<?php

namespace Modules\Admin\Http\Controllers\User;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Modules\Kizuner\Models\Offer;
use Modules\User\Domains\User;
use Modules\Wallet\Domains\Card;
use Modules\Wallet\Domains\Entities\HistoryEntity;
use Yajra\DataTables\Facades\DataTables;

class TransactionController
{

    public function show(Request $request)
    {
        $id = $request->id;
        $transaction = DB::table('wallet_histories')
                        ->where('id', $id)
                        ->first();

        $user   = User::find($transaction->ref_user_id);
        $result =  [
            'type' => $transaction->type,
            'user' => [
                'id' => $user->id,
                'name' => $user->name
            ]
        ];
        if ($transaction->type == HistoryEntity::TYPE_TRANSACTION) {
            $result['data'] =  [
                'point' => $transaction->point
            ];
        } elseif ($transaction->type == HistoryEntity::TYPE_OFFER) {
            $offer = Offer::find($transaction->ref_id);
            $result['data'] = [
                'start' => $offer->start,
                'end'   => $offer->end,
                'point' => $transaction->point
            ];
        } elseif ($transaction->type == HistoryEntity::TYPE_PURCHASE) {
            $purchase = DB::table('wallet_purchases')->where('id', $transaction->ref_id)->first();
            $card = Card::find($purchase->card_id);
            $cardInfo = strtoupper($card->card_brand) . " " . $card->card_last_four;
            $result['data'] =  [
                'amount' => $transaction->amount,
                'point'  => $transaction->point,
                'card'   => $cardInfo
            ];
        }
        return response()->json($result, Response::HTTP_OK);
    }

    public function data(string $id)
    {
        $historyQuery = DB::table('users')
            ->select(
                'wallet_histories.id as id',
                'users.id as user_id',
                'users.name as user_name',
                'uploads.thumb as user_avatar',
                'wallet_histories.created_at as created_at',
                'wallet_histories.balance_type as balance_type',
                'wallet_histories.point as point',
                'wallet_histories.type as type'
            )
            ->join('wallet_histories', 'users.id', '=', 'wallet_histories.ref_user_id')
            ->leftJoin('uploads', 'uploads.id', '=', 'users.avatar_id')
            ->where('wallet_histories.user_id', $id)
            ->orderBy('wallet_histories.created_at', 'desc')
            ->groupBy('wallet_histories.id');

        return DataTables::of($historyQuery)->make(true);
    }
}
