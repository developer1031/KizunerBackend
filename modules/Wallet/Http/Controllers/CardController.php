<?php

namespace Modules\Wallet\Http\Controllers;

use Illuminate\Http\Response;
use Modules\Wallet\Domains\Card;
use Modules\Wallet\Domains\Repositories\Contracts\CardRepositoryInterface;
use Modules\Wallet\Domains\Wallet;
use Modules\Wallet\Http\Requests\CardStoreRequest;
use Modules\Wallet\Http\Transformers\CardTransformer;
use Stripe\Exception\ApiErrorException;

class CardController
{

    /**
     * Get Intent client secret for Front End add new Card
     * @return \Illuminate\Http\JsonResponse
     * @throws \Stripe\Exception\ApiErrorException
     */
    public function create()
    {
        $user = auth()->user();
        $intent = \Stripe\SetupIntent::create([
            'customer' => Wallet::findByUserId($user->id)->stripe_id
        ]);

        return response()->json([
            'data' => [
                'client_secret' => $intent->client_secret
            ]
        ], Response::HTTP_OK);
    }

    /**
     * Save new Card
     * @param CardStoreRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CardStoreRequest $request)
    {
        try {
            return response()->json(
                fractal($request->save(), new CardTransformer()),
                Response::HTTP_CREATED
            );
        } catch (\Exception $exception) {
            return response()->json([
                'errors' => [
                    'message' => 'Add credit card error!'
                ]
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @param CardRepositoryInterface $cardRepository
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(CardRepositoryInterface $cardRepository)
    {
        $user  = auth()->user();
        $cards = $cardRepository->getByWalletId(Wallet::findByUserId($user->id)->id);

        return response()->json(
            fractal($cards, new CardTransformer()),
            Response::HTTP_OK
        );
    }

    public function destroy(string $cardId)
    {
        $userCard = Card::find($cardId);
        $paymentMethod = $userCard->payment_method;
        try {
            \Stripe\PaymentMethod::retrieve($paymentMethod)->detach();
            $userCard->delete();
            return response()->json([
                'data' => [
                    'message' => 'Remove Card Successful'
                ]
            ], Response::HTTP_OK);
        } catch (ApiErrorException $exception) {
            return response()->json([
                'errors' => [
                    'message' => 'Remove Card Unsuccessful'
                ]
            ], Response::HTTP_BAD_REQUEST);
        }
    }
}
