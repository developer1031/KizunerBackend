<?php

namespace Modules\Wallet\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Wallet\Domains\Actions\CreateCardAction;
use Modules\Wallet\Domains\Dto\CardDto;
use Modules\Wallet\Domains\Wallet;
use Stripe\Exception\ApiErrorException;
use Stripe\PaymentMethod;

class CardStoreRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'payment_method' => 'required'
        ];
    }

    /**
     * @throws ApiErrorException
     */
    public function save()
    {
        if ($this->validated()) {
          try {
              $paymentMethod  = $this->payment_method;

              $wallet = Wallet::findByUserId(auth()->user()->id);
              $payment       = PaymentMethod::retrieve($paymentMethod);
              $cardBrand     = $payment->card->brand;
              $cardLastFour  = $payment->card->last4;
              $name          = $payment->billing_details->name;
              $cardDto       = new CardDto($name, $wallet->id, $paymentMethod, $cardBrand, $cardLastFour);

              // Save Card Token to Database
              return (new CreateCardAction($cardDto))->execute();
          } catch (ApiErrorException $exception) {
              throw $exception;
          }
        }
    }
}
