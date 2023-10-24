<?php

namespace Modules\Wallet\Domains;

use Modules\Framework\Support\Facades\EntityManager;
use Modules\Wallet\Domains\Dto\PurchaseDto;
use Modules\Wallet\Domains\Entities\PurchaseEntity;

class Purchase
{
    public $purchase;

    public function __construct(PurchaseEntity $entity)
    {
        $this->purchase = $entity;
    }

    public static function create(PurchaseDto $purchaseDto)
    {
        $purchase = EntityManager::create(PurchaseEntity::class);
        $purchase->stripe_intent_id = $purchaseDto->stripe_intent_id;
        $purchase->wallet_id        = $purchaseDto->wallet_id;
        $purchase->package_id       = $purchaseDto->package_id;
        $purchase->card_id          = $purchaseDto->card_id;
        $purchase->amount           = $purchaseDto->amount;
        $purchase->point            = $purchaseDto->point;
        $purchase->save();
        return $purchase;
    }
}
