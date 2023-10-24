<?php

namespace Modules\Wallet\Domains\Entities;

use Modules\Framework\Support\DB\UuidEntity;
use Modules\Framework\Support\Facades\EntityManager;

class WalletEntity extends UuidEntity
{

    const STRIPE_FEE = 0.1; // 10%
    const NOW_PAYMENTS_FEE = 0.08; // 8%

    const PAYMENT = 'payment';
    const TRANSFER = 'transfer';
    const REFUND = 'refund';

    /**
     * @desc Table Name
     */
    protected $table = 'wallet_wallets';

    public function cards()
    {
        return EntityManager::getRepository(CardEntity::class)
            ->getByWalletId($this->id);
    }
}
