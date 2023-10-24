<?php


namespace Modules\Search\Domain\Actions;


use Modules\Search\Domain\Queries\HangoutNearByQuery;
use Modules\Search\Domain\Queries\HelpNearByQuery;
use Modules\Search\Http\Transformers\HangoutNearByTransform;
use Modules\Search\Http\Transformers\HelpNearByTransform;
use Modules\Search\Http\Transformers\NearByTransform;

class HangoutNearByAction
{
    public function execute($lat, $long, $radius, $perPage)
    {
        $helps = (new HelpNearByQuery($lat, $long, $radius, $perPage))->execute();
        $hangouts = (new HangoutNearByQuery($lat, $long, $radius, $perPage))->execute();

        $dataMerge = $helps->merge($hangouts);

        $sortedData = $dataMerge->sortBy('distance');


        //$helps = fractal((new HelpNearByQuery($lat, $long, $radius, $perPage))->execute(), new HelpNearByTransform());
        //$hangouts = fractal((new HangoutNearByQuery($lat, $long, $radius, $perPage))->execute(), new HangoutNearByTransform());
        //dd($sortedData);

        $helps = fractal(null, new NearByTransform());
        $hangouts = fractal($sortedData, new NearByTransform());

        return [
            'data' => [
                'helps' => $helps,
                'hangouts' => $hangouts
            ]
        ];
        //return (new HangoutNearByQuery($lat, $long, $radius, $perPage))->execute();
    }
}
