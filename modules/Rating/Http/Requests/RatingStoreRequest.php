<?php

namespace Modules\Rating\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Notification\Job\NewReviewJob;
use Modules\Rating\Domains\Actions\CreateRateAction;
use Modules\Rating\Domains\Queries\RatingQuery;

class RatingStoreRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'offer_id'   => 'required',
            'user_id'    => 'required',
            'rate'       => 'required',
            'comment'    => 'required'
        ];
    }

    public function save()
    {
        if ($this->validated()) {
            $rate = (new CreateRateAction(auth()->user()->id, $this->rate, $this->comment, $this->offer_id, $this->user_id))->execute();
            info(json_encode($rate));
            NewReviewJob::dispatch($rate);
            return  (new RatingQuery($rate->id))->execute();
        }
    }
}
