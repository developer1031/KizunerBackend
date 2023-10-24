<?php

namespace Modules\Wallet\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Wallet\Domains\Actions\CreateTransactionAction;
use Modules\Wallet\Exceptions\NotEnoughPointException;

class TransactionStoreRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'user_id' => 'required',
            'amount'  => 'required'
        ];
    }

    public function save()
    {
        $currentUser = auth()->user();
        $user        = $this->user_id;

        try {
            (new CreateTransactionAction($currentUser->id, $user, $this->amount))->execute();
            return [
                'data' => [
                    'status' => true
                ]
            ];
        } catch (NotEnoughPointException $exception) {
            throw  $exception;
        }
    }
}
