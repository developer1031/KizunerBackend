<?php

namespace Modules\Package\Price;

class Price
{
    public static function stripePrice(float $price)
    {
        return $price * 1000;
    }

    public static function humanPrice(float  $price)
    {
        return $price / 1000;
    }
}
