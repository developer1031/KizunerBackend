<?php

namespace Modules\Wallet\Domains;

use Modules\Framework\Support\Facades\EntityManager;
use Modules\Wallet\Domains\Entities\WalletEntity;

class Wallet
{

    /** @var WalletEntity  */
    private $wallet;

    /**
     * Wallet constructor.
     * @param WalletEntity $wallet
     */
    public function __construct(WalletEntity $wallet)
    {
        $this->wallet = $wallet;
    }

    /**
     * Find Wallet by UserId
     * @param string $userId
     * @return WalletEntity
     */
    public static function findByUserId(string $userId)
    {
        $walletManager = EntityManager::getManager(WalletEntity::class);
        return $walletManager->where('user_id', $userId)->first();
    }

    /**
     * Find Wallet
     * @param string $id
     * @return WalletEntity
     */
    public static function find(string $id)
    {
        $walletManager = EntityManager::getManager(WalletEntity::class);
        return $walletManager->findOrFail($id);
    }

    /**
     * @param string $walletId
     * @param $amount
     * @return mixed
     */
    public static function updateBalance(string $walletId, $amount)
    {
        $walletManager      = EntityManager::getManager(WalletEntity::class);
        $wallet             = $walletManager->find($walletId);
        /*
        if(!$wallet) {
            $wallet = self::create($walletId, '');
        }
        */
        $wallet->balance    = ((int)$wallet->balance + (int)$amount);
        $wallet->available  = ((int)$wallet->available + (int)$amount);
        $wallet->save();
        return $wallet;
    }

    /**
     * @param string $walletId
     * @param $amount
     * @return mixed
     */
    public static function updateAvailable(string $walletId, $amount)
    {
        $walletManager      = EntityManager::getManager(WalletEntity::class);
        $wallet             = $walletManager->find($walletId);
        $wallet->available  = ((int)$wallet->available + (int)$amount);

        /*
        if($wallet->available > $wallet->balance) {
            $wallet->available = $wallet->balance;
        }
        */

        //Force update Balance = Available
        $wallet->balance = $wallet->available;

        $wallet->save();
        return $wallet;
    }

    /**
     * Create new Wallet
     * @param string $userId
     * @param string $stripeId
     * @return WalletEntity
     */
    public static function create(string $userId, string $stripeId)
    {
        $wallet = EntityManager::create(WalletEntity::class);
        $wallet->user_id    = $userId;
        $wallet->stripe_id  = $stripeId;
        $wallet->balance    = 0;
        $wallet->available  = 0;
        $wallet->save();
        return $wallet;
    }

    /**
     * Find Wallet by stripe connect id
     * @param string $userId
     * @return WalletEntity
     */
    public static function findByStripeConnectId(string $stripeConnectId)
    {
        $walletManager = EntityManager::getManager(WalletEntity::class);
        return $walletManager->where('stripe_connect_id', $stripeConnectId)->first();
    }
}
