<?php

namespace Modules\Wallet\Domains\Entities;

use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Framework\Support\DB\UuidEntity;
use Modules\Framework\Support\Facades\EntityManager;
use Modules\Wallet\Domains\Dto\HistoryDto;
use Modules\Wallet\Domains\Entities\HistoryEntity;

class CryptoWalletEntity extends UuidEntity
{
    use SoftDeletes;

    protected $table = 'wallet_crypto_wallets';
}
