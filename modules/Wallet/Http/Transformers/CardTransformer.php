<?php

namespace Modules\Wallet\Http\Transformers;

use League\Fractal\TransformerAbstract;
use Modules\Wallet\Stripe\StripeCustomer;

class CardTransformer extends TransformerAbstract
{
    public function transform($item)
    {
        return [
            'id'            =>      $item->id,
            'default'       =>      $item->default == 1 ? true : false,
            'name'          =>      $item->name,
            'brand'         =>      $item->card_brand,
            '4digit'        =>      $item->card_last_four
        ];
    }
}
