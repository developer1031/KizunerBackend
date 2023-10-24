<?php


namespace Modules\Wallet\Domains\Dto;


class PurchaseDto
{
    public $stripe_intent_id;

    public $wallet_id;

    public $package_id;

    public $card_id;

    public $amount;

    public $point;

    /**
     * PurchaseDto constructor.
     * @param $stripe_intent_id
     * @param $wallet_id
     * @param $package_id
     * @param $card_id
     * @param $amount
     * @param $point
     */
    public function __construct($stripe_intent_id, $wallet_id, $package_id, $card_id, $amount, $point)
    {
        $this->stripe_intent_id     = $stripe_intent_id;
        $this->wallet_id            = $wallet_id;
        $this->package_id           = $package_id;
        $this->card_id              = $card_id;
        $this->amount               = $amount;
        $this->point                = $point;
    }
}
