<?php

namespace Modules\Wallet\Domains\Queries;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Modules\Wallet\Domains\Entities\HistoryEntity;
use Modules\Wallet\Domains\Wallet;

class UserWalletQuery
{
    private $userId;

    public function __construct($userId)
    {
        $this->userId = $userId;
    }

    public function execute()
    {
        $result = [];
        $wallet = Wallet::findByUserId($this->userId);

        $result['balance'] = $wallet->balance;

        $today      = Carbon::today()->format('Y-m-d');
        $startDate  = $today . ' 00:00:00';
        $endDate    = $today . ' 23.59.59';

        $result['today'] = DB::table('wallet_histories as w')
                            ->where('w.user_id', $this->userId)
                            ->whereIn('w.type', ['transaction', 'offer', HistoryEntity::TYPE_SHARE_POST, HistoryEntity::TYPE_FIRST_POST, HistoryEntity::TYPE_LEVEL_UP])
                            ->where('w.balance_type', 'add')
                            ->whereBetween('w.created_at', [$startDate, $endDate])
                            ->sum('w.point');
        return $result;
    }
}
