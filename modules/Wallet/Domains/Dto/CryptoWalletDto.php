<?php

namespace Modules\Wallet\Domains\Dto;

class CryptoWalletDto
{

    public $currency;

    public $wallet_address;

    public $extra_id;

    public $wallet_id;

    /**
     * CryptoWalletDto constructor.
     * @param $currency
     * @param $walletAddress
     * @param $extra_id
     * @param $walletId
     */
    public function __construct($currency, $walletAddress, $extra_id, $walletId)
    {
        $this->currency = $currency;
        $this->wallet_address = $walletAddress;
        $this->extra_id = $extra_id;
        $this->wallet_id = $walletId;
    }
}
