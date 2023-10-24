<?php

namespace Modules\Wallet\Domains\Dto;

class HistoryDto
{
    public $userId;
    public $refUserId;
    public $refId;
    public $type;
    public $balanceType;
    public $point;
    public $amount;

    /**
     * HistoryDto constructor.
     * @param $userId
     * @param $refUserId
     * @param $refId
     * @param $type
     * @param $balanceType
     * @param $point
     */
    public function __construct($userId, $refUserId, $refId, $type, $balanceType, $point, $amount)
    {
        $this->userId = $userId;
        $this->refUserId = $refUserId;
        $this->refId = $refId;
        $this->type = $type;
        $this->balanceType = $balanceType;
        $this->point = $point;
        $this->amount = $amount;
    }
}
