<?php

namespace Modules\Wallet\Http\Controllers;

use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Modules\Wallet\Domains\CryptoWallet;
use Modules\Wallet\Domains\Wallet;
use Modules\Wallet\Http\Requests\CryptoWalletStoreRequest;
use Modules\Wallet\Http\Transformers\CryptoWalletTransformer;

class CryptoWalletController
{
    public function store(CryptoWalletStoreRequest $request)
    {
        try {
            return response()->json(
                fractal($request->save(), new CryptoWalletTransformer()),
                Response::HTTP_CREATED
            );
        } catch (\Exception $exception) {
            Log::error($exception);
            return response()->json([
                'errors' => [
                    'message' => 'Add credit crypto wallet error!'
                ]
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    public function index()
    {
        try {
            $user  = auth()->user();
            $cryptoWallet = CryptoWallet::getByWalletId(Wallet::findByUserId($user->id)->id);

            return response()->json(
                fractal($cryptoWallet, new CryptoWalletTransformer()),
                Response::HTTP_OK
            );
        } catch (Exception $exception) {
            Log::error($exception);
            return response()->json([
                'errors' => [
                    'message' => 'Remove Crypto wallet Unsuccessful'
                ]
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    public function destroy(string $cryptoWalletId)
    {
        try {
            $cryptoWallet = CryptoWallet::find($cryptoWalletId);

            $cryptoWallet->delete();
            return response()->json([
                'data' => [
                    'message' => 'Remove Crypto wallet Successful'
                ]
            ], Response::HTTP_OK);
        } catch (Exception $exception) {
            Log::error($exception);
            return response()->json([
                'errors' => [
                    'message' => 'Remove Crypto wallet Unsuccessful'
                ]
            ], Response::HTTP_BAD_REQUEST);
        }
    }
}
