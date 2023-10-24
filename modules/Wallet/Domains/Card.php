<?php

namespace Modules\Wallet\Domains;

use Modules\Framework\Support\Facades\EntityManager;
use Modules\Wallet\Domains\Dto\CardDto;
use Modules\Wallet\Domains\Entities\CardEntity;

class Card
{

    /** @var CardEntity  */
    private $card;

    /**
     * Card constructor.
     * @param CardEntity $card
     */
    public function __construct(CardEntity $card)
    {
        $this->card = $card;
    }

    /**
     * @param CardDto $cardDto
     * @param bool $default
     * @return mixed
     */
    public static function create(
        CardDto $cardDto,
        bool $default = false
    ) {
        $cardCheck = self::findByWalletIdAndPayment($cardDto->walletId, $cardDto->paymentMethod);
        if ($cardCheck) {
            return $cardCheck;
        }
        $card = EntityManager::create(CardEntity::class);
        $card->name                 = $cardDto->name;
        $card->wallet_id            = $cardDto->walletId;
        $card->payment_method       = $cardDto->paymentMethod;
        $card->card_brand           = $cardDto->cardBrand;
        $card->card_last_four       = $cardDto->cardLastFour;
        $card->default              = $default;
        $card->save();
        return $card;
    }

    /**
     * @param string $walletId
     * @param string $payment
     * @return mixed
     */
    public static function findByWalletIdAndPayment(string $walletId, string $payment)
    {
        $cardManager = EntityManager::getManager(CardEntity::class);
        return $cardManager->where([
                    'wallet_id'         => $walletId,
                    'payment_method'    => $payment
                ])->first();
    }

    /**
     * @param string $id
     * @return mixed
     */
    public static function find(string $id)
    {
        $cardManager = EntityManager::getManager(CardEntity::class);
        return $cardManager->find($id);
    }
}
