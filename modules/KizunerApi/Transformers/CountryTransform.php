<?php

namespace Modules\KizunerApi\Transformers;

use League\Fractal\TransformerAbstract;
use Modules\Kizuner\Models\Country;

class CountryTransform extends TransformerAbstract
{
    public function transform(Country $country)
    {
        return [
            'id'        => $country->id,
            'country'   => $country->country,
            'city'      => $country->city,
            'latitude'  => $country->latitude,
            'longitude' => $country->longitude,
            'altitude'  => $country->altitude,
        ];
    }
}
