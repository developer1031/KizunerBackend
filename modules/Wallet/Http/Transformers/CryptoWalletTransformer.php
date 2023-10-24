<?php

namespace Modules\Wallet\Http\Transformers;

use League\Fractal\TransformerAbstract;
use Modules\Wallet\Stripe\StripeCustomer;

class CryptoWalletTransformer extends TransformerAbstract
{
    public function transform($item)
    {
        return [
            'id'                =>      $item->id,
            'currency'          =>      $item->currency,
            'wallet_address'    =>      $item->wallet_address,
            'extra_id'          =>      $item->extra_id,
        ];
    }
}
