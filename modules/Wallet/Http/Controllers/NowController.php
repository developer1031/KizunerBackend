<?php

namespace Modules\Wallet\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Modules\Wallet\Services\NowPaymentsAPI;
use Modules\Wallet\Services\NowManager;

class NowController
{
    private $nowPaymentsAPI;
    private $nowManager;

    const MIN_PRICE = 10;   // 10$

    public function __construct(NowPaymentsAPI $nowPaymentsAPI, NowManager $nowManager)
    {
        $this->nowPaymentsAPI = $nowPaymentsAPI;
        $this->nowManager = $nowManager;
    }

    /**
     * The function `nowPaymentsIpnCallback` handles the IPN callback from NowPayments API, logs the
     * payment type and request data, validates the IPN request, and calls the `ipn` method of the
     * `nowManager` class.
     * 
     * @param string paymentType The paymentType parameter is a string that represents the type of
     * payment being processed. It could be a value like "credit_card" or "bitcoin" depending on the
     * payment method used.
     * @param Request request The `` parameter is an instance of the `Request` class, which is
     * used to handle HTTP requests in Laravel. It contains information about the current request, such
     * as the request method, headers, and input data.
     * 
     * @return a JSON response with an empty array and a status code of 200 (OK) if the IPN request is
     * valid and processed successfully. If there is an exception or error, it returns a JSON response
     * with an error message and a status code of 500 (Internal Server Error).
     */
    public function nowPaymentsIpnCallback(string $paymentType, Request $request)
    {
        Log::debug("nowPaymentsIpnCallback start");
        Log::debug('paymentType: ' . $paymentType);
        Log::debug($request->all());


        try {
            if ($this->nowPaymentsAPI->checkIpnRequestIsValid()) {
                $this->nowManager->ipn($paymentType, $request);
            }

            Log::debug("nowPaymentsIpnCallback end");
            return response()->json([], Response::HTTP_OK);
        } catch (\Exception $th) {
            Log::error("nowPaymentsIpnCallback error:" . $th->getMessage());
            return response()->json([
                'errors' => [
                    'message' => $th->getMessage()
                ]
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * The function retrieves a list of currencies from the NowPayments API and returns it as a JSON
     * response.
     * 
     * @return a JSON response containing the currencies obtained from the NowPayments API.
     */
    public function getNowPaymentsCurrencies()
    {
        $currencies = $this->nowPaymentsAPI->getCurrencies();
        return response()->json($currencies, Response::HTTP_OK);
    }

    /**
     * The function retrieves the minimum payment amount in a specified currency and converts it to USD
     * if necessary.
     * 
     * @param Request request The `` parameter is an instance of the `Request` class, which is
     * used to retrieve data from the HTTP request. In this code snippet, it is used to get the values
     * of the `currency_from` and `currency_to` parameters from the request.
     * 
     * @return a JSON response. If the function executes successfully, it will return a JSON object
     * containing the minimum payment amount and its equivalent in USD. If there is an error during
     * execution, it will return a JSON object with an error message.
     */
    public function getNowPaymentsMinAmount(Request $request)
    {
        try {
            $minAmount = $this->nowPaymentsAPI->getMinimumPaymentAmount([
                'currency_from' => $request->get('currency_from'),
                'currency_to' => $request->get('currency_from')
            ]);

            $estimate = $this->nowPaymentsAPI->getEstimatePrice([
                'amount' => $minAmount->min_amount,
                'currency_from' => $request->get('currency_from'),
                'currency_to' => 'usd'
            ]);

            $minAmount->min_amount_usd = (float)$estimate->estimated_amount;

            if ($minAmount->min_amount_usd >= self::MIN_PRICE) {
                $minAmount->absolute_min_amount = $minAmount->min_amount;
                $minAmount->absolute_min_amount_usd = $minAmount->min_amount_usd;
            } else {
                $minAmount->absolute_min_amount_usd = self::MIN_PRICE;

                $absoluteEstimate = $this->nowPaymentsAPI->getEstimatePrice([
                    'amount' => $minAmount->absolute_min_amount_usd,
                    'currency_from' => 'usd',
                    'currency_to' => $request->get('currency_from')
                ]);

                $minAmount->absolute_min_amount = (float)$absoluteEstimate->estimated_amount;
            }

            return response()->json($minAmount, Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json([
                'errors' => [
                    'message' => $e->getMessage()
                ]
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * The function `getNowPaymentsEstimate` retrieves an estimate of the price for a given amount and
     * currency conversion using the NowPayments API in PHP.
     * 
     * @param Request request The `` parameter is an instance of the `Request` class, which is
     * used to retrieve data from the HTTP request. It is typically used to access data sent through
     * the request's query parameters, form data, or JSON payload.
     * 
     * @return a JSON response. If the API call is successful, it returns the estimate object as a JSON
     * response with a status code of 200 (HTTP_OK). If there is an exception or error, it returns a
     * JSON response with an error message and a status code of 500 (HTTP_INTERNAL_SERVER_ERROR).
     */
    public function getNowPaymentsEstimate(Request $request)
    {
        try {
            $estimate = $this->nowPaymentsAPI->getEstimatePrice([
                'amount' => $request->get('amount'),
                'currency_from' => $request->get('currency_from'),
                'currency_to' => $request->get('currency_to')
            ]);

            $estimate->estimated_amount = (float)$estimate->estimated_amount;

            return response()->json($estimate, Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json([
                'errors' => [
                    'message' => $e->getMessage()
                ]
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
