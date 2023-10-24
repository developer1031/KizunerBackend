<?php

namespace Modules\Wallet\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;
use Modules\Package\Domains\Package;
use Modules\Wallet\Domains\Actions\CreateTransactionAction;
use Modules\Wallet\Domains\Card;
use Modules\Wallet\Domains\Dto\HistoryDto;
use Modules\Wallet\Domains\Dto\PurchaseDto;
use Modules\Wallet\Domains\Entities\HistoryEntity;
use Modules\Wallet\Domains\Entities\TransactionEntity;
use Modules\Wallet\Domains\History;
use Modules\Wallet\Domains\Purchase;
use Modules\Wallet\Domains\Wallet;

class PurchaseStoreRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'package_id' => 'required',
            'card_id'    => 'required'
        ];
    }

    public function save()
    {
        $userId     = auth()->user()->id;
        $packageId  = $this->package_id;
        $cardId     = $this->card_id;

        $package    = Package::find($packageId);
        $card       = Card::find($cardId);
        $wallet     = Wallet::findByUserId($userId);

        try {
            $stripePurchase = \Stripe\PaymentIntent::create([
                'amount'            =>      ($package->price)/10,
                'currency'          =>      'usd',
                'customer'          =>      $wallet->stripe_id,
                'payment_method'    =>      $card->payment_method,
                'off_session'       =>      true,
                'confirm'           =>      true,
            ]);
        } catch (\Stripe\Exception\CardException $exception) {
            $payment_intent_id  = $exception->getError()->payment_intent->id;
            $payment_intent     = \Stripe\PaymentIntent::retrieve($payment_intent_id);
            $message = $payment_intent->last_payment_error->message;
            return response()->json([
                'message' => $message,
                'error' => [
                    'message' => $message
                ]
            ], Response::HTTP_BAD_REQUEST);
        }

        $purChaseDto   =  new PurchaseDto(
            $stripePurchase->id,
            $wallet->id,
            $package->id,
            $cardId,
            $package->price,
            $package->point
        );

        $purchase = Purchase::create($purChaseDto);

        Wallet::updateBalance($wallet->id, $package->point);
        History::create(new HistoryDto($userId, $userId, $purchase->id, HistoryEntity::TYPE_PURCHASE, HistoryEntity::BALANCE_ADD, $purchase->point));

        return response()->json([
            'data' => [
                'status' => true
            ]
        ], Response::HTTP_CREATED);
    }
}
