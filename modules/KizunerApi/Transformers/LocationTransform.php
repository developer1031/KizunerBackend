<?php

namespace Modules\KizunerApi\Transformers;

use League\Fractal\TransformerAbstract;
use Modules\Kizuner\Models\Location;

class LocationTransform extends TransformerAbstract
{
    public function transform(Location $location)
    {
        return [
            'id' => $location->id,
            'address' => ($location->address && $location->address!='.') ? $location->address : '',
            'lat' => $location->lat,
            'lng' => $location->lng,
            'short_address' => $location->short_address
        ];
    }
}
