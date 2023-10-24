<?php

namespace Modules\Rating\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Rating\Domains\Queries\RatingQuery;
use Modules\Rating\Domains\Rating;

class RatingUpdateRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'rate'          => 'required',
            'comment'       => 'required'
        ];
    }

    public function save($id)
    {
        $rate = Rating::update($id, $this->rate, $this->comment);
        return (new RatingQuery($rate->id))->execute();
    }
}
