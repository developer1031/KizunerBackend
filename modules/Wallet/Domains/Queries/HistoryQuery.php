<?php

namespace Modules\Wallet\Domains\Queries;

use Illuminate\Support\Facades\DB;

class HistoryQuery
{
    private $userId;

    private $fromDate;

    private $toDate;

    private $perPage;

    public function __construct($userId, $fromDate, $toDate, $perPage)
    {
        $this->userId   = $userId;
        $this->fromDate = $fromDate;
        $this->toDate   = $toDate;
        $this->perPage  = $perPage;
    }

    public function execute()
    {
        if ($this->fromDate && $this->toDate) {
            return $this->filterByDate();
        }
        return $this->filterWithoutDate();
    }

    private function filterByDate()
    {
        $fromDate = date($this->fromDate . " 00:00:00");
        $toDate   = date($this->toDate . " 23:59:00");

        return DB::table('wallet_histories')
                ->select(
                    'wallet_histories.id as id',
                    'users.id as user_id',
                    'users.name as user_name',
                    'uploads.thumb as user_avatar',
                    'wallet_histories.created_at as created_at',
                    'wallet_histories.balance_type as balance_type',
                    'wallet_histories.point as point',
                    'wallet_histories.type as type'
                )
                ->join('users', 'users.id', '=', 'wallet_histories.ref_user_id')
                ->leftJoin('uploads', 'uploads.id', '=', 'users.avatar_id')
                ->where('wallet_histories.user_id', $this->userId)
                ->whereBetween('wallet_histories.created_at', [$fromDate, $toDate])
                ->orderBy('wallet_histories.created_at', 'desc')
                ->groupBy('wallet_histories.id')
                ->paginate($this->perPage);
    }

    private function filterWithoutDate()
    {
        return DB::table('wallet_histories')
                ->select(
                    'wallet_histories.id as id',
                    'users.id as user_id',
                    'users.name as user_name',
                    'uploads.thumb as user_avatar',
                    'wallet_histories.created_at as created_at',
                    'wallet_histories.balance_type as balance_type',
                    'wallet_histories.point as point'
                )
                ->join('users', 'users.id', '=', 'wallet_histories.ref_user_id')
                ->leftJoin('uploads', 'uploads.id', '=', 'users.avatar_id')
                ->where('wallet_histories.user_id', $this->userId)
                ->orderBy('wallet_histories.created_at', 'desc')
                ->groupBy('wallet_histories.id')
                ->paginate($this->perPage);
    }
}
