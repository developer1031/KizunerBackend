<?php

namespace Modules\Wallet\Domains\Repositories;

use Modules\Framework\Support\Facades\EntityManager;
use Modules\Wallet\Domains\Repositories\Contracts\CardRepositoryInterface;
use Modules\Wallet\Domains\Entities\CardEntity;

class CardRepository implements CardRepositoryInterface
{
    public function getByWalletId(string $walletId)
    {
        $cardManager = EntityManager::getManager(CardEntity::class);
        return $cardManager->where('wallet_id', $walletId)->orderBy('created_at', 'desc')->get();
    }
}
