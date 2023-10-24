<?php

namespace Modules\Admin\Http\Requests\Package;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Package\Domains\Package;
use Modules\Package\Price\Price;

class StoreRequest extends FormRequest
{
    public function rules()
    {
        return [
            'point' => 'required|numeric|unique:package_packages,point',
            'price' => 'required|numeric'
        ];
    }

    public function save()
    {
        $point = $this->point;
        $price = Price::stripePrice($this->price);
        Package::create($point, $price);
    }
}
