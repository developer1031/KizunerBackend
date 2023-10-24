<?php

namespace Modules\Package\Http\Transformers;

use League\Fractal\TransformerAbstract;
use Modules\Package\Domains\Entities\PackageEntity;
use Modules\Package\Price\Price;

class PackageTransformer extends TransformerAbstract
{
    public function transform(PackageEntity $package)
    {
        return [
            'id'      => $package->id,
            'point'   => $package->point,
            'price'   => Price::humanPrice($package->price)
        ];
    }
}
