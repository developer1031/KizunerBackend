<?php

namespace Modules\Wallet\Http\Controllers;

use Illuminate\Http\Response;
use Modules\Wallet\Domains\Queries\UserWalletQuery;

class WalletController
{
    public function index()
    {
        return response()
            ->json((new UserWalletQuery(auth()->user()->id))->execute(), Response::HTTP_OK);
    }
}
