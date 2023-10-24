<?php

namespace Modules\Wallet\Services;

use Carbon\Carbon;
use Exception;
use Modules\Wallet\Domains\Wallet;
use Illuminate\Http\Request;
use Modules\Wallet\Domains\Card;
use Modules\Wallet\Domains\Entities\WalletEntity;
use Stripe\Account;
use Stripe\Balance;

class StripeManager
{
    /**
     * The function creates a Stripe Connect account for a user and saves the account ID in the user's
     * wallet.
     * 
     * @param Request request The `` parameter is an instance of the `Request` class, which is
     * used to retrieve data from the HTTP request made to the server. It contains information such as
     * form inputs, query parameters, and request headers.
     * 
     * @return the created Stripe account object.
     */
    public function createStripeConnect(Request $request)
    {
        $user = auth()->user();
        $wallet = Wallet::findByUserId($user->id);

        if ($wallet->stripe_connect_id) {
            throw new Exception("You already have a stripe connect account");
        }

        $dob = Carbon::parse($request->get('dob'));

        $account = \Stripe\Account::create([
            'type' => 'custom',
            'country' => 'US',
            'business_type' => 'individual',
            'individual' => [
                'dob' => [
                    'day' => $dob->day,
                    'month' => $dob->month,
                    'year' => $dob->year
                ],
                'email' => $user->email,
                'first_name' => $user->name,
                'last_name' => $user->email,
                'phone' => $request->get('phone'),
                'id_number' => $request->get('id_number'),
                "address" => [ // TODO kizuner's address
                    "city" => "Ho Chi Minh",
                    "country" => "US",
                    "line1" => "Dang Van Ngu",
                    "line2" => "186",
                    "postal_code" => "99950",
                    "state" => "AL"
                ],
                'verification' => [
                    'document' => [
                        'front' => $request->get('identity_document'),
                        'back' => $request->get('identity_document_back')
                    ],
                ]
            ],
            'external_account' => [
                'object' => 'bank_account',
                'country' => 'US',
                'routing_number' => $request->get('routing_number'),
                'account_number' => $request->get('account_number')
            ],
            'capabilities' => [
                'card_payments' => ['requested' => true],
                'transfers' => ['requested' => true],
            ],
            'tos_acceptance' => [
                'date' => Carbon::now()->timestamp,
                'ip' => request()->ip(),
            ],
            'settings' => [
                'payouts' => [
                    'schedule' => [
                        'interval' => 'manual'
                    ]
                ]
            ],
            'business_profile' => [
                'url' => config('app.url'),
                'mcc' => '7623'
            ],
        ]);

        $wallet->stripe_connect_id = $account->id;
        $wallet->payouts_enabled = false;
        $wallet->save();

        return $account;
    }

    public function getStatus()
    {
        $user = auth()->user();
        $wallet = Wallet::findByUserId($user->id);

        if (!$wallet->stripe_connect_id) {
            return [
                'amount' => 0,
                'status' => 'NOT_CONNECTED'
            ];
        }

        $account = Account::retrieve($wallet->stripe_connect_id);

        $wallet->payouts_enabled = $account->payouts_enabled;
        $wallet->save();

        if (!$wallet->payouts_enabled) {
            return [
                'amount' => 0,
                'status' => 'PENDING'
            ];
        }

        $balance = Balance::retrieve([
            'stripe_account' => $wallet->stripe_connect_id,
        ]);

        return [
            'amount' => $balance->available[0]->amount / 100,
            'status' => 'CONNECTED'
        ];
    }

    public function getPaymentInfo()
    {
        $user = auth()->user();
        $wallet = Wallet::findByUserId($user->id);

        if (!$wallet->stripe_connect_id) {
            return null;
        }

        $account = Account::retrieve($wallet->stripe_connect_id);
        $dob = Carbon::create(
            $account->individual->dob->year,
            $account->individual->dob->month,
            $account->individual->dob->day
        );

        $bank = $account->external_accounts->data[0];

        return [
            'country' => $account->country,
            'phone' => $account->individual->phone,
            'dob' => $dob,
            'id_number' => $account->individual->id_number_provided,
            'identity_document' => $account->individual->verification->document->details,
            'routing_number' => $bank ? $bank->routing_number : '',
            'account_number' => $bank ? $bank->last4 : '',
        ];
    }

    public function payment(string $cardId, float $amount, string $description)
    {
        $wallet = Wallet::findByUserId(auth()->user()->id);
        $card = Card::find($cardId);

        return \Stripe\PaymentIntent::create([
            'amount' => $amount * 100,
            'currency' => 'usd',
            'customer' => $wallet->stripe_id,
            'payment_method' => $card->payment_method,
            'off_session' => true,
            'confirm' => true,
            'description' => $description
        ]);
    }

    public function transfer(string $userId, float $amount, string $description)
    {
        $wallet = Wallet::findByUserId($userId);

        return \Stripe\Transfer::create([
            'amount' => ($amount - $amount * WalletEntity::STRIPE_FEE) * 100,
            'currency' => 'usd',
            'destination' => $description
        ]);
    }

    public function refund(string $stripeIntentId, float $amount = 0)
    {
        $params = [
            'payment_intent' => $stripeIntentId,
        ];

        if ($amount > 0) {
            $params['amount'] = $amount * 100;
        }

        return \Stripe\Refund::create($params);
    }

    public function withdraw()
    {
        $user = auth()->user();
        $wallet = Wallet::findByUserId($user->id);

        $balance = Balance::retrieve([
            'stripe_account' => $wallet->stripe_connect_id,
        ]);

        if ($balance->available[0]->amount <= 0) {
            throw new Exception("Insufficient balance");
        }

        return \Stripe\Payout::create([
            'amount' => $balance->available[0]->amount,
            'currency' => 'USD'
        ], [
            'stripe_account' => $wallet->stripe_connect_id,
        ]);
    }

    public function update(Request $request)
    {
        $user = auth()->user();
        $wallet = Wallet::findByUserId($user->id);

        if ($wallet->stripe_connect_id) {
            throw new Exception("This user has no stripe connect account");
        }

        return \Stripe\Account::update(
            $wallet->stripe_connect_id,
            [
                'external_account' => [
                    'object' => 'bank_account',
                    'country' => 'US',
                    'routing_number' => $request->get('routing_number'),
                    'account_number' => $request->get('account_number')
                ],
            ]
        );
    }
}
