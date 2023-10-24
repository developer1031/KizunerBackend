<?php

namespace Modules\Wallet\Domains\Repositories\Contracts;

interface CardRepositoryInterface
{
    public function getByWalletId(string $walletId);
}
