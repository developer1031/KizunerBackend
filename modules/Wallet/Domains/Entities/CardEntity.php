<?php

namespace Modules\Wallet\Domains\Entities;

use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Framework\Support\DB\UuidEntity;
use Modules\Wallet\Domains\Repositories\Contracts\CardRepositoryInterface;

class CardEntity extends UuidEntity
{
    use SoftDeletes;

    /**
     * Entity Repository
     * @var string
     */
    public $repository = CardRepositoryInterface::class;

    /**
     * @desc Table Name
     */
    protected $table = 'wallet_cards';
}
