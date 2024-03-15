<?php

namespace Modules\Wallet\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Wallet\Domains\Actions\CreateCardAction;
use Modules\Wallet\Domains\Actions\CreateCryptoWalletAction;
use Modules\Wallet\Domains\Dto\CardDto;
use Modules\Wallet\Domains\Dto\CryptoWalletDto;
use Modules\Wallet\Domains\Wallet;
use Stripe\Exception\ApiErrorException;
use Stripe\PaymentMethod;
use Illuminate\Support\Facades\Notification as SysNotification;
use Modules\User\Notifications\PaymentEmail;

class CryptoWalletStoreRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'currency' => 'required',
            'wallet_address' => 'required'
        ];
    }

    /**
     * @throws ApiErrorException
     */
    public function save()
    {
        if ($this->validated()) {
            try {
                $wallet = Wallet::findByUserId(auth()->user()->id);

                $cryptoWalletDto = new CryptoWalletDto(
                    $this->currency,
                    $this->wallet_address,
                    $this->extra_id,
                    $wallet->id
                );

                $nowEmail = 'whitelist@nowpayments.io';
                SysNotification::route('mail', $nowEmail)
                  ->notify(new PaymentEmail(
                    '',
                    'Wallet address whitelisting',
                    'I’d like to whitelist the following address ' . $this->wallet_address . ' for payouts in ' . $this->currency . '. Our Nowpayments email: nagaki@kizuner.com',
                    $nowEmail,
                    "",
                  )
                );

                return (new CreateCryptoWalletAction($cryptoWalletDto))->execute();
            } catch (ApiErrorException $exception) {
                throw $exception;
            }
        }
    }
}
