<?php

namespace Modules\Kizuner\Repositories;

use Modules\Kizuner\Contracts\LocationRepositoryInterface;
use Modules\Kizuner\Models\Location;

class LocationRepository implements LocationRepositoryInterface
{

    public function create(array $data): Location
    {
        $location = new Location($data);
        $location->save();
        return $location;
    }
}
