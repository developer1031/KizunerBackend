<?php

namespace Modules\Wallet\Domains\Actions;

use Modules\Wallet\Domains\Repositories\Contracts\CardRepositoryInterface;
use Modules\Wallet\Domains\Wallet;
use Modules\Wallet\Stripe\StripeCustomer;

class GetUserCardsAction
{

    private $userId;

    public function __construct(string $userId)
    {
        $this->userId = $userId;
    }

    public function execute()
    {
        $wallet    = $this->getUserWallet();
        $userCards = $this->getUserCards($wallet->id);

        $userCards->each(function ($item) use ($wallet) {
            $item->card = StripeCustomer::addCreditCard($wallet->stripe_id, $item->source);
        });
        return $userCards;
    }

    /**
     * @param string $walletId
     * @return mixed
     */
    public function getUserCards(string $walletId)
    {
        $cardRepository = resolve(CardRepositoryInterface::class);
        return $cardRepository->getByWalletId($walletId);
    }

    /**
     * @return \Modules\Wallet\Domains\Entities\WalletEntity
     */
    private function getUserWallet()
    {
        // Get User Wallet
        return Wallet::findByUserId($this->userId);
    }
}
