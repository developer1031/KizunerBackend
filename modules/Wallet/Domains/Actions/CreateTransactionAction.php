<?php

namespace Modules\Wallet\Domains\Actions;

use Modules\Wallet\Domains\Dto\HistoryDto;
use Modules\Wallet\Domains\Entities\HistoryEntity;
use Modules\Wallet\Domains\Entities\TransactionEntity;
use Modules\Wallet\Domains\History;
use Modules\Wallet\Domains\Transaction;
use Modules\Wallet\Domains\Wallet;
use Modules\Wallet\Exceptions\NotEnoughPointException;

class CreateTransactionAction
{
    private $userId;

    private $receiverId;

    private $point;

    private $type;

    /**
     * CreateTransactionAction constructor.
     * @param $userId
     * @param $receiverId
     * @param $point
     * @param string $type
     */
    public function __construct($userId, $receiverId, $point, $type = TransactionEntity::TYPE_TRANSFER)
    {
        $this->userId       = $userId;
        $this->receiverId   = $receiverId;
        $this->point        = $point;
        $this->type         = $type;
    }

    public function execute()
    {
        $transaction = $this->transferMoney();
        $this->createSenderHistory($transaction);
        $this->createReceiverHistory($transaction);
    }

    /**
     * @throws NotEnoughPointException
     */
    public function transferMoney()
    {
        $currentWallet  = $this->checkBalance();
        $receiverWallet = Wallet::findByUserId($this->receiverId);
        Wallet::updateBalance($currentWallet->id, -$this->point);
        Wallet::updateBalance($receiverWallet->id, $this->point);
        return Transaction::create($currentWallet->id,
                            $receiverWallet->id,
                            $this->point,
                            $this->type);
    }

    /**
     * @throws NotEnoughPointException
     */
    private function checkBalance()
    {
        $wallet = Wallet::findByUserId($this->userId);

        if ($wallet->available < $this->point) {
            throw new NotEnoughPointException(
                'Your balance is ' . $wallet->balance . ', not enough kizuna.'
            );
        }
        return $wallet;
    }

    private function createSenderHistory($transaction)
    {
        History::create(new HistoryDto(
            $this->userId,
            $this->receiverId,
            $transaction->id,
            HistoryEntity::TYPE_TRANSACTION,
            HistoryEntity::BALANCE_MINUS,
            $transaction->point,
            0
        ));
    }

    private function createReceiverHistory($transaction)
    {
        History::create(new HistoryDto(
            $this->receiverId,
            $this->userId,
            $transaction->id,
            HistoryEntity::TYPE_TRANSACTION,
            HistoryEntity::BALANCE_ADD,
            $transaction->point,
            0
        ));
    }
}
