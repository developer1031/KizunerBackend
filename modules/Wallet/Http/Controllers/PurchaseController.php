<?php

namespace Modules\Wallet\Http\Controllers;

use Illuminate\Http\Response;
use Modules\Wallet\Http\Requests\PurchaseStoreRequest;

class PurchaseController
{

    /**
     * Purchase Kizuna
     * @param PurchaseStoreRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(PurchaseStoreRequest $request)
    {
        return $request->save();
    }
}
