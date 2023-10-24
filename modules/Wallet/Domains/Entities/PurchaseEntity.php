<?php

namespace Modules\Wallet\Domains\Entities;

use Modules\Framework\Support\DB\UuidEntity;

class PurchaseEntity extends UuidEntity
{
    /**
     * @desc Table Name
     */
    protected $table = 'wallet_purchases';
}
