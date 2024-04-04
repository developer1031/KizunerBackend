<?php

namespace Modules\Wallet\Services;

use Carbon\Carbon;
use Exception;
use Modules\Wallet\Domains\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Modules\Wallet\Domains\Card;
use Modules\Wallet\Domains\Entities\WalletEntity;
use Stripe\Account;
use Stripe\AccountLink;
use Stripe\Balance;
use Stripe\Stripe;

const IBAN_COUNTRY = [
  'BE',
  'BG',
  'CR',
  'CI',
  'HR',
  'CY',
  'CZ',
  'DK',
  'EE',
  'FI',
  'FR',
  'DE',
  'GI',
  'GR',
  'HU',
  'IS',
  'IE',
  'IL',
  'IT',
  'LV',
  'LI',
  'LT',
  'LU',
  'MT',
  'MC',
  'NL',
  'NE',
  'NO',
  'PL',
  'PT',
  'RO',
  'SN',
  'SK',
  'SI',
  'ES',
  'SE',
  'CH',
  'TN',
  'AE',
];
const BANK_NORMAL = ['BD', 'BO', 'ID', 'PY', 'TH', 'UY', 'VN'];
const BANK_BRANCH_CODES = ['BR', 'DO', 'JM', 'SG', 'LK', 'TT', 'UZ'];
const BANK_SWIFT_CODES = [
  'AL',
  'DZ',
  'AO',
  'AG',
  'AM',
  'BS',
  'BH',
  'BT',
  'BA',
  'BW',
  'BN',
  'EC',
  'EG',
  'SV',
  'ET',
  'GA',
  'GM',
  'GT',
  'GY',
  'JO',
  'KZ',
  'KE',
  'KW',
  'LA',
  'MO',
  'MG',
  'MU',
  'MD',
  'MN',
  'MA',
  'MZ',
  'NA',
  'NG',
  'MK',
  'OM',
  'PK',
  'PA',
  'PH',
  'QA',
  'RW',
  'LC',
  'SA',
  'SM',
  'RS',
  'ZA',
  'KR',
  'TW',
  'TZ',
  'TR',
];


class StripeManager
{
  public function getStripeCustomAccount()
  {
    $user = auth()->user();
    $wallet = Wallet::findByUserId($user->id);

    if (!$wallet->stripe_connect_id) {
      throw new Exception("This user has no PaymentHub account");
    }

    $account = Account::retrieve($wallet->stripe_connect_id);
    // \Log::debug($account);
    // $frontFileId = $account->individual->verification->document->front;
    // $frontFile = \Stripe\File::retrieve($frontFileId);
    // $frontUrl = "";

    // $backFileId = $account->individual->verification->document->back;
    // $backFile = \Stripe\File::retrieve($backFileId);
    // $backUrl = $backFile->url;

    return [
      'account' => $account,
    ];
  }
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

    $dob = Carbon::parse($request->get('dob'));
    $countryCode = $request->get('country_code');

    // \Log::debug($request->all());

    $accountParams = [
      'type' => 'custom',
      'country' => $countryCode,
      'business_type' => 'individual',
      'individual' => [
        'dob' => [
          'day' => $dob->day,
          'month' => $dob->month,
          'year' => $dob->year
        ],
        'email' => $user->email,
        'phone' => $request->get('phone'),
        'verification' => [
          'document' => [
            'front' => $request->get('identity_document'),
            'back' => $request->get('identity_document_back')
          ],
        ]
      ],
      'external_account' => [
        'object' => 'bank_account',
        'country' => $countryCode,
        'currency' => $request->get('currency'),
      ],
      'capabilities' => [
        // 'card_payments' => ['requested' => true],
        'transfers' => ['requested' => true],
      ],
      'tos_acceptance' => [
        'date' => Carbon::now()->timestamp,
        'ip' => request()->ip(),
        'service_agreement' => 'recipient',
      ],
      'settings' => [
        'payouts' => [
          'schedule' => [
            'interval' => 'manual'
          ]
        ]
      ],
      'business_profile' => [
        'url' => 'https://kizuner.com',
        'mcc' => '5817',
        'product_description' => 'Kizuner is a social media platform that allows users to share their thoughts and ideas with the world.',
      ],
    ];

    if ($countryCode == 'JP') {
      if ($request->get('account_number')) {
        $accountParams['external_account']['account_holder_name'] = $request->get('account_name');
        $accountParams['external_account']['account_number'] = $request->get('account_number');
        $accountParams['external_account']['routing_number'] = $request->get('bank_code') . $request->get('branch_code');
      } else {
        unset($accountParams['external_account']);
      }
    }
    if ($countryCode == 'US') {
      $accountParams['external_account']['account_number'] = $request->get('account_number');
      $accountParams['external_account']['routing_number'] = $request->get('routing_number');
      $accountParams['individual']['id_number'] = $request->get('id_number');
      // $accountParams['individual']['ssn_last_4'] = $request->get('ssn_last_4');
      $accountParams['company']['name'] = $request->get('first_name');
      $accountParams['company']['tax_id'] = $request->get('tax_id');
      $accountParams['company']['address']['postal_code'] = $request->get('postal_code');
      $accountParams['company']['address']['line1'] = $request->get('address_line1');
      $accountParams['company']['address']['line2'] = $request->get('address_line2');
      $accountParams['settings']['payments']['statement_descriptor'] = 'Kizuner';
      $accountParams['tos_acceptance']['service_agreement'] = 'full';
    }
    if ($countryCode == 'BR') {
      $accountParams['tos_acceptance']['service_agreement'] = 'full';
    }
    if ($countryCode == 'KR') {
      $accountParams['individual']['address']['state'] = $request->get('address_state');
      $accountParams['individual']['address']['city'] = $request->get('address_city');
    }
    if ($countryCode == 'GB') {
      if ($request->get('account_number')) {
        $accountParams['external_account']['account_number'] = $request->get('account_number');
        $accountParams['external_account']['routing_number'] = $request->get('sort_code');
      } else {
        unset($accountParams['external_account']);
      }
    }
    if ($countryCode == 'AU') {
      $accountParams['individual']['address']['state'] = $request->get('address_state');
      $accountParams['individual']['address']['city'] = $request->get('address_city');

      if ($request->get('account_number')) {
        $accountParams['external_account']['account_number'] = $request->get('account_number');
        $accountParams['external_account']['routing_number'] = $request->get('bsb');
      } else {
        unset($accountParams['external_account']);
      }
    }
    if ($countryCode == 'HK') {
      if ($request->get('account_number')) {
        $accountParams['external_account']['account_number'] = $request->get('account_number');
        $accountParams['external_account']['routing_number'] = $request->get('clearing_code') . '-' . $request->get('branch_code');
      } else {
        unset($accountParams['external_account']);
      }
    }
    if ($countryCode == 'CA') {
      if ($request->get('account_number')) {
        $accountParams['external_account']['account_number'] = $request->get('account_number');
        $accountParams['external_account']['routing_number'] = $request->get('transit_number') . '-' . $request->get('institution_number');
      } else {
        unset($accountParams['external_account']);
      }
    }
    if ($countryCode == 'IN') {
      if ($request->get('account_number')) {
        $accountParams['external_account']['account_number'] = $request->get('account_number');
        $accountParams['external_account']['routing_number'] = $request->get('ifsc_code');
      } else {
        unset($accountParams['external_account']);
      }
    }
    if ($countryCode == 'AR') {
      if ($request->get('cbu')) {
        $accountParams['external_account']['account_number'] = $request->get('cbu');
      } else {
        unset($accountParams['external_account']);
      }
    }
    if ($countryCode == 'MX') {
      if ($request->get('clabe')) {
        $accountParams['external_account']['account_number'] = $request->get('clabe');
      } else {
        unset($accountParams['external_account']);
      }
    }
    if ($countryCode == 'NZ') {
      if ($request->get('account_number')) {
        $accountParams['external_account']['account_number'] = $request->get('account_number');
      } else {
        unset($accountParams['external_account']);
      }
    }
    if ($countryCode == 'MY') {
      if ($request->get('account_number')) {
        $accountParams['external_account']['account_number'] = $request->get('account_number');
        $accountParams['external_account']['routing_number'] = $request->get('swift_code');
      } else {
        unset($accountParams['external_account']);
      }
    }
    if ($countryCode == 'GH') {
      if ($request->get('iban')) {
        $accountParams['external_account']['account_number'] = $request->get('iban');
        $accountParams['external_account']['routing_number'] = $request->get('sort_code');
      } else {
        unset($accountParams['external_account']);
      }
    }

    if (in_array($countryCode, IBAN_COUNTRY)) {
      if ($request->get('iban')) {
        $accountParams['external_account']['account_number'] = $request->get('iban');
      } else {
        unset($accountParams['external_account']);
      }
    }
    if (in_array($countryCode, BANK_NORMAL)) {
      if ($request->get('account_number')) {
        $accountParams['external_account']['account_number'] = $request->get('account_number');
        $accountParams['external_account']['routing_number'] = $request->get('bank_code');
      } else {
        unset($accountParams['external_account']);
      }
    }
    if (in_array($countryCode, BANK_BRANCH_CODES)) {
      if ($request->get('account_number')) {
        $accountParams['external_account']['account_number'] = $request->get('account_number');
        $accountParams['external_account']['routing_number'] = $request->get('bank_code') . '-' . $request->get('branch_code');
      } else {
        unset($accountParams['external_account']);
      }
    }
    if (in_array($countryCode, BANK_SWIFT_CODES)) {
      if ($request->get('iban')) {
        $accountParams['external_account']['account_number'] = $request->get('iban');
        $accountParams['external_account']['routing_number'] = $request->get('swift_code');
      } else {
        unset($accountParams['external_account']);
      }
    }

    if ($countryCode == 'JP') {
      $accountParams['individual']['first_name_kanji'] = $request->get('first_name');
      $accountParams['individual']['last_name_kanji'] = $request->get('last_name');
      $accountParams['individual']['first_name_kana'] = $request->get('first_name_kana');
      $accountParams['individual']['last_name_kana'] = $request->get('last_name_kana');
      $accountParams['individual']['address_kanji'] = [
        "postal_code" => $request->get('postal_code'),
        "country" => $countryCode,
        "line1" => $request->get('address_line1'),
        "line2" => $request->get('address_line2'),
      ];
      $accountParams['individual']['address_kana'] = [
        "postal_code" => $request->get('postal_code'),
        "country" => $countryCode,
        "line1" => $request->get('address_line1_kana'),
        "line2" => $request->get('address_line2_kana'),
      ];
    } else {
      $accountParams['individual']['first_name'] = $request->get('first_name');
      $accountParams['individual']['last_name'] = $request->get('last_name');
      $accountParams['individual']['address']['postal_code'] = $request->get('postal_code');
      $accountParams['individual']['address']['country'] = $countryCode;
      $accountParams['individual']['address']['line1'] = $request->get('address_line1');
      $accountParams['individual']['address']['line2'] = $request->get('address_line2');
    }

    if (!$request->identity_document) {
      unset($accountParams['individual']['verification']);
    }

    // \Log::debug($accountParams);

    if ($wallet->stripe_connect_id) {
      unset($accountParams['type']);
      unset($accountParams['country']);
      $account = Account::update($wallet->stripe_connect_id, $accountParams);
    } else {
      $account = Account::create($accountParams);

      $wallet->stripe_connect_id = $account->id;
      $wallet->payouts_enabled = false;
      $wallet->save();
    }

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

    // $currency = $balance->available[0]->currency;
    // $amount = $balance->available[0]->amount / ($currency == 'jpy' ? 1 : 1);

    $currency = $balance->available[0]->currency;
    $amount = $balance->available[0]->amount / 100;

    return [
      'amount' => $amount,
      'currency' => $currency,
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
      'amount' => ($amount + $amount * WalletEntity::STRIPE_GUEST_FEE) * 100,
      // 'application_fee_amount' => $amount * WalletEntity::STRIPE_FEE * 100,
      'currency' => 'usd',
      'customer' => $wallet->stripe_id,
      'payment_method' => $card->payment_method,
      'off_session' => true,
      'confirm' => true,
      'description' => $description
      // "on_behalf_of" => $destination,
    ]);
  }

  public function transfer(string $userId, float $amount, string $description)
  {
    $destination = Wallet::findByUserId($userId)->stripe_connect_id;

    return \Stripe\Transfer::create([
      'amount' => ($amount - $amount * WalletEntity::STRIPE_FEE) * 100,
      'currency' => 'usd',
      'destination' => $destination
    ]);
  }

  public function payout(float $amount, string $externalAccountId, string $currency)
  {
    $stripeConnectId = Wallet::findByUserId(auth()->user()->id)->stripe_connect_id;

    return \Stripe\Payout::create([
      'amount' => $amount,
      'currency' => $currency,
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

    // \Stripe\Transfer::create([
    //   'amount' => WalletEntity::STRIPE_PAYOUT_FEE,
    //   'currency' => 'usd',
    //   'destination' => 'default_for_currency', // platform account
    //   'from' => $wallet->stripe_connect_id,
    // ]);

    \Stripe\Payout::create([
      'amount' => $balance->available[0]->amount,
      'currency' => $balance->available[0]->currency
    ], [
      'stripe_account' => $wallet->stripe_connect_id,
    ]);

    return [
      'success' => true
    ];
  }

  public function update(Request $request)
  {
    $user = auth()->user();
    $wallet = Wallet::findByUserId($user->id);

    if (!$wallet->stripe_connect_id) {
      throw new Exception("This user has no PaymentHub account");
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
