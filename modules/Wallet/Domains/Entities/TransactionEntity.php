<?php

namespace Modules\Wallet\Domains\Entities;

use Modules\Framework\Support\DB\UuidEntity;

class TransactionEntity extends UuidEntity
{

    const TYPE_TRANSFER = 'transfer';
    const TYPE_OFFER    = 'offer';
    const TYPE_REFUND_OFFER    = 'refund_offer';
    const TYPE_SHARE_POST    = 'share_post';

    /**
     * @desc Table name in Database
     */
    protected $table = 'wallet_transactions';
}
