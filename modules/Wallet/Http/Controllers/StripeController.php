<?php

namespace Modules\Wallet\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Modules\Wallet\Domains\Wallet;
use Modules\Wallet\Services\StripeManager;
use Stripe\File;

class StripeController
{
    private $stripeManager;

    public function __construct(StripeManager $stripeManager)
    {
        $this->stripeManager = $stripeManager;
    }

    /**
     * The function creates a Stripe Connect account and returns a JSON response with the result.
     * 
     * @param Request request The  parameter is an instance of the Request class, which is used
     * to retrieve data from the HTTP request made to the server. It contains information such as the
     * request method, headers, and any data sent in the request body. In this case, it is being passed
     * to the createStripeConnect
     * 
     * @return a JsonResponse object with the response data and a HTTP status code of 201
     * (HTTP_CREATED) if the try block is successful. If an exception is caught, it returns a JSON
     * response with an error message and a HTTP status code of 500 (HTTP_INTERNAL_SERVER_ERROR).
     */
    public function createStripeConnect(Request $request)
    {
        try {
            $res = $this->stripeManager->createStripeConnect($request);
            return new JsonResponse($res, Response::HTTP_CREATED);
        } catch (\Exception $th) {
            Log::error($th->getMessage());
            return response()->json([
                'errors' => [
                    'message' => $th->getMessage()
                ]
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    public function getStripeCustomAccount() {
        try {
            $res = $this->stripeManager->getStripeCustomAccount();
            return new JsonResponse($res, Response::HTTP_OK);
        } catch (\Exception $th) {
            Log::debug($th->getMessage());
            return response()->json([
                'errors' => [
                    'message' => $th->getMessage()
                ]
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * The function `uploadIdentityDocument` handles the uploading of an identity document file and
     * returns a JSON response with the created file data or an error message.
     * 
     * @param Request request The  parameter is an instance of the Request class, which
     * represents an HTTP request. It contains information about the request such as the request
     * method, headers, and any data sent with the request.
     * 
     * @return a JSON response with the created file object and a status code of 201 (HTTP_CREATED) if
     * the file is successfully created. If an exception occurs, it will return a JSON response with an
     * error message and a status code of 500 (HTTP_INTERNAL_SERVER_ERROR).
     */
    public function uploadIdentityDocument(Request $request)
    {
        try {
            $file = File::create([
                'file' => fopen($request->file('file'), 'r'),
                'purpose' => 'identity_document'
            ]);
            return new JsonResponse($file, Response::HTTP_CREATED);
        } catch (\Exception $th) {
            dd($th);
            Log::error($th->getMessage());
            return response()->json([
                'errors' => [
                    'message' => $th->getMessage()
                ]
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * The function `stripeWebhook` handles incoming webhook events from Stripe and updates the
     * `payouts_enabled` field of a wallet based on the `account.updated` event.
     * 
     * @return a JSON response with a status code of 200 (OK) if the webhook is processed successfully.
     * If there is an error, it will return a JSON response with an appropriate error message and a
     * status code of 400 (Bad Request) or 500 (Internal Server Error).
     */
    public function stripeWebhook()
    {
        Log::info('[Stripe Webhook]: Start');
        $endpoint_secret = config('services.stripe.stripe_webhook_secret');

        $payload = @file_get_contents('php://input');
        $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];
        $event = null;

        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload,
                $sig_header,
                $endpoint_secret
            );
        } catch (\UnexpectedValueException $e) {
            // Invalid payload
            Log::error('[Stripe Webhook]: ' . $e->getMessage());
            return response()->json("Invalid payload", Response::HTTP_BAD_REQUEST);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            // Invalid signature
            Log::error('[Stripe Webhook]: ' . $e->getMessage());
            return response()->json("Invalid signature", Response::HTTP_BAD_REQUEST);
        }

        try {
            // Handle the event
            switch ($event->type) {
                case 'account.updated':
                    Log::info($event->data->object);
                    $account = $event->data->object;
                    $wallet = Wallet::findByStripeConnectId($account->id);

                    $wallet->payouts_enabled = $account->payouts_enabled;
                    $wallet->save();
                    break;
                default:
                    Log::error('[Stripe Webhook]: Received unknown event type ' . $event->type);
                    break;
            }
        } catch (\Throwable $th) {
            Log::error('[Stripe Webhook]: ' . $th->getMessage());
            return response()->json("Internal server error", Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        Log::info('[Stripe Webhook]: Success');
        return response()->json(null, Response::HTTP_OK);
    }

    public function status(Request $request)
    {
        try {
            $res = $this->stripeManager->getStatus($request);
            return new JsonResponse($res, Response::HTTP_OK);
        } catch (\Exception $th) {
            Log::error($th->getMessage());
            return response()->json([
                'errors' => [
                    'message' => $th->getMessage()
                ]
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function paymentInfo(Request $request)
    {
        try {
            $res = $this->stripeManager->getPaymentInfo($request);
            return new JsonResponse($res, Response::HTTP_OK);
        } catch (\Exception $th) {
            Log::error($th->getMessage());
            return response()->json([
                'errors' => [
                    'message' => $th->getMessage()
                ]
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function withdraw()
    {
        try {
            $res = $this->stripeManager->withdraw();
            return new JsonResponse($res, Response::HTTP_OK);
        } catch (\Exception $th) {
            Log::error($th->getMessage());
            return response()->json([
                'errors' => [
                    'message' => $th->getMessage()
                ]
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function payout(Request $request) {
        try {
            $res = $this->stripeManager->payout(
              $request->get('amount'),
              $request->get('externalAccountId'),
              $request->get('currency')
            );

            return new JsonResponse($res, Response::HTTP_OK);
        } catch (\Exception $th) {
            Log::error($th->getMessage());
            return response()->json([
                'errors' => [
                    'message' => $th->getMessage()
                ]
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(Request $request)
    {
        try {
            $res = $this->stripeManager->update($request);
            return new JsonResponse($res, Response::HTTP_OK);
        } catch (\Exception $th) {
            Log::error($th->getMessage());
            return response()->json([
                'errors' => [
                    'message' => $th->getMessage()
                ]
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function return_url(Request $request)
    {
        Log::debug("______RETURN");
        Log::debug($request->all());
    }

    public function refresh_url(Request $request)
    {
        Log::debug("______REFRSH");
        Log::debug($request->all());
    }
}
