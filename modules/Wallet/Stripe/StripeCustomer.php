<?php

namespace Modules\Wallet\Stripe;

use Stripe\Customer;

class StripeCustomer
{
    /**
     * Create a new Stripe Customer
     * @param string $email
     * @param string $name
     * @return Customer
     * @throws \Stripe\Exception\ApiErrorException
     */
    public static function create(string $email, string $name)
    {
        return Customer::create([
            'email' => $email,
            'name'  => $name
        ]);
    }
}
