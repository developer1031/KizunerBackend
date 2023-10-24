<?php

namespace Modules\Wallet\Domains;

use Modules\Framework\Support\Facades\EntityManager;
use Modules\Wallet\Domains\Dto\CryptoWalletDto;
use Modules\Wallet\Domains\Entities\CryptoWalletEntity;

class CryptoWallet
{
    public $cryptoWallet;

    public function __construct(CryptoWalletEntity $cryptoWallet)
    {
        $this->cryptoWallet = $cryptoWallet;
    }

    public static function create(CryptoWalletDto $cryptoWalletDto)
    {
        $cryptoWallet = EntityManager::create(CryptoWalletEntity::class);
        $cryptoWallet->currency = $cryptoWalletDto->currency;
        $cryptoWallet->wallet_address = $cryptoWalletDto->wallet_address;
        $cryptoWallet->extra_id = $cryptoWalletDto->extra_id;
        $cryptoWallet->wallet_id = $cryptoWalletDto->wallet_id;
        $cryptoWallet->save();
        return $cryptoWallet;
    }

    public static function find(string $id)
    {
        $cryptoWalletManager = EntityManager::getManager(CryptoWalletEntity::class);
        return $cryptoWalletManager->find($id);
    }

    public static function getByWalletId(string $walletId)
    {
        $cryptoWalletManager = EntityManager::getManager(CryptoWalletEntity::class);
        return $cryptoWalletManager->where('wallet_id', $walletId)->orderBy('created_at', 'desc')->get();
    }
}
