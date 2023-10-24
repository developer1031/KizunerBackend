<?php

namespace Modules\Wallet\Http\Controllers;

use Illuminate\Http\Response;
use Modules\Wallet\Exceptions\NotEnoughPointException;
use Modules\Wallet\Http\Requests\TransactionStoreRequest;

class TransactionController
{

    /**
     * Send kizuna to others User Manual
     * @param TransactionStoreRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(TransactionStoreRequest $request)
    {
        try {
            return response()
                        ->json(
                            $request->save(),
                            Response::HTTP_CREATED
                        );
        } catch (NotEnoughPointException $exception) {
            return response()
                    ->json([
                        'message' => $exception->getMessage(),
                        'errors' => [
                            'message' => $exception->getMessage()
                        ]
                    ], Response::HTTP_BAD_REQUEST);
        }
    }
}
