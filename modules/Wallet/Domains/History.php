<?php

namespace Modules\Wallet\Domains;

use Modules\Framework\Support\Facades\EntityManager;
use Modules\Wallet\Domains\Dto\HistoryDto;
use Modules\Wallet\Domains\Entities\HistoryEntity;

class History
{
    public $history;

    public function __construct(HistoryEntity $history)
    {
        $this->history = $history;
    }

    public static function create(HistoryDto $historyDto)
    {
        $history = EntityManager::create(HistoryEntity::class);
        $history->user_id       = $historyDto->userId;
        $history->ref_user_id   = $historyDto->refUserId;
        $history->ref_id        = $historyDto->refId;
        $history->type          = $historyDto->type;
        $history->balance_type  = $historyDto->balanceType;
        $history->point         = $historyDto->point;
        $history->amount         = $historyDto->amount;
        $history->save();
        return $history;
    }
}
