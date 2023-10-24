<?php

namespace Modules\Offer\Domains\Queries;

class HangoutOfferQueries
{
    private $hangoutId;

    private $offerStatus;

    private $perPage;

    public function __construct($hangoutId, $offerStatus, $perPage)
    {
        $this->hangoutId = $hangoutId;
        $this->offerStatus = $offerStatus;
        $this->perPage = $perPage;
    }

    public function execute()
    {

    }
}
