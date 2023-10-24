<?php

namespace Modules\Wallet\Domains\Dto;

class CardDto
{

    public $name;

    public $walletId;

    public $paymentMethod;

    public $cardBrand;

    public $cardLastFour;

    /**
     * CardDto constructor.
     * @param $walletId
     * @param $paymentMethod
     * @param $cardBrand
     * @param $cardLastFour
     */
    public function __construct($name, $walletId, $paymentMethod, $cardBrand, $cardLastFour)
    {
        $this->name             = $name;
        $this->walletId         = $walletId;
        $this->paymentMethod    = $paymentMethod;
        $this->cardBrand        = $cardBrand;
        $this->cardLastFour     = $cardLastFour;
    }
}
