<?php
namespace Modules\Wallet\Domains\Entities;

use Modules\Framework\Support\DB\UuidEntity;

class HistoryEntity extends UuidEntity
{
    const BALANCE_ADD         = 'add';
    const BALANCE_MINUS       = 'minus';

    const TYPE_TRANSACTION    = 'transaction';
    const TYPE_PURCHASE       = 'purchase';
    const TYPE_OFFER          = 'offer';
    const TYPE_REFUND_OFFER   = 'refund_offer';

    const TYPE_ADVANCE_HELP   = 'advance_help';
    const TYPE_ADVANCE        = 'advance';
    const TYPE_REFUND_ADVANCE   = 'refund_advance';
    const TYPE_ADVANCE_COMPLETE_OFFER  = 'advance_complete_offer';
    const TYPE_FIRST_POST     = 'first_add_post';
    const TYPE_SHARE_POST     = 'first_share_post';
    const TYPE_LEVEL_UP       = 'level_up';

    protected $table          = 'wallet_histories';
}
