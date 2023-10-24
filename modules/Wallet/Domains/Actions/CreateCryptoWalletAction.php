<?php

namespace Modules\Wallet\Domains\Actions;

use Modules\Wallet\Domains\Card;
use Modules\Wallet\Domains\CryptoWallet;
use Modules\Wallet\Domains\Dto\CardDto;
use Modules\Wallet\Domains\Dto\CryptoWalletDto;
use Modules\Wallet\Domains\Repositories\Contracts\CardRepositoryInterface;

class CreateCryptoWalletAction
{

    private $cryptoWallet;

    public function __construct(CryptoWalletDto $cryptoWallet)
    {
        $this->cryptoWallet = $cryptoWallet;
    }

    public function execute()
    {
        return $this->createNewCryptoWallet();
    }

    private function createNewCryptoWallet()
    {
        return CryptoWallet::create($this->cryptoWallet);
    }
}
