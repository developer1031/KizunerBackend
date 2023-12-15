<?php

namespace Modules\Wallet\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Modules\Helps\Models\Help;
use Modules\Helps\Models\HelpOffer;
use Modules\Kizuner\Models\Offer;
use Modules\Wallet\Domains\Dto\HistoryDto;
use Modules\Wallet\Domains\Entities\CryptoWalletEntity;
use Modules\Wallet\Domains\Entities\HistoryEntity;
use Modules\Wallet\Domains\Entities\WalletEntity;
use Modules\Wallet\Domains\History;

class NowManager
{
    private $nowPaymentsApi;
    private $isSandbox;

    const FINISHED = 'finished';

    public function __construct(NowPaymentsAPI $nowPaymentsApi)
    {
        $this->nowPaymentsApi = $nowPaymentsApi;
        $this->isSandbox = config('services.now_payments.now_payments_sandbox') == 'true';
    }

    /**
     * The function returns the value of the isSandbox property.
     * 
     * @return The value of the variable ->isSandbox.
     */
    public function getIsSandbox()
    {
        return $this->isSandbox;
    }

    /**
     * The function creates an invoice for a payment using the Now Payments API.
     * 
     * @param float amount The amount parameter is a float value representing the payment amount. It
     * specifies the total amount to be paid by the customer.
     * @param string currency The "currency" parameter is a string that represents the currency in
     * which the payment will be made. It can be any valid currency code, such as "USD" for US dollars,
     * "EUR" for euros, "GBP" for British pounds, etc.
     * 
     * @return the result of the `createInvoice` method call on the `->nowPaymentsApi` object.
     */
    public function payment(float $amount, string $currency)
    {
        return $this->nowPaymentsApi->createInvoice([
            'price_amount' => $amount,
            'price_currency' => 'usd',
            'pay_currency' => $currency,
            'ipn_callback_url' => config('app.url') . '/api/wallets/now-payments/ipn/payment'
        ]);
    }

    public function transfer(float $amount, string $cryptoWalletId)
    {
        // if ($this->isSandbox) {
        //     return (object)[
        //         "id" => date("YmdHis"),
        //     ];
        // }
        Log::debug("transfer: " . $amount . " " . $cryptoWalletId);

        $wallet = CryptoWalletEntity::where('id', $cryptoWalletId)->first();

        return $this->nowPaymentsApi->createPayout([
            'withdrawals' => [
                [
                    'address' => $wallet->wallet_address,
                    'currency' => $wallet->currency,
                    'extra_id' => $wallet->extra_id,
                    'amount' => 0,
                    'fiat_amount' => $amount - $amount * WalletEntity::NOW_PAYMENTS_FEE,
                    'fiat_currency' => 'usd',
                    'ipn_callback_url' => config('app.url') . '/api/wallets/now-payments/ipn/transfer',
                ]
            ]
        ]);
    }

    public function refund(float $amount, string $cryptoWalletId)
    {
        if ($this->isSandbox) {
            return (object)[
                "id" => date("YmdHis"),
            ];
        }

        $wallet = CryptoWalletEntity::where('id', $cryptoWalletId)->first();

        return $this->nowPaymentsApi->createPayout([
            'withdrawals' => [
                [
                    'address' => $wallet->wallet_address,
                    'currency' => $wallet->currency,
                    'extra_id' => $wallet->extra_id,
                    'amount' => 0,
                    'fiat_amount' => $amount,
                    'fiat_currency' => 'usd',
                    'ipn_callback_url' => config('app.url') . '/api/wallets/now-payments/ipn/refund',
                ]
            ]
        ]);
    }

    /**
     * The function handles different payment types (payment, transfer, refund) and updates the payment
     * status accordingly.
     * 
     * @param string paymentType The paymentType parameter is a string that represents the type of
     * payment being processed. It can have one of three values: "PAYMENT", "TRANSFER", or "REFUND".
     * @param Request request The `` parameter is an instance of the `Request` class, which is
     * typically used in Laravel applications to handle HTTP requests. It contains information about
     * the current request, such as the request method, headers, and input data. In this case, it is
     * used to retrieve data from the incoming
     */
    public function ipn(string $paymentType, Request $request)
    {
        switch ($paymentType) {
            case WalletEntity::PAYMENT:
                if (strtolower($request->get('payment_status')) == self::FINISHED) {
                    HelpOffer::where('now_payments_id', $request->get('invoice_id'))
                        ->update([
                            'payment_status' => HelpOffer::PAYMENT_STATUS_PAID,
                            'status' => HelpOffer::$status['paid']
                        ]);
                    Offer::where('now_payments_id', $request->get('invoice_id'))
                        ->update([
                            'payment_status' => Offer::PAYMENT_STATUS_PAID,
                            'status' => Offer::$status['paid']
                        ]);
                }
                break;
            case WalletEntity::TRANSFER:
                if (strtolower($request->get('status')) == self::FINISHED) {
                    HelpOffer::where('now_payments_transfer_id', $request->get('batch_withdrawal_id'))
                        ->update([
                            'payment_status' => HelpOffer::PAYMENT_STATUS_TRANSFERRED,
                            'status' => HelpOffer::$status['approved']
                        ]);
                    Offer::where('now_payments_transfer_id', $request->get('batch_withdrawal_id'))
                        ->update([
                            'payment_status' => Offer::PAYMENT_STATUS_TRANSFERRED,
                            'status' => Offer::$status['approved']
                        ]);
                }
                break;
            case WalletEntity::REFUND:
                if (strtolower($request->get('status')) == self::FINISHED) {
                    HelpOffer::where('now_payments_refund_id', $request->get('batch_withdrawal_id'))
                        ->update([
                            'payment_status' => HelpOffer::PAYMENT_STATUS_REFUNDED
                        ]);
                    Offer::where('now_payments_refund_id', $request->get('batch_withdrawal_id'))
                        ->update([
                            'payment_status' => Offer::PAYMENT_STATUS_REFUNDED
                        ]);
                }
                break;
            default:
                Log::debug("Payment type not found: " . $paymentType);
                break;
        }
    }
}
