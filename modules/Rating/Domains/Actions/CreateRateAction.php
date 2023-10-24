<?php

namespace Modules\Rating\Domains\Actions;

use Illuminate\Support\Facades\DB;
use Modules\Rating\Domains\Rating;

class CreateRateAction
{

    public $userId;

    public $rate;

    public $comment;

    public $offerId;

    public $ratedUserId;

    public function __construct($userId, $rate, $comment, $offerId, $ratedUserId)
    {
        $this->userId       = $userId;
        $this->rate         = $rate;
        $this->comment      = $comment;
        $this->offerId      = $offerId;
        $this->ratedUserId  = $ratedUserId;
    }

    public function execute()
    {
        return Rating::create($this->userId, $this->rate, $this->comment, $this->ratedUserId, $this->offerId);
    }
}
