<?php

namespace Modules\Wallet\Domains\Actions;

use Modules\Wallet\Domains\Card;
use Modules\Wallet\Domains\Dto\CardDto;
use Modules\Wallet\Domains\Repositories\Contracts\CardRepositoryInterface;

class CreateCardAction
{

    private $cardDto;

    public function __construct(CardDto $cardDto)
    {
        $this->cardDto = $cardDto;
    }

    public function execute()
    {
        return $this->createNewCard();
    }

    private function createNewCard()
    {
        return Card::create($this->cardDto, $this->isDefault());
    }

    private function isDefault()
    {
        /** @var CardRepositoryInterface $cardRepository */
        $cardRepository = resolve(CardRepositoryInterface::class);
        return $cardRepository->getByWalletId($this->cardDto->walletId)->count() > 0 ? false : true;
    }
}
