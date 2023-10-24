<?php

namespace Modules\Wallet\Domains;

use Modules\Framework\Support\Facades\EntityManager;
use Modules\Notification\Job\TransferJob;
use Modules\Wallet\Domains\Entities\TransactionEntity;

class Transaction
{

    /** @var TransactionEntity  */
    private $transaction;

    /**
     * Transaction constructor.
     * @param TransactionEntity $transaction
     */
    public function __construct(TransactionEntity $transaction)
    {
        $this->transaction = $transaction;
    }

    /**
     * @param $sender
     * @param $receiver
     * @param $point
     * @param $type
     * @param $amount
     * @return mixed
     */
    public static function create($sender, $receiver, $point, $type, $amount)
    {
        $transaction = EntityManager::create(TransactionEntity::class);
        $transaction->sender        = $sender;
        $transaction->receiver      = $receiver;
        $transaction->point         = $point;
        $transaction->type          = $type;
        $transaction->amount        = $amount;
        $transaction->save();

        //Send noti
        TransferJob::dispatch($transaction);

        return $transaction;
    }
}
