<?php

namespace Modules\Admin\Http\Requests\Package;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Package\Domains\Package;
use Modules\Package\Price\Price;

class UpdateRequest extends FormRequest
{
    public function rules()
    {
        return [
            'id'    => 'required',
            'point' => 'required|numeric',
            'price' => 'required|numeric'
        ];
    }

    public function save()
    {
        Package::update($this->id, $this->point, Price::stripePrice($this->price));
    }
}
