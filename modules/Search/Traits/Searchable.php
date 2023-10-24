<?php

namespace Modules\Search\Traits;

trait Searchable
{
    private $query;
    private $perPage;
    private $category;
    private $offerType;
    private $paymentMethod;
    private $location;
    private $amount;

    public function __construct($query, string $perPage, $category=null, $offerType=null, $paymentMethod=null, $location=null, $amount=null)
    {
        $this->perPage = $perPage;
        $this->query   = $query;
        $this->category   = $category;
        $this->offerType   = $offerType;
        $this->paymentMethod   = $paymentMethod;
        $this->location   = $location;
        $this->amount   = $amount;
    }
}
